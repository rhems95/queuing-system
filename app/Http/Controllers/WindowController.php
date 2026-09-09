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
            'current_name' => $this->queueService->studentNameForQueue($currentQueue),
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
                'student_name' => $this->queueService->studentNameForQueue($q),
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
            'currentStudentName' => $this->queueService->studentNameForQueue($currentQueue),
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
            'currentStudentName' => $this->queueService->studentNameForQueue($currentQueue),
            'nextQueue' => $nextQueue,
            'window' => $window,
            'servingStartedAt' => $servingStartedAt,
        ]);
    }

    /**
     * Launch the Windows always-on-top float helper (.bat) on this PC.
     * Intended for local XAMPP kiosk/staff machines only.
     */
    public function launchFloat(Request $request)
    {
        $bat = base_path('bats/start-staff-float.bat');

        if (! is_file($bat)) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Float launcher file was not found.'], 404);
            }

            return back()->with('status', 'Float launcher file was not found.');
        }

        // Non-blocking launch on Windows so Apache does not wait for the script.
        $cmd = 'cmd /c start "" '.escapeshellarg($bat);
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            pclose(popen($cmd, 'r'));
        } else {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'System float is only available on Windows.'], 400);
            }

            return back()->with('status', 'System float is only available on Windows.');
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'System float launched. Log in there if asked.']);
        }

        return back()->with('status', 'System float launched. Log in there if asked.');
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
