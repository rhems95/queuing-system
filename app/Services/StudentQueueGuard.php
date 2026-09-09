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

    public function findStudent(string $normalizedId): ?Student
    {
        if ($normalizedId === '') {
            return null;
        }

        return Student::query()->where('student_id', $normalizedId)->first();
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
