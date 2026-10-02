<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Check authentication across web (admin) and warga guards
        $user = auth('web')->user() ?? auth('warga')->user();
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Silakan masuk terlebih dahulu.',
                ], 401);
            }

            return redirect()->guest(route('login'))->with('error', 'Sesi Anda belum aktif atau telah berakhir. Silakan masuk terlebih dahulu.');
        }

        $userRole = str_replace('-', '_', strtolower($user->role ?? ''));

        // 2. Parse and normalize allowed roles
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $trimmed = str_replace('-', '_', strtolower(trim($r)));
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // 3. Verify user role
        if (!in_array($userRole, $allowedRoles)) {
            // Citizen trying to access CMS routes
            if ($userRole === 'warga') {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Akses ditolak: Akun Warga tidak memiliki izin ke panel CMS Pengurus.',
                    ], 403);
                }

                return redirect()->route('portal.home')->with('error', 'Akses ditolak: Akun Warga hanya memiliki otorisasi pada Portal Layanan Mandiri.');
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => "Akses ditolak: Peran '{$userRole}' tidak memiliki hak akses ke modul ini.",
                    'required_roles' => $allowedRoles,
                    'current_role' => $userRole,
                ], 403);
            }

            abort(403, "Akses ditolak: Peran Anda ('{$userRole}') tidak memiliki otorisasi untuk mengakses halaman ini.");
        }

        return $next($request);
    }
}
