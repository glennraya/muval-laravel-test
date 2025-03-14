<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginControllerApi extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(Auth::user());
        }

        return response()->json([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
