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
        // 1. Panel Pengurus / Admin CMS: Selalu utamakan guard 'web' (Admin/Pengurus RT)
        if ($request->is('admin*') || $request->is('cms*')) {
            if (Auth::guard('web')->check()) {
                Auth::shouldUse('web');
                return $next($request);
            }
        }

        // 2. Request Livewire di dalam panel admin: Utamakan guard 'web'
        if ($request->is('livewire*')) {
            $referer = $request->headers->get('referer', '');
            $isFilamentAdmin = str_contains($referer, '/admin') 
                || str_contains($referer, '/cms') 
                || str_contains($request->getContent(), 'Filament')
                || str_contains($request->getContent(), 'App\\\\Filament');

            if ($isFilamentAdmin && Auth::guard('web')->check()) {
                Auth::shouldUse('web');
                return $next($request);
            } elseif (Auth::guard('web')->check() && !Auth::guard('warga')->check()) {
                Auth::shouldUse('web');
                return $next($request);
            } elseif (Auth::guard('warga')->check()) {
                Auth::shouldUse('warga');
                return $next($request);
            }
        }

        // 3. Portal Warga & Layanan Mandiri: Utamakan guard 'warga'
        if ($request->is('portal*') || $request->is('layanan*') || $request->is('bantuan*') || $request->is('surat*') || $request->is('aspirasi*') || $request->is('transparansi*') || $request->is('agenda*')) {
            if (Auth::guard('warga')->check()) {
                Auth::shouldUse('warga');
                return $next($request);
            }
        }

        // 4. Default fallback jika rute umum
        if (Auth::guard('web')->check()) {
            Auth::shouldUse('web');
        } elseif (Auth::guard('warga')->check()) {
            Auth::shouldUse('warga');
        }

        return $next($request);
    }
}
