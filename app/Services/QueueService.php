<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\QueueCall;
use App\Models\Student;

class QueueService
{
    public function latestCallForWindow(int $windowId, string $today): ?QueueCall
    {
        return $this->openCallQuery($windowId, $today)->first();
    }

    /**
     * Open call for this window today (row lock). Must run inside a transaction.
     */
    public function lockOpenCallForWindow(int $windowId, string $today): ?QueueCall
    {
        return $this->openCallQuery($windowId, $today)->lockForUpdate()->first();
    }

    public function servingQueueFromCall(?QueueCall $call): ?Queue
    {
        if (! $call || $call->finished_time !== null) {
            return null;
        }

        $queue = Queue::find($call->queue_id);

        if (! $queue || $queue->status !== 'serving') {
            return null;
        }

        // If this ticket was later called at another window, do not still show it here.
        $latestOpen = QueueCall::query()
            ->where('queue_id', $queue->id)
            ->whereNull('finished_time')
            ->orderByDesc('called_time')
            ->orderByDesc('id')
            ->first();

        if (! $latestOpen || (int) $latestOpen->id !== (int) $call->id) {
            return null;
        }

        return $queue;
    }

    private function openCallQuery(int $windowId, string $today)
    {
        return QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->whereNull('finished_time')
            ->orderByDesc('called_time')
            ->orderByDesc('id');
    }

    public function queueNumberFromCall(?QueueCall $call): ?string
    {
        return $this->servingQueueFromCall($call)?->queue_number;
    }

    public function studentNameForQueue(?Queue $queue): ?string
    {
        if (! $queue || ! $queue->student_id) {
            return null;
        }

        $name = $queue->relationLoaded('student')
            ? $queue->student?->name
            : Student::query()->where('student_id', $queue->student_id)->value('name');

        return $name ? (string) $name : null;
    }
}

