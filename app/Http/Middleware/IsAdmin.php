<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika user sudah login dan adalah admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Jika bukan admin, redirect ke admin login
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }

        // Jika sudah login tapi bukan admin
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('error', 'Anda tidak memiliki akses admin.');
    }
}
