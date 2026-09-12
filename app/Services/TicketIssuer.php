<?php

namespace App\Services;

use App\Models\DailyQueueCounter;
use App\Models\Queue;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TicketIssuer
{
    public const REASON_NEW_ENROLLEE = 'new_enrollee';

    public const REASON_NO_RECORD = 'no_record';

    public const REASON_OTHER = 'other';

    /**
     * @return array<string, string>
     */
    public static function walkInReasons(): array
    {
        return [
            self::REASON_NEW_ENROLLEE => 'New enrollee',
            self::REASON_NO_RECORD => 'No record in database',
            self::REASON_OTHER => 'Other (cannot use kiosk)',
        ];
    }

    public static function reasonLabel(?string $reason): ?string
    {
        if ($reason === null || $reason === '') {
            return null;
        }

        return self::walkInReasons()[$reason] ?? $reason;
    }

    /**
     * Compact staff-only label, e.g. "Walk-in (New enrollee)".
     */
    public static function walkInServingLabel(?string $reason): string
    {
        $short = match ($reason) {
            self::REASON_NEW_ENROLLEE => 'New enrollee',
            self::REASON_NO_RECORD => 'No record',
            self::REASON_OTHER => 'Other',
            default => self::reasonLabel($reason),
        };

        return $short ? 'Walk-in ('.$short.')' : 'Walk-in';
    }

    /**
     * Next waiting ticket for a service today. Caller should hold any
     * student-row lock before calling when issuing a student ticket.
     */
    public function issueWaiting(
        Service $service,
        bool $isPriority,
        ?string $studentId = null,
        ?int $issuedBy = null,
        ?string $issueReason = null,
    ): Queue {
        $today = Carbon::today()->toDateString();

        $counter = DailyQueueCounter::where('service_id', $service->id)
            ->whereDate('queue_date', $today)
            ->lockForUpdate()
            ->first();

        if (! $counter) {
            $counter = DailyQueueCounter::create([
                'service_id' => $service->id,
                'queue_date' => $today,
                'last_number' => 0,
            ]);
        }

        $next = $counter->last_number + 1;
        $counter->last_number = $next;
        $counter->save();

        $numberStr = str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        $queueNumber = $service->prefix.$numberStr;

        $payload = [
            'queue_number' => $queueNumber,
            'service_id' => $service->id,
            'student_id' => $studentId,
            'priority' => $isPriority ? 1 : 0,
            'status' => 'waiting',
            'queue_date' => $today,
        ];

        if (Schema::hasColumn('queues', 'issued_by')) {
            $payload['issued_by'] = $issuedBy;
        }
        if (Schema::hasColumn('queues', 'issue_reason')) {
            $payload['issue_reason'] = $issueReason;
        }
        if (Schema::hasColumn('queues', 'created_at')) {
            $payload['created_at'] = now();
        }
        if (Schema::hasColumn('queues', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        $queueId = DB::table('queues')->insertGetId($payload);

        return Queue::findOrFail($queueId);
    }
}
