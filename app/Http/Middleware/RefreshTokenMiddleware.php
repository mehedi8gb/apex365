<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Check if the token is valid
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            // If token is expired, refresh it
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                try {
                    $newToken = JWTAuth::refresh();
                    // Add the refreshed token to the response header
                    $response = $next($request);
                    $response->headers->set('Authorization', 'Bearer ' . $newToken);
                    return $response;
                } catch (JWTException $e) {
                    // Unable to refresh the token
                    return response()->json(['error' => 'Token could not be refreshed'], 401);
                }
            }

            // Invalid token
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        return $next($request);
    }
}
