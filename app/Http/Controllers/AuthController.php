<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Hardcoded credentials
        if ($request->email === 'admin@gmail.com' && $request->password === 'admin') {
            Session::put('admin_authenticated', true);
            Session::put('admin_email', 'admin@gmail.com');
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        Session::forget('admin_authenticated');
        Session::forget('admin_email');
        return redirect()->route('login');
    }
}
