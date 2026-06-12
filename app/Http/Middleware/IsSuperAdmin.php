<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Hanya superadmin (pemilik) yang bisa mengakses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai Super Admin.');
        }

        // Jika admin biasa, redirect ke admin dashboard
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses Super Admin.');
        }

        // Jika user biasa, redirect ke dashboard user
        return redirect('/dashboard')
            ->with('error', 'Anda tidak memiliki akses.');
    }
}
