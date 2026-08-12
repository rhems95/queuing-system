<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\QueueCall;
use App\Models\Window;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\QueueService;

class WindowController extends Controller
{
    public function __construct(private QueueService $queueService)
    {
    }

    /**
     * Current and next queue for the staff's window (for polling).
     */
    private function getWindowState(int $windowId): array
    {
        $today = Carbon::today()->toDateString();

        $currentCall = $this->queueService->latestCallForWindow($windowId, $today);

        $currentQueueNumber = null;
        if ($currentCall) {
            $currentQueueNumber = $this->queueService->queueNumberFromCall($currentCall);
        }

        $window = Window::find($windowId);
        $windowServiceId = $window ? $window->service_id : null;

        $nextQueue = $windowServiceId
            ? Queue::where('service_id', $windowServiceId)
                ->whereDate('queue_date', $today)
                ->where('status', 'waiting')
                ->orderByDesc('priority')
                ->orderBy('id')
                ->first()
            : null;

        $waitingList = $windowServiceId
            ? Queue::where('service_id', $windowServiceId)
                ->whereDate('queue_date', $today)
                ->where('status', 'waiting')
                ->orderByDesc('priority')
                ->orderBy('queue_number')
                ->limit(10)
                ->get(['id', 'queue_number', 'priority'])
            : collect();

        return [
            'current'       => $currentQueueNumber,
            'next'          => $nextQueue ? $nextQueue->queue_number : null,
            'waiting_list'  => $waitingList->map(fn ($q) => [
                'queue_number' => $q->queue_number,
                'priority'     => $q->priority ? 'Priority' : 'Regular',
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

        $currentCall = QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();

        $currentQueue = $currentCall ? Queue::find($currentCall->queue_id) : null;

        $windowServiceId = $window->service_id;

        $nextQueue = Queue::where('service_id', $windowServiceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->first();

        $waitingTickets = Queue::where('service_id', $windowServiceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('queue_number')
            ->limit(10)
            ->get();

        return view('staff.window', [
            'currentQueue'   => $currentQueue,
            'nextQueue'      => $nextQueue,
            'window'         => $window,
            'waitingTickets' => $waitingTickets,
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

        $currentCall = QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();

        $currentQueue = $currentCall ? Queue::find($currentCall->queue_id) : null;

        $nextQueue = Queue::where('service_id', $window->service_id)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->first();

        return view('staff.float', [
            'currentQueue' => $currentQueue,
            'nextQueue'    => $nextQueue,
            'window'       => $window,
        ]);
    }

    /**
     * Launch the Windows always-on-top float helper (.bat) on this PC.
     * Intended for local XAMPP kiosk/staff machines only.
     */
    public function launchFloat(Request $request)
    {
        $bat = base_path('start-staff-float.bat');

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
            return response()->json(['current' => null, 'next' => null, 'waiting_list' => []], 403);
        }

        return response()->json($this->getWindowState($windowId));
    }

    public function callNext(Request $request)
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) abort(403);

        $today = Carbon::today()->toDateString();
        $windowServiceId = DB::table('windows')->where('id', $windowId)->value('service_id');

        $queue = DB::transaction(function () use ($windowId, $windowServiceId, $today) {
            // Finish the currently called queue for this window (same as "Complete"),
            // so history + admin completed counts reflect immediately.
            $currentCall = QueueCall::where('window_id', $windowId)
                ->whereDate('called_time', $today)
                ->orderByDesc('called_time')
                ->lockForUpdate()
                ->first();

            if ($currentCall && $currentCall->finished_time === null) {
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

            $queue = Queue::where('service_id', $windowServiceId)
                ->whereDate('queue_date', $today)
                ->where('status', 'waiting')
                ->orderByDesc('priority')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $queue) {
                return null;
            }

            $queue->status = 'serving';
            $queue->save();

            QueueCall::create([
                'queue_id'    => $queue->id,
                'window_id'   => $windowId,
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
        if (! $windowId) abort(403);

        $today = Carbon::today()->toDateString();

        $currentCall = QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();

        if (! $currentCall) {
            return back()->with('status', 'No current queue to recall.');
        }

        // Update called_time so display polling always sees recall as a fresh event.
        $currentCall->called_time = now();
        $currentCall->save();

        $queue = Queue::find($currentCall->queue_id);

        return back()->with('recalled_queue_number', $queue->queue_number);
    }

    public function complete()
    {
        $user = Auth::user();
        $windowId = $user->window_id;
        if (! $windowId) abort(403);

        $today = Carbon::today()->toDateString();

        $currentCall = QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();

        if (! $currentCall) {
            return back()->with('status', 'No current queue.');
        }

        $currentCall->finished_time = now();
        $currentCall->save();

        $queue = Queue::find($currentCall->queue_id);
        if ($queue) {
            $queue->status = 'done';
            $queue->save();
        }

        return back()->with('status', 'Queue completed.');
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
}
