<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class KioskCardTap
{
    public const FRESH_SECONDS = 8;

    public function path(): string
    {
        return storage_path('app/kiosk-card.json');
    }

    /**
     * Start the hidden ACR122U helper when the kiosk is open and the helper is not running.
     */
    public function ensureRunning(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $vbs = base_path('tools/kiosk/Start-Acr122-Hidden.vbs');
        if (! is_file($vbs)) {
            return;
        }

        $lock = storage_path('app/kiosk-card.launch');
        if (is_file($lock) && (time() - (int) filemtime($lock)) < 15) {
            return;
        }

        @touch($lock);
        if ($this->watcherIsRunning()) {
            return;
        }

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

    /**
     * @return array{uid: string, seen_at: string}|null
     */
    public function latestFresh(): ?array
    {
        $this->ensureRunning();

        $path = $this->path();
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $raw = @file_get_contents($path);
        if ($raw === false || $raw === '') {
            return null;
        }

        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        }

        $data = json_decode($raw, true);
        if (! is_array($data)) {
            return null;
        }

        $uid = StudentQueueGuard::normalizeUid($data['uid'] ?? null);
        $seenAt = trim((string) ($data['seen_at'] ?? ''));
        if ($uid === '' || $seenAt === '') {
            return null;
        }

        try {
            $seen = Carbon::parse($seenAt);
        } catch (\Throwable) {
            return null;
        }

        if ($seen->lt(now()->subSeconds(self::FRESH_SECONDS))) {
            return null;
        }

        return [
            'uid' => $uid,
            'seen_at' => $seen->toIso8601String(),
        ];
    }

    private function watcherIsRunning(): bool
    {
        if (! class_exists('COM', false)) {
            return false;
        }

        try {
            $wmi = new \COM('winmgmts:\\\\.\\root\\cimv2');
            $procs = $wmi->ExecQuery("SELECT CommandLine FROM Win32_Process WHERE Name='powershell.exe'");
            foreach ($procs as $proc) {
                $cmd = (string) $proc->CommandLine;
                if (stripos($cmd, 'Watch-Acr122.ps1') !== false) {
                    return true;
                }
            }
        } catch (\Throwable) {
            return false;
        }

        return false;
    }
}
