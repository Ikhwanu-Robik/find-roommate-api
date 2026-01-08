<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class StatefulLoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();

            return response()->json(['message' => 'Login successful']);
        }

        return response()->json([
            'message' => 'The credentials do not match our record'
        ], 401);
    }
}