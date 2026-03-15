<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DisplayController extends Controller
{
    /**
     * Data used for both the HTML view and the JSON poll.
     */
    private function getDisplayData(): array
    {
        $today = Carbon::today()->toDateString();

        $windows = DB::table('windows')->get();

        $nowServing = [];
        foreach ($windows as $window) {
            $call = DB::table('queue_calls')
                ->where('window_id', $window->id)
                ->whereDate('called_time', $today)
                ->orderByDesc('called_time')
                ->first();

            $queueNumber = null;
            if ($call) {
                $queueNumber = DB::table('queues')->where('id', $call->queue_id)->value('queue_number');
            }

            $nowServing[] = [
                'window_name'  => $window->window_name,
                'queue_number' => $queueNumber,
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
                'priority'     => $q->priority ? 'Parent' : 'Regular',
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

