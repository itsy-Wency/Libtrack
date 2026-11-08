<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Show the sign-in form. */
    public function form()
    {
        return view('login');
    }

    /** Validate credentials, start an authenticated session, and record the sign-in. */
    public function login(Request $r, ActivityLogger $log)
    {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (! Auth::attempt($data)) {
            return back()->withErrors(['email' => 'Invalid login details.']);
        }

        $r->session()->regenerate();
        $log->write('LOGIN', 'User signed in.');

        return redirect()->route('catalog');
    }

    /** End the current session and record the sign-out before redirecting. */
    public function logout(Request $r, ActivityLogger $log)
    {
        $log->write('LOGOUT', 'User signed out.');
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('login');
    }
}
