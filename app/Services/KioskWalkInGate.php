<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class KioskWalkInGate
{
    public const SESSION_UNTIL = 'kiosk_walkin_until';

    public const SESSION_ISSUER = 'kiosk_walkin_issuer_id';

    public const SESSION_FAILS = 'kiosk_walkin_fails';

    public const SESSION_FAIL_UNTIL = 'kiosk_walkin_fail_until';

    public function isUnlocked(Request $request): bool
    {
        $until = (int) $request->session()->get(self::SESSION_UNTIL, 0);
        if ($until < time()) {
            $this->lock($request);

            return false;
        }

        return (int) $request->session()->get(self::SESSION_ISSUER, 0) > 0;
    }

    public function issuerId(Request $request): ?int
    {
        if (! $this->isUnlocked($request)) {
            return null;
        }

        $id = (int) $request->session()->get(self::SESSION_ISSUER, 0);

        return $id > 0 ? $id : null;
    }

    public function lock(Request $request): void
    {
        $request->session()->forget([self::SESSION_UNTIL, self::SESSION_ISSUER]);
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function unlock(Request $request, string $pin): array
    {
        $failUntil = (int) $request->session()->get(self::SESSION_FAIL_UNTIL, 0);
        if ($failUntil > time()) {
            return ['ok' => false, 'error' => 'Too many attempts. Try again in a minute.'];
        }

        $expected = $this->currentPin();
        $given = preg_replace('/\D+/', '', $pin) ?? '';

        if ($expected === '' || strlen($given) !== strlen($expected) || ! hash_equals($expected, $given)) {
            $fails = (int) $request->session()->get(self::SESSION_FAILS, 0) + 1;
            $request->session()->put(self::SESSION_FAILS, $fails);
            if ($fails >= 5) {
                $request->session()->put(self::SESSION_FAIL_UNTIL, time() + 60);
                $request->session()->put(self::SESSION_FAILS, 0);
            }

            return ['ok' => false, 'error' => 'Wrong PIN.'];
        }

        $issuer = $this->resolveIssuer();
        if (! $issuer) {
            return ['ok' => false, 'error' => 'Walk-in is not configured (no guard account).'];
        }

        $minutes = max(1, (int) config('kiosk.walkin_unlock_minutes', 5));
        $request->session()->put(self::SESSION_UNTIL, time() + ($minutes * 60));
        $request->session()->put(self::SESSION_ISSUER, (int) $issuer->id);
        $request->session()->forget([self::SESSION_FAILS, self::SESSION_FAIL_UNTIL]);

        return ['ok' => true];
    }

    public function resolveIssuer(): ?User
    {
        $email = (string) config('kiosk.walkin_issuer_email', 'guard@gmail.com');
        $user = User::query()->where('email', $email)->whereIn('role', ['guard', 'admin'])->first();
        if ($user) {
            return $user;
        }

        return User::query()->where('role', 'guard')->orderBy('id')->first();
    }

    public function currentPin(): string
    {
        $pin = preg_replace('/\D+/', '', (string) Setting::getValue('walkin_pin', '1981')) ?? '';

        return $pin !== '' ? $pin : '1981';
    }

    public function pinLength(): int
    {
        return max(4, min(6, strlen($this->currentPin())));
    }
}
