<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiGuardResolver
{
    /**
     * Resolve default authentication guard dynamically between 'web' (admin) and 'warga' (citizen).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('warga')->check()) {
            Auth::shouldUse('warga');
        } elseif (Auth::guard('web')->check()) {
            Auth::shouldUse('web');
        }

        return $next($request);
    }
}
