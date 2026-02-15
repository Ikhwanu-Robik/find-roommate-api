<?php

namespace App\Http\Controllers\Auth;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GoogleSignInController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'credential' => ['required', 'string']
        ]);

        $keys = json_decode(
            file_get_contents('https://www.googleapis.com/oauth2/v3/certs'),
            true
        );

        $decoded = JWT::decode($request->credential, JWK::parseKeySet($keys));

        if ($decoded->aud !== config('services.google.client_id')) {
            abort(401);
        }

        if (
            !in_array($decoded->iss, [
                'accounts.google.com',
                'https://accounts.google.com'
            ])
        ) {
            abort(401);
        }

        $user = User::firstOrCreate(
            ['google_id' => $decoded->sub],
            [
                'name' => $decoded->name,
                'email' => $decoded->email,
                'password' => bcrypt(str()->random(32)),
            ]
        );

        Auth::login($user);

        return response()->json([
            'user' => $user
        ]);
    }
}