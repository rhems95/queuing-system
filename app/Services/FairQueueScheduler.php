<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Shared fair call order for a service (2 Priority → 1 Regular).
 * All windows for the same service_id pull from one waiting queue.
 */
class FairQueueScheduler
{
    public const PRIORITIES_PER_REGULAR = 2;

    /**
     * How many priority tickets have been called since the last regular
     * (service-wide, today). Used to decide the next fair pick.
     */
    public function prioritiesSinceRegular(int $serviceId, string $today): int
    {
        $recent = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->where('queues.service_id', $serviceId)
            ->whereDate('queue_calls.called_time', $today)
            ->orderByDesc('queue_calls.id')
            ->limit(20)
            ->get(['queues.priority']);

        $streak = 0;
        foreach ($recent as $row) {
            if ((int) $row->priority === 1) {
                $streak++;
                continue;
            }
            break;
        }

        return $streak;
    }

    /**
     * Whether the next call should prefer a priority ticket.
     */
    public function shouldTakePriority(int $streak, bool $hasPriority, bool $hasRegular): bool
    {
        if ($hasPriority && ! $hasRegular) {
            return true;
        }
        if (! $hasPriority && $hasRegular) {
            return false;
        }
        if (! $hasPriority && ! $hasRegular) {
            return false;
        }

        return $streak < self::PRIORITIES_PER_REGULAR;
    }

    /**
     * Next waiting ticket under the fair rule (does not mutate).
     * Caller should hold a service-level lock when claiming.
     */
    public function peekNextWaiting(int $serviceId, string $today): ?Queue
    {
        return $this->orderedWaiting($serviceId, $today, 1)->first();
    }

    /**
     * Waiting tickets in the order they will be called (fair schedule).
     *
     * @return Collection<int, Queue>
     */
    public function orderedWaiting(int $serviceId, string $today, int $limit = 10): Collection
    {
        $priority = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 1)
            ->orderBy('id')
            ->get();

        $regular = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 0)
            ->orderBy('id')
            ->get();

        $streak = $this->prioritiesSinceRegular($serviceId, $today);
        $result = collect();

        while ($result->count() < $limit && ($priority->isNotEmpty() || $regular->isNotEmpty())) {
            $takePriority = $this->shouldTakePriority(
                $streak,
                $priority->isNotEmpty(),
                $regular->isNotEmpty()
            );

            if ($takePriority) {
                $result->push($priority->shift());
                $streak++;
            } else {
                $result->push($regular->shift());
                $streak = 0;
            }
        }

        return $result;
    }

    /**
     * Claim the next waiting ticket (status → serving). Returns null if none
     * or if a concurrent claim won the race.
     * Must run inside a DB transaction with the service row locked.
     */
    public function claimNextWaiting(int $serviceId, string $today): ?Queue
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = $this->peekNextWaiting($serviceId, $today);
            if (! $candidate) {
                return null;
            }

            $updated = Queue::where('id', $candidate->id)
                ->where('status', 'waiting')
                ->update(['status' => 'serving']);

            if ($updated === 1) {
                return Queue::find($candidate->id);
            }
        }

        return null;
    }

    /**
     * How many waiting tickets would be called before a new ticket of the
     * given priority (hypothetical, before insert).
     */
    public function ticketsAheadForNew(int $serviceId, string $today, bool $isPriority): int
    {
        $priority = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 1)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $regular = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 0)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        // Virtual ticket at the end of its lane.
        $virtualId = PHP_INT_MAX;
        if ($isPriority) {
            $priority[] = $virtualId;
        } else {
            $regular[] = $virtualId;
        }

        return $this->simulateAhead($serviceId, $today, $priority, $regular, $virtualId);
    }

    /**
     * How many waiting tickets would be called before an existing waiting ticket.
     */
    public function ticketsAheadForQueue(Queue $queue, string $today): int
    {
        $priority = Queue::query()
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 1)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $regular = Queue::query()
            ->where('service_id', $queue->service_id)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->where('priority', 0)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        return $this->simulateAhead(
            (int) $queue->service_id,
            $today,
            $priority,
            $regular,
            (int) $queue->id
        );
    }

    /**
     * @param  list<int>  $priorityIds
     * @param  list<int>  $regularIds
     */
    private function simulateAhead(
        int $serviceId,
        string $today,
        array $priorityIds,
        array $regularIds,
        int $targetId
    ): int {
        $streak = $this->prioritiesSinceRegular($serviceId, $today);
        $ahead = 0;

        while ($priorityIds !== [] || $regularIds !== []) {
            $hasP = $priorityIds !== [];
            $hasR = $regularIds !== [];
            $takePriority = $this->shouldTakePriority($streak, $hasP, $hasR);

            if ($takePriority) {
                $id = array_shift($priorityIds);
                $streak++;
            } else {
                $id = array_shift($regularIds);
                $streak = 0;
            }

            if ($id === $targetId) {
                return $ahead;
            }

            $ahead++;
        }

        return $ahead;
    }
}
