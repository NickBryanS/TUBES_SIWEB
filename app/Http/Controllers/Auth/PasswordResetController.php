<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form lupa password (input email).
     */
    public function showForgotForm()
    {
        return view('auth.lupa-password');
    }

    /**
     * Proses kirim token reset password.
     * (Simulasi: token ditampilkan langsung karena belum ada mail server)
     */
    public function sendResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak terdaftar di sistem kami.',
        ]);

        // Generate token
        $token = strtoupper(Str::random(8));

        // Simpan ke password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Dalam produksi, token ini dikirim via email.
        // Untuk demo/development, kita tampilkan langsung.
        return back()->with([
            'success'    => 'Token reset password berhasil dibuat!',
            'reset_token' => $token,
            'reset_email' => $request->email,
        ]);
    }

    /**
     * Tampilkan form reset password (input token + password baru).
     */
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->query('email', ''),
            'token' => $request->query('token', ''),
        ]);
    }

    /**
     * Proses reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.exists'      => 'Email tidak terdaftar.',
            'token.required'    => 'Token wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        // Cari token di database
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Token tidak valid atau sudah kedaluwarsa.']);
        }

        // Cek apakah token sudah expired (60 menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Token sudah kedaluwarsa. Silakan minta token baru.']);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Token tidak valid.']);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Password berhasil direset! Silakan login dengan password baru.');
    }
}
