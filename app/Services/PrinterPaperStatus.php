<?php

namespace App\Services;

class PrinterPaperStatus
{
    public const STALE_SECONDS = 20;

    public const HOLD_SECONDS = 300;

    public function path(): string
    {
        return storage_path('app/kiosk-printer.json');
    }

    public function isOut(): bool
    {
        $path = $this->path();
        $age = is_file($path) ? time() - (int) filemtime($path) : null;
        $data = $this->read($path);

        if ($age === null || $age > self::STALE_SECONDS) {
            $this->ensureWatcher();
        }

        if (! is_array($data) || empty($data['paper_out'])) {
            return false;
        }

        return $age !== null && $age <= self::HOLD_SECONDS;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function read(string $path): ?array
    {
        if (! is_file($path)) {
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

        return is_array($data) ? $data : null;
    }

    private function ensureWatcher(): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $vbs = base_path('tools/kiosk/Start-PrinterPaper-Hidden.vbs');
        if (! is_file($vbs)) {
            return;
        }

        $lock = storage_path('app/kiosk-printer.launch');
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
