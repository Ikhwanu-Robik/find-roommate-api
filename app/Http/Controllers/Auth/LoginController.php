<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = $this->attemptLogin($credentials);
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
    }

    private function attemptLogin(array $credentials)
    {
        $user = User::where('phone', $credentials['phone'])->with('profile')->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            abort(401, 'The credentials do not match our record');
        }

        return $user;
    }
}
