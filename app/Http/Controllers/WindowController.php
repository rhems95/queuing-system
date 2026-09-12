<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\QueueCall;
use App\Models\Service;
use App\Models\Window;
use App\Services\FairQueueScheduler;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class WindowController extends Controller
{
    public function __construct(
        private QueueService $queueService,
        private FairQueueScheduler $scheduler,
    ) {
    }

    /**
     * Current and next queue for the staff's window (for polling).
     */
    private function getWindowState(int $windowId): array
    {
        $today = Carbon::today()->toDateString();

        $currentCall = $this->queueService->latestCallForWindow($windowId, $today);
        $currentQueue = $this->queueService->servingQueueFromCall($currentCall);

        $currentQueueNumber = $currentQueue?->queue_number;
        $servingStartedAt = $currentQueue && $currentCall
            ? Carbon::parse($currentCall->called_time)->toIso8601String()
            : null;

        $window = Window::find($windowId);
        $windowServiceId = $window ? (int) $window->service_id : null;

        $nextQueue = $windowServiceId
            ? $this->scheduler->peekNextWaiting($windowServiceId, $today)
            : null;

        $waitingList = $windowServiceId
            ? $this->scheduler->orderedWaiting($windowServiceId, $today, 10)
            : collect();

        $heldList = $windowServiceId
            ? $this->heldTickets($windowServiceId, $today)
            : collect();

        return [
            'current' => $currentQueueNumber,
            'current_name' => $this->queueService->servingLabelForQueue($currentQueue),
            'next' => $nextQueue ? $nextQueue->queue_number : null,
            'serving_started_at' => $servingStartedAt,
            'waiting_list' => $waitingList->map(fn ($q) => [
                'queue_number' => $q->queue_number,
                'priority' => $q->priority ? 'Priority' : 'Regular',
            ])->values()->all(),
            'held_list' => $heldList->map(fn ($q) => [
                'id' => $q->id,
                'queue_number' => $q->queue_number,
                'priority' => $q->priority ? 'Priority' : 'Regular',
                'student_name' => $this->queueService->servingLabelForQueue($q),
            ])->values()->all(),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $windowId = $user->window_id;

        if (! $windowId) {
            abort(403, 'No window assigned to this user.');
        }

        $today = Carbon::today()->toDateString();

        $window = Window::with('service')->findOrFail($windowId);

        $currentCall = $this->queueService->latestCallForWindow($windowId, $today);

        $currentQueue = $this->queueService->servingQueueFromCall($currentCall);
        $servingStartedAt = $currentQueue && $currentCall
            ? Carbon::parse($currentCall->called_time)->toIso8601String()
            : null;

        $windowServiceId = (int) $window->service_id;
        $nextQueue = $this->scheduler->peekNextWaiting($windowServiceId, $today);
        $waitingTickets = $this->scheduler->orderedWaiting($windowServiceId, $today, 10);
        $heldTickets = $this->heldTickets($windowServiceId, $today);

        return view('staff.window', [
            'currentQueue' => $currentQueue,
            'currentStudentName' => $this->queueService->servingLabelForQueue($currentQueue),
            'nextQueue' => $nextQueue,
            'window' => $window,
            'waitingTickets' => $waitingTickets,
            'heldTickets' => $heldTickets,
            'servingStartedAt' => $servingStartedAt,
        ]);
    }

    /**
     * Compact always-on-top staff controls (system float window).
     */
    public function floatPanel()
    {
        $user = Auth::user();
        $windowId = $user->window_id;

        if (! $windowId) {
            abort(403, 'No window assigned to this user.');
        }

        $today = Carbon::today()->toDateString();
        $window = Window::with('service')->findOrFail($windowId);

        $currentCall = $this->queueService->latestCallForWindow($windowId, $today);

        $currentQueue = $this->queueService->servingQueueFromCall($currentCall);
        $servingStartedAt = $currentQueue && $currentCall
            ? Carbon::parse($currentCall->called_time)->toIso8601String()
            : null;

        $nextQueue = $this->scheduler->peekNextWaiting((int) $window->service_id, $today);

        return view('staff.float', [
            'currentQueue' => $currentQueue,
            'currentStudentName' => $this->queueService->servingLabelForQueue($currentQueue),
            'nextQueue' => $nextQueue,
            'window' => $window,
            'servingStartedAt' => $servingStartedAt,
        ]);
    }

    /**
     * Open / pin the staff float on the connecting PC.
     * The .bat is only started when this request is from the same Windows machine as Apache.
     */
    public function launchFloat(Request $request)
    {
        $floatUrl = $this->staffFloatUrl($request);
        $local = $this->isLocalRequest($request);
        $pinnedOnServer = false;

        if ($local && strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $ps1 = base_path('tools/staff-float/Start-StaffFloat.ps1');
            if (is_file($ps1)) {
                $this->startHiddenWindowsProcess(
                    'powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File '
                    .escapeshellarg($ps1)
                    .' -Browser chrome -Url '
                    .escapeshellarg($floatUrl)
                );
                $pinnedOnServer = true;
            }
        }

        $message = $pinnedOnServer
            ? 'System float opened from this PC (always on top, no address bar).'
            : 'Open System Float runs bats\\start-staff-float.bat on this PC. If nothing opened, run bats\\install-staff-float-protocol.bat once. Other PCs: set bats\\staff-float-url.txt to http://192.168.2.100/queue-system/public/window/float';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'local' => $local,
                'pinned' => $pinnedOnServer,
                'needs_download' => ! $pinnedOnServer,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Windows helper ZIP for the connecting PC (Chrome blocks raw .bat downloads).
     */
    public function floatLauncher(Request $request)
    {
        $files = $this->floatHelperFiles($request);
        $tmp = tempnam(sys_get_temp_dir(), 'pfz');
        $zipPath = $tmp.'.zip';
        @unlink($tmp);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create the float helper download.');
        }

        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }
        $zip->close();

        $binary = (string) file_get_contents($zipPath);
        @unlink($zipPath);

        return response($binary, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="PECIT-Staff-Float.zip"',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function floatHelperFiles(Request $request): array
    {
        $url = $this->staffFloatUrl($request);
        $scriptPath = base_path('tools/staff-float/Start-StaffFloat.ps1');
        if (! is_file($scriptPath)) {
            abort(404, 'Float launcher was not found.');
        }

        $script = (string) file_get_contents($scriptPath);
        $script = preg_replace_callback(
            '/\[string\]\$Url = "[^"]*"/',
            fn () => '[string]$Url = '.json_encode($url, JSON_UNESCAPED_SLASHES),
            $script,
            1
        ) ?? $script;

        $bat = "@echo off\r\n"
            ."REM PECIT Staff Float — unzip, then double-click this file on THIS computer.\r\n"
            ."cd /d \"%~dp0\"\r\n"
            ."start \"\" powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File \"%~dp0Start-StaffFloat.ps1\" -Browser chrome\r\n"
            ."exit\r\n";

        return [
            'PECIT-Staff-Float.bat' => $bat,
            'Start-StaffFloat.ps1' => $script,
        ];
    }

    private function staffFloatUrl(Request $request): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/').route('window.float', [], false);
    }

    private function isLocalRequest(Request $request): bool
    {
        $ip = (string) $request->ip();

        return in_array($ip, ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true);
    }

    /**
     * Start a Windows process with no visible console, and do not wait for it.
     */
    private function startHiddenWindowsProcess(string $command): void
    {
        if (class_exists('COM', false)) {
            try {
                $shell = new \COM('WScript.Shell');
                $shell->Run($command, 0, false);

                return;
            } catch (\Throwable) {
                // Fall through to cmd start.
            }
        }

        pclose(popen('cmd /c start "" '.$command, 'r'));
    }

    /**
     * JSON for staff window auto-refresh (current & next queue).
     */
    public function state(): JsonResponse
    {
        $user = Auth::user();
        $windowId = $user->window_id;

        if (! $windowId) {
            return response()->json([
                'current' => null,
                'current_name' => null,
                'next' => null,
                'serving_started_at' => null,
                'waiting_list' => [],
                'held_list' => [],
            ], 403);
        }

        return response()->json($this->getWindowState($windowId));
    }

    public function callNext(Request $request)
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) {
            abort(403);
        }

        $today = Carbon::today()->toDateString();
        $windowServiceId = (int) DB::table('windows')->where('id', $windowId)->value('service_id');

        $queue = DB::transaction(function () use ($windowId, $windowServiceId, $today) {
            // Serialize Call Next across all windows for this service.
            Service::where('id', $windowServiceId)->lockForUpdate()->first();

            // Finish the currently called queue for THIS window only.
            $currentCall = $this->queueService->lockOpenCallForWindow($windowId, $today);

            if ($currentCall) {
                $currentCall->finished_time = now();
                $currentCall->save();

                $currentQueue = Queue::where('id', $currentCall->queue_id)
                    ->lockForUpdate()
                    ->first();

                if ($currentQueue) {
                    $currentQueue->status = 'done';
                    $currentQueue->save();
                }
            }

            $queue = $this->scheduler->claimNextWaiting($windowServiceId, $today);

            if (! $queue) {
                return null;
            }

            QueueCall::create([
                'queue_id' => $queue->id,
                'window_id' => $windowId,
                'called_time' => now(),
            ]);

            return $queue;
        });

        if (! $queue) {
            return back()->with('status', 'No more queues.');
        }

        return back()->with('called_queue_number', $queue->queue_number);
    }

    public function recall()
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) {
            abort(403);
        }

        $today = Carbon::today()->toDateString();

        $currentCall = $this->queueService->latestCallForWindow($windowId, $today);

        if (! $currentCall) {
            return back()->with('status', 'No current queue to recall.');
        }

        // Update called_time so display polling always sees recall as a fresh event.
        // Timer resets on recall (re-announce starts a fresh call moment).
        $currentCall->called_time = now();
        $currentCall->save();

        $queue = Queue::find($currentCall->queue_id);

        return back()->with('recalled_queue_number', $queue->queue_number);
    }

    public function complete()
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) {
            abort(403);
        }

        $today = Carbon::today()->toDateString();

        $done = DB::transaction(function () use ($windowId, $today) {
            $currentCall = $this->queueService->lockOpenCallForWindow($windowId, $today);

            if (! $currentCall) {
                return false;
            }

            $currentCall->finished_time = now();
            $currentCall->save();

            $queue = Queue::where('id', $currentCall->queue_id)->lockForUpdate()->first();
            if ($queue) {
                $queue->status = 'done';
                $queue->save();
            }

            return true;
        });

        if (! $done) {
            return back()->with('status', 'No current queue.');
        }

        return back()->with('status', 'Queue completed.');
    }

    /**
     * Set the current ticket aside (held). Frees the window and hides it from the display.
     */
    public function hold()
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) {
            abort(403);
        }

        $today = Carbon::today()->toDateString();

        $held = DB::transaction(function () use ($windowId, $today) {
            $currentCall = $this->queueService->lockOpenCallForWindow($windowId, $today);

            if (! $currentCall) {
                return null;
            }

            $queue = Queue::where('id', $currentCall->queue_id)->lockForUpdate()->first();
            if (! $queue || $queue->status !== 'serving') {
                return null;
            }

            $currentCall->finished_time = now();
            $currentCall->save();

            $queue->status = 'held';
            $queue->save();

            return $queue;
        });

        if (! $held) {
            return back()->with('status', 'No current queue to hold.');
        }

        return back()->with('status', 'Ticket '.$held->queue_number.' set aside.');
    }

    /**
     * Resume a held ticket at this window (does not go through 2P→1R).
     */
    public function callHeld(Request $request)
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) {
            abort(403);
        }

        $data = $request->validate([
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
        ]);

        $today = Carbon::today()->toDateString();
        $windowServiceId = (int) DB::table('windows')->where('id', $windowId)->value('service_id');

        $queue = DB::transaction(function () use ($windowId, $windowServiceId, $today, $data) {
            Service::where('id', $windowServiceId)->lockForUpdate()->first();

            $openCall = $this->queueService->lockOpenCallForWindow($windowId, $today);

            if ($openCall) {
                return 'busy';
            }

            $queue = Queue::where('id', $data['queue_id'])
                ->where('status', 'held')
                ->where('service_id', $windowServiceId)
                ->whereDate('queue_date', $today)
                ->lockForUpdate()
                ->first();

            if (! $queue) {
                return null;
            }

            $queue->status = 'serving';
            $queue->save();

            QueueCall::create([
                'queue_id' => $queue->id,
                'window_id' => $windowId,
                'called_time' => now(),
            ]);

            return $queue;
        });

        if ($queue === 'busy') {
            return back()->with('status', 'Complete or hold the current ticket first.');
        }

        if (! $queue) {
            return back()->with('status', 'Held ticket was not found.');
        }

        return back()->with('called_queue_number', $queue->queue_number);
    }

    /**
     * Staff's own service history (tickets they served). No delete, no other staff, no analytics.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $windowId = $user->window_id;

        if (! $windowId) {
            abort(403, 'No window assigned.');
        }

        $query = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->where('queue_calls.window_id', $windowId)
            ->where('queues.status', 'done')
            ->whereNotNull('queue_calls.finished_time')
            ->whereRaw('queue_calls.id = (SELECT MAX(qc2.id) FROM queue_calls qc2 WHERE qc2.queue_id = queues.id AND qc2.finished_time IS NOT NULL)')
            ->select(
                'queues.queue_number',
                'services.service_name',
                'queue_calls.called_time',
                'queue_calls.finished_time'
            )
            ->orderByDesc('queue_calls.called_time');

        if ($request->filled('date')) {
            $query->whereDate('queue_calls.called_time', $request->input('date'));
        }

        $records = $query->paginate(20);

        return view('staff.history', compact('records'));
    }

    /**
     * Held (set-aside) tickets for this service today.
     *
     * @return \Illuminate\Support\Collection<int, Queue>
     */
    private function heldTickets(int $serviceId, string $today)
    {
        return Queue::query()
            ->with('student')
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'held')
            ->orderBy('id')
            ->get();
    }
}
