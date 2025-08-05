<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $payload = JWTAuth::parseToken()->getPayload();

            $issuedAt = $payload->get('iat');
            $userUpdatedAt = $user->updated_at?->timestamp;

            if ($userUpdatedAt && $userUpdatedAt > $issuedAt) {
                return sendErrorResponse('Token invalid due to recent account update. Please login again.', 401);
            }
        } catch (JWTException $e) {
            return sendErrorResponse('Token not valid', 401);
        }

        return $next($request);
    }
}
