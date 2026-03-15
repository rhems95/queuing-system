<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
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
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                if ($user->role === 'admin') {
                    return redirect()->intended('/admin');
                } elseif ($user->role === 'staff') {
                    return redirect()->intended('/window');
                }

                return redirect()->intended('/');
            }
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

