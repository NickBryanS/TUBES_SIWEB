<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Jika admin sudah login, langsung redirect ke admin dashboard.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login (validasi email & password, buat session, redirect ke dashboard).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Jika user adalah superadmin (pemilik), redirect ke executive dashboard
            if ($user->isSuperAdmin()) {
                return redirect()->intended('/superadmin/dashboard')
                    ->with('success', 'Selamat datang, Pemilik!');
            }

            // Jika user adalah admin, redirect ke admin dashboard
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard')
                    ->with('success', 'Selamat datang, Admin!');
            }

            // Jika user biasa, redirect ke user dashboard
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali!');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    /**
     * Tampilkan halaman register.
     * Admin yang sudah login langsung redirect ke admin dashboard.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/dashboard');
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi (validasi input, hash password, simpan user, auto login).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'name.max'           => 'Nama maksimal 100 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Hapus session, redirect ke login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda telah keluar.');
    }
}
