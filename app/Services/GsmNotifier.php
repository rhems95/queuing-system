<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class GsmNotifier
{
    public const NEAR_AHEAD = 10;

    public function normalizeMobile(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            if (! preg_match('/^09\d{9}$/', $digits)) {
                return null;
            }
            $digits = '63'.substr($digits, 1);
        } elseif (preg_match('/^9\d{9}$/', $digits)) {
            $digits = '63'.$digits;
        }
        if (! preg_match('/^639\d{9}$/', $digits)) {
            return null;
        }

        return '+'.$digits;
    }

    public function last4(?string $raw): ?string
    {
        $mobile = $this->normalizeMobile($raw);
        if ($mobile === null) {
            return null;
        }

        return substr($mobile, -4);
    }

    public function sendIssued(Queue $queue, Service $service): void
    {
        if (! $this->ready() || ! $queue->sms_mobile || (int) $queue->sms_issued === 1) {
            return;
        }

        $serviceName = trim((string) $service->service_name);
        $text = 'PECIT: Your ticket is '.$queue->queue_number;
        if ($serviceName !== '') {
            $text .= ' ('.$serviceName.')';
        }
        $token = trim((string) ($queue->screen_token ?? ''));
        if ($token !== '') {
            $text .= '. Token: '.$token;
        }
        $text .= '.';

        if ($this->enqueue($queue->id.'-issued', (string) $queue->sms_mobile, $text)) {
            $queue->sms_issued = 1;
            $queue->save();
        }

        $this->markAlreadyClose($queue);
    }

    /**
     * A ticket already inside the 10-ahead window only gets the ticket-number text,
     * then the called text. Mark it so a later Call Next does not send "come back".
     */
    public function markAlreadyClose(Queue $queue): void
    {
        if (! $this->ready() || ! $queue->sms_mobile || (int) $queue->sms_near === 1) {
            return;
        }

        $today = $queue->queue_date
            ? Carbon::parse($queue->queue_date)->toDateString()
            : Carbon::today()->toDateString();
        $ahead = app(FairQueueScheduler::class)->ticketsAheadForQueue($queue, $today);
        if ($ahead <= self::NEAR_AHEAD) {
            $queue->sms_near = 1;
            $queue->save();
        }
    }

    public function sendCalled(Queue $queue, string $windowName): void
    {
        if (! $this->ready() || ! $queue->sms_mobile) {
            return;
        }

        $windowName = trim($windowName) !== '' ? trim($windowName) : 'the window';
        $text = 'PECIT: '.$queue->queue_number.', proceed to '.$windowName.'.';
        $this->enqueue($queue->id.'-called-'.now()->format('YmdHis'), (string) $queue->sms_mobile, $text);
    }

    public function sendPaperOut(string $id, string $mobile): bool
    {
        $mobile = $this->normalizeMobile($mobile);
        if ($mobile === null) {
            return false;
        }

        return $this->enqueue($id, $mobile, 'PECIT: Kiosk printer is out of paper. Load a new roll.');
    }

    /**
     * Tell opted-in waiting tickets that have moved to 10 or fewer ahead.
     * A ticket issued already inside that range does not get this text.
     */
    public function notifyWaitingWithinTen(int $serviceId): void
    {
        if (! $this->ready() || $serviceId < 1) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $waiting = Queue::query()
            ->where('service_id', $serviceId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->whereNotNull('sms_mobile')
            ->where('sms_issued', 1)
            ->where('sms_near', 0)
            ->get();

        $scheduler = app(FairQueueScheduler::class);
        foreach ($waiting as $queue) {
            $ahead = $scheduler->ticketsAheadForQueue($queue, $today);
            if ($ahead < 1 || $ahead > self::NEAR_AHEAD) {
                continue;
            }
            $text = 'PECIT: '.$queue->queue_number.' is '.$ahead.' tickets away. Please come back to the waiting area.';
            if ($this->enqueue($queue->id.'-near', (string) $queue->sms_mobile, $text)) {
                $queue->sms_near = 1;
                $queue->save();
            }
        }
    }

    public function ready(): bool
    {
        return Schema::hasColumn('queues', 'sms_mobile');
    }

    private function enqueue(string $id, string $mobile, string $text): bool
    {
        $dir = storage_path('app/sms-outbox');
        if (! is_dir($dir) && ! @mkdir($dir, 0775, true) && ! is_dir($dir)) {
            return false;
        }

        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $id) ?: (string) time();
        $path = $dir.DIRECTORY_SEPARATOR.$safeId.'.json';
        if (is_file($path)) {
            return true;
        }

        $payload = json_encode([
            'to' => $mobile,
            'text' => $text,
            'queued_at' => now()->toIso8601String(),
        ], JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            return false;
        }

        if (@file_put_contents($path, $payload) === false) {
            return false;
        }

        $this->ensureWatcher();

        return true;
    }

    private function ensureWatcher(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $vbs = base_path('tools/kiosk/Start-GsmSms-Hidden.vbs');
        if (! is_file($vbs)) {
            return;
        }

        $lock = storage_path('app/kiosk-gsm.launch');
        if (is_file($lock) && (time() - (int) filemtime($lock)) < 30) {
            return;
        }

        @touch($lock);

        $cmd = 'wscript.exe //nologo "'.$vbs.'"';
        if (class_exists('COM', false)) {
            try {
                $shell = new \COM('WScript.Shell');
                $shell->Run($cmd, 0, false);

                return;
            } catch (\Throwable) {
                // Fall through.
            }
        }

        pclose(popen('cmd /c start "" /MIN '.$cmd, 'r'));
    }
}
