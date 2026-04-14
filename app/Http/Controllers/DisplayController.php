<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DisplayController extends Controller
{
    public function __construct(private QueueService $queueService)
    {
    }

    /**
     * Data used for both the HTML view and the JSON poll.
     */
    private function getDisplayData(): array
    {
        $today = Carbon::today()->toDateString();

        $windows = DB::table('windows')->get();

        $nowServing = [];
        foreach ($windows as $window) {
            $call = $this->queueService->latestCallForWindow($window->id, $today);
            $queueNumber = $this->queueService->queueNumberFromCall($call);

            $nowServing[] = [
                'window_name'  => $window->window_name,
                'queue_number' => $queueNumber,
                'call_token'   => $call ? ($call->id.'|'.($call->called_time ?? '')) : null,
            ];
        }

        $waiting = Queue::whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get(['queue_number', 'student_name', 'priority']);

        return [
            'now_serving' => $nowServing,
            'waiting'     => $waiting->map(fn ($q) => [
                'queue_number' => $q->queue_number,
                'student_name' => $q->student_name,
                'priority'     => $q->priority ? 'Priority' : 'Regular',
            ])->values()->all(),
        ];
    }

    public function index()
    {
        $data = $this->getDisplayData();

        return view('display.index', [
            'nowServing' => $data['now_serving'],
            'waiting'    => collect($data['waiting'])->map(fn ($w) => (object) $w),
        ]);
    }

    /**
     * JSON endpoint for display page auto-refresh (polling).
     */
    public function data(): JsonResponse
    {
        return response()->json($this->getDisplayData());
    }
}

