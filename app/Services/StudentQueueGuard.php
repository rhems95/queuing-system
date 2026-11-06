<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Student;
use Illuminate\Support\Carbon;

class StudentQueueGuard
{
    public const OPEN_STATUSES = ['waiting', 'serving', 'held'];

    public function normalize(?string $raw): string
    {
        $id = strtoupper(trim((string) $raw));
        $id = preg_replace('/\s+/', '', $id) ?? '';

        return $id;
    }

    public static function normalizeUid(mixed $raw): string
    {
        $uid = strtoupper(trim((string) $raw));
        $uid = preg_replace('/[^A-F0-9]/', '', $uid) ?? '';

        return $uid;
    }

    public function findStudent(string $normalizedId): ?Student
    {
        if ($normalizedId === '') {
            return null;
        }

        return Student::query()->where('student_id', $normalizedId)->first();
    }

    public function findByCardUid(string $normalizedUid): ?Student
    {
        if ($normalizedUid === '') {
            return null;
        }

        return Student::query()->where('card_uid', $normalizedUid)->first();
    }

    public function openTicketToday(string $normalizedId, ?string $today = null): ?Queue
    {
        if ($normalizedId === '') {
            return null;
        }

        $today ??= Carbon::today()->toDateString();

        return Queue::query()
            ->where('student_id', $normalizedId)
            ->whereDate('queue_date', $today)
            ->whereIn('status', self::OPEN_STATUSES)
            ->orderByDesc('id')
            ->first();
    }
}
