<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback dari Google.
     * - Ambil data user dari Google (name, email, google_id, avatar)
     * - Jika email sudah ada di DB → login langsung
     * - Jika belum → buat akun baru, lalu login
     * - Simpan google_id & avatar ke tabel users
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect('/login')
                ->withErrors(['google' => 'Gagal login dengan Google: ' . $e->getMessage()]);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // User sudah ada → update google_id & avatar, lalu login
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        } else {
            // User belum ada → buat akun baru
            $user = User::create([
                'nama_lengkap' => $googleUser->getName(),
                'email'        => $googleUser->getEmail(),
                'google_id'    => $googleUser->getId(),
                'avatar'       => $googleUser->getAvatar(),
                'password'     => bcrypt(uniqid()), // random password untuk OAuth user
            ]);
        }

        Auth::login($user, true);

        // Jika user adalah admin, redirect ke admin dashboard
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard')
                ->with('success', 'Selamat datang, Admin!');
        }

        return redirect('/dashboard')
            ->with('success', 'Berhasil login dengan Google!');
    }
}
