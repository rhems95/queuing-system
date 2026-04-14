<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\QueueCall;

class QueueService
{
    public function latestCallForWindow(int $windowId, string $today): ?QueueCall
    {
        return QueueCall::where('window_id', $windowId)
            ->whereDate('called_time', $today)
            ->orderByDesc('called_time')
            ->first();
    }

    public function queueNumberFromCall(?QueueCall $call): ?string
    {
        if (! $call) {
            return null;
        }

        return Queue::where('id', $call->queue_id)->value('queue_number');
    }
}

