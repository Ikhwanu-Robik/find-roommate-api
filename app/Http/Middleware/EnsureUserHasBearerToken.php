<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 * Laravel's Feature test simulate a request with 
 * "the same domain as the Laravel application".
 * As such, after a successful login is simulated,
 * and a request to sanctum-protected route is made,
 * it will pass because the request "is made from the 
 * same domain".
 * 
 * It is said that Sanctum typically uses web middleware
 * to check for this "same-domain request".
 * I tried removing web middleware from my selected route,
 * and a request without bearer token still passes.
 * 
 * In the end, I resolve to just make this middleware
 * to ensure the request includes a bearer token.
*/
class EnsureUserHasBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->header('Authorization');
        if (!$authorization) {
            abort(401);
        }
        return $next($request);
    }
}
