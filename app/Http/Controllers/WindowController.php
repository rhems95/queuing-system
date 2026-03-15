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

class WindowController extends Controller
{
    /**
     * Current and next queue for the staff's window (for polling).
     */
    private function getWindowState(int $windowId): array
    {
        $today = Carbon::today()->toDateString();

        $currentCall = QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();

        $currentQueueNumber = null;
        if ($currentCall) {
            $currentQueueNumber = Queue::where('id', $currentCall->queue_id)->value('queue_number');
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

        return [
            'current' => $currentQueueNumber,
            'next'    => $nextQueue ? $nextQueue->queue_number : null,
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

        return view('staff.window', [
            'currentQueue' => $currentQueue,
            'nextQueue'    => $nextQueue,
            'window'       => $window,
        ]);
    }

    /**
     * JSON for staff window auto-refresh (current & next queue).
     */
    public function state(): JsonResponse
    {
        $user = Auth::user();
        $windowId = $user->window_id;

        if (! $windowId) {
            return response()->json(['current' => null, 'next' => null], 403);
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
                'queues.student_name',
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

