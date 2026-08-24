<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $totalToday = Queue::whereDate('queue_date', $today)->count();
        $waiting = Queue::whereDate('queue_date', $today)->where('status', 'waiting')->count();
        $serving = Queue::whereDate('queue_date', $today)->where('status', 'serving')->count();
        $completed = Queue::whereDate('queue_date', $today)->where('status', 'done')->count();

        return view('admin.dashboard', compact('totalToday', 'waiting', 'serving', 'completed'));
    }

    public function waitingQueues(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $queues = Queue::with('service')
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get(['id', 'queue_number', 'service_id', 'priority', 'status']);

        $counts = [
            'total' => Queue::whereDate('queue_date', $today)->count(),
            'waiting' => Queue::whereDate('queue_date', $today)->where('status', 'waiting')->count(),
            'serving' => Queue::whereDate('queue_date', $today)->where('status', 'serving')->count(),
            'completed' => Queue::whereDate('queue_date', $today)->where('status', 'done')->count(),
        ];

        return response()->json([
            'queues' => $queues,
            'counts' => $counts,
        ]);
    }
}
