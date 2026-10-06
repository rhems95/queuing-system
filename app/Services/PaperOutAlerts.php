<?php

namespace App\Services;

use App\Models\PaperSmsContact;
use Illuminate\Support\Facades\Schema;

class PaperOutAlerts
{
    /**
     * Send one paper-out text to each enabled staff number.
     * A later poll while the roll is still out does not send again.
     * Loading paper clears that mark so the next outage can text.
     */
    public function sync(bool $paperOut): void
    {
        if (! Schema::hasTable('paper_sms_contacts')) {
            return;
        }

        $lockPath = storage_path('app/kiosk-paper-sms.lock');
        $flagPath = storage_path('app/kiosk-paper-sms.json');
        $fh = @fopen($lockPath, 'c');
        if ($fh === false) {
            return;
        }
        if (! flock($fh, LOCK_EX | LOCK_NB)) {
            fclose($fh);

            return;
        }

        try {
            $flag = $this->readFlag($flagPath);
            $active = ! empty($flag['active']);
            if (! $paperOut) {
                if ($active) {
                    $this->writeFlag($flagPath, ['active' => false]);
                }

                return;
            }
            if ($active) {
                return;
            }

            $contacts = PaperSmsContact::query()->where('enabled', 1)->orderBy('id')->get();
            if ($contacts->isEmpty()) {
                return;
            }

            $stamp = now()->format('YmdHis');
            $gsm = app(GsmNotifier::class);
            $allOk = true;
            foreach ($contacts as $contact) {
                $tail = substr(preg_replace('/\D+/', '', (string) $contact->mobile) ?? '', -4);
                $id = 'paper-'.$stamp.'-'.$tail;
                if (! $gsm->sendPaperOut($id, (string) $contact->mobile)) {
                    $allOk = false;
                }
            }
            if ($allOk) {
                $this->writeFlag($flagPath, ['active' => true, 'stamp' => $stamp]);
            }
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function readFlag(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }
        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function writeFlag(string $path, array $data): void
    {
        $payload = json_encode($data);
        if ($payload === false) {
            return;
        }
        @file_put_contents($path, $payload);
    }
}
