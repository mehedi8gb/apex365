<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelescopeAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('telescope/*')) {
            return $next($request);
        }

        $secret = config('apex365.service.telescope_secret_key');
        $header = $request->header('X-TELESCOPE-KEY');

        if (! $header || ! hash_equals($secret, $header)) {
            abort(403, 'Unauthorized Telescope access');
        }

        return $next($request);
    }
}
