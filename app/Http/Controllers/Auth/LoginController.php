<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($redirect = $this->redirectIfAuthenticated($request)) {
            return $redirect;
        }

        if ($this->isFloatLogin($request)) {
            return view('auth.login-float');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $stored = $user->password;
            $plain  = $data['password'];
            $ok     = false;

            if (is_string($stored) && str_starts_with($stored, '$2y$')) {
                $ok = Hash::check($plain, $stored);
            } else {
                $ok = hash_equals($stored, $plain);
            }

            if ($ok) {
                if ($user->role === 'guard') {
                    return redirect()
                        ->route('login')
                        ->withErrors(['email' => 'Guard accounts cannot log in. Use the kiosk PIN to issue walk-in tickets.'])
                        ->withInput($request->only('email', 'float_login'));
                }

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                return $this->redirectAfterLogin($user, $request);
            }
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->withInput($request->only('email', 'float_login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $fromFloat = $request->boolean('float_logout')
            || str_contains((string) $request->headers->get('referer'), '/window/float');

        if ($fromFloat) {
            return redirect()->route('login', ['float' => 1]);
        }

        return redirect()->route('login');
    }

    private function isFloatLogin(Request $request): bool
    {
        if ($request->boolean('float') || $request->boolean('float_login') || old('float_login')) {
            return true;
        }

        $intended = (string) $request->session()->get('url.intended', '');

        return str_contains($intended, '/window/float');
    }

    private function redirectIfAuthenticated(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->role === 'guard') {
            return null;
        }

        return redirect()->to($this->homePath($user, $request));
    }

    private function redirectAfterLogin(User $user, Request $request)
    {
        $fallback = $this->homePath($user, $request);
        $intended = (string) $request->session()->pull('url.intended', '');

        if ($this->intendedAllowed($user, $intended, $request)) {
            return redirect()->to($intended);
        }

        return redirect()->to($fallback);
    }

    private function homePath(User $user, Request $request): string
    {
        if ($user->role === 'admin') {
            return url('/admin');
        }

        if ($user->role === 'staff') {
            if ($request->boolean('float_login') || $request->boolean('float')) {
                return route('window.float');
            }

            return url('/window');
        }

        return url('/');
    }

    private function intendedAllowed(User $user, string $intended, Request $request): bool
    {
        if ($intended === '' || str_starts_with($intended, '//')) {
            return false;
        }

        $parts = parse_url($intended);
        if ($parts === false) {
            return false;
        }

        if (isset($parts['host']) && strcasecmp((string) $parts['host'], $request->getHost()) !== 0) {
            return false;
        }

        $path = (string) ($parts['path'] ?? '');
        $prefixes = match ($user->role) {
            'admin' => ['admin', 'guard'],
            'staff' => ['window'],
            default => [],
        };

        foreach ($prefixes as $prefix) {
            if (preg_match('#(?:^|/)'.preg_quote($prefix, '#').'(?:/|$)#', $path) === 1) {
                return true;
            }
        }

        return false;
    }
}

