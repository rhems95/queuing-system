<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use App\Services\FairQueueScheduler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct(private FairQueueScheduler $scheduler)
    {
    }

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

        $queues = new Collection;
        foreach (Service::query()->orderBy('id')->get() as $service) {
            foreach ($this->scheduler->orderedWaiting((int) $service->id, $today, 500) as $queue) {
                $queues->push($queue);
            }
        }
        $queues->load('service');

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
