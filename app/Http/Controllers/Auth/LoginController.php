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

                if ($user->role === 'admin') {
                    return redirect()->intended('/admin');
                } elseif ($user->role === 'staff') {
                    if ($request->boolean('float_login')) {
                        return redirect()->intended(route('window.float'));
                    }

                    return redirect()->intended('/window');
                }

                return redirect()->intended('/');
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
}

