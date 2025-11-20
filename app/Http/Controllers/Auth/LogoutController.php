<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $token = $user->currentAccessToken();
        $token->delete();
        return response(['message' => 'Logout successful']);
    }
}
