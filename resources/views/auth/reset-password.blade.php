<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Gardakala Outdoor</title>
    <meta name="description" content="Atur ulang password akun Gardakala Outdoor Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-page">
        {{-- LEFT PANEL: Image + Branding --}}
        <div class="auth-left">
            <div class="auth-left-bg">
                <img src="{{ asset('images/login-mountain.png') }}" alt="Mountain landscape">
            </div>

            <div class="auth-left-content">
                <div class="auth-brand-logo">
                    GARDAKALA <span>OUTDOOR</span>
                </div>
                <p class="auth-brand-desc">
                    Masukkan token reset dan password baru Anda 
                    untuk kembali mengakses akun petualangan Anda.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL: Reset Password Form --}}
        <div class="auth-right">
            <div class="auth-form-wrapper">
                {{-- Header --}}
                <div class="auth-form-header">
                    <h1 class="auth-form-title">Reset Password</h1>
                    <p class="auth-form-subtitle">Masukkan token dan buat password baru untuk akun Anda.</p>
                </div>

                {{-- Success Alert --}}
                @if(session('success'))
                    <div class="auth-alert auth-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="auth-alert auth-alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.reset') }}" id="reset-form">
                    @csrf

                    {{-- Email --}}
                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">Alamat Email</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="auth-input" placeholder="nama@email.com" value="{{ old('email', $email ?? '') }}" required autocomplete="email">
                        </div>
                    </div>

                    {{-- Token --}}
                    <div class="auth-form-group">
                        <label for="token" class="auth-form-label">Token Reset</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-key input-icon"></i>
                            <input type="text" id="token" name="token" class="auth-input" placeholder="Masukkan token 8 karakter" value="{{ old('token', $token ?? '') }}" required style="font-family: 'Courier New', monospace; letter-spacing: 0.1em; font-weight: 700;">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="auth-form-group">
                        <label for="password" class="auth-form-label">Password Baru</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" class="auth-input" placeholder="Minimal 6 karakter" required minlength="6" autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="auth-form-group">
                        <label for="password_confirmation" class="auth-form-label">Konfirmasi Password</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input" placeholder="Ulangi password baru" required autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn-submit" id="btn-reset">
                        <i class="fas fa-shield-alt"></i> Reset Password
                    </button>
                </form>

                {{-- Footer --}}
                <p class="auth-footer-text">
                    Belum punya token? <a href="{{ route('password.forgot') }}" class="auth-footer-link">Minta Token</a>
                </p>
                <p class="auth-footer-text" style="margin-top: 10px;">
                    Ingat password? <a href="/login" class="auth-footer-link">Kembali ke Login</a>
                </p>
            </div>

            <div class="auth-copyright">
                &copy; {{ date('Y') }} Gardakala Outdoor
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
