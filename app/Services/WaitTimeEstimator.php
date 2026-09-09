<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\QueueCall;
use App\Models\User;
use App\Models\Window;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WaitTimeEstimator
{
    public function __construct(private FairQueueScheduler $scheduler)
    {
    }

    /**
     * Snapshot used by kiosk confirm + thermal print.
     *
     * @return array{
     *   estimated_minutes: int|null,
     *   priority_waiting: int,
     *   regular_waiting: int,
     *   currently_serving: string|null,
     *   active_counters: int,
     *   average_service_minutes: float|null
     * }
     */
    public function snapshot(int $serviceId, bool $isPriority, ?Queue $existingQueue = null): array
    {
        $today = Carbon::today()->toDateString();

        $priorityWaiting = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 1)
            ->count();

        $regularWaiting = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 0)
            ->count();

        $servingNumbers = QueueCall::query()
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('windows', 'queue_calls.window_id', '=', 'windows.id')
            ->where('windows.service_id', $serviceId)
            ->where('queues.status', 'serving')
            ->whereDate('queue_calls.called_time', $today)
            ->whereNull('queue_calls.finished_time')
            ->orderBy('queue_calls.id')
            ->pluck('queues.queue_number')
            ->all();

        $activeCounters = $this->activeCounterCount($serviceId, $today);
        $avgMinutes = $this->averageServiceMinutes($serviceId);

        $ticketsAhead = $existingQueue
            ? $this->scheduler->ticketsAheadForQueue($existingQueue, $today)
            : $this->scheduler->ticketsAheadForNew($serviceId, $today, $isPriority);

        $servingCount = count($servingNumbers);
        $estimated = null;

        if ($avgMinutes !== null) {
            $slots = $ticketsAhead + $servingCount;
            $perCounter = $slots / max($activeCounters, 1);
            $estimated = (int) max(1, (int) ceil($perCounter * $avgMinutes));
            // Cap display so cold/outlier averages cannot print extreme values.
            $estimated = min($estimated, 180);
        }

        return [
            'estimated_minutes' => $estimated,
            'priority_waiting' => $priorityWaiting,
            'regular_waiting' => $regularWaiting,
            'currently_serving' => $servingNumbers === [] ? null : implode(', ', $servingNumbers),
            'active_counters' => $activeCounters,
            'average_service_minutes' => $avgMinutes,
        ];
    }

    /**
     * Active counters for a service: staffed active windows, or windows
     * currently serving — never treat a multi-window service as a single server.
     */
    public function activeCounterCount(int $serviceId, string $today): int
    {
        $windowIds = Window::query()
            ->where('service_id', $serviceId)
            ->where('status', 'active')
            ->pluck('id');

        if ($windowIds->isEmpty()) {
            return 1;
        }

        $staffed = User::query()
            ->where('role', 'staff')
            ->whereIn('window_id', $windowIds)
            ->pluck('window_id')
            ->unique();

        $serving = QueueCall::query()
            ->whereIn('window_id', $windowIds)
            ->whereDate('called_time', $today)
            ->whereNull('finished_time')
            ->pluck('window_id')
            ->unique();

        $active = $staffed->merge($serving)->unique()->count();

        if ($active > 0) {
            return $active;
        }

        // No staff assigned yet — fall back to configured active windows.
        return max(1, $windowIds->count());
    }

    /**
     * Average completed service duration in minutes (recent history).
     * Returns null when there is not enough data.
     */
    public function averageServiceMinutes(int $serviceId): ?float
    {
        $rows = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->where('queues.service_id', $serviceId)
            ->where('queues.status', 'done')
            ->whereNotNull('queue_calls.finished_time')
            ->whereRaw('queue_calls.id = (SELECT MAX(qc2.id) FROM queue_calls qc2 WHERE qc2.queue_id = queues.id AND qc2.finished_time IS NOT NULL)')
            ->where('queue_calls.finished_time', '>=', now()->subDays(14))
            ->orderByDesc('queue_calls.finished_time')
            ->limit(50)
            ->get(['queue_calls.called_time', 'queue_calls.finished_time']);

        $minutes = [];
        foreach ($rows as $row) {
            $start = Carbon::parse($row->called_time);
            $end = Carbon::parse($row->finished_time);
            $secs = $start->diffInSeconds($end);
            // Ignore zero/negative and extreme outliers (> 60 min).
            if ($secs < 15 || $secs > 3600) {
                continue;
            }
            $minutes[] = $secs / 60;
        }

        if (count($minutes) < 3) {
            return null;
        }

        return round(array_sum($minutes) / count($minutes), 2);
    }
}
