<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi - Gardakala Outdoor</title>
    <meta name="description" content="Buat akun Gardakala Outdoor untuk mulai menyewa alat outdoor.">
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
                <img src="{{ asset('images/register-forest.png') }}" alt="Forest landscape">
            </div>

            <div class="auth-left-content">
                <div class="auth-brand-logo">
                    GARDAKALA <span>OUTDOOR</span>
                </div>
                <p class="auth-brand-desc">
                    Bergabunglah dengan ribuan petualang yang telah mempercayakan kebutuhan outdoor mereka kepada kami. 
                    Perlengkapan profesional untuk petualangan tak terlupakan.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL: Register Form --}}
        <div class="auth-right">
            <div class="auth-form-wrapper">
                {{-- Header --}}
                <div class="auth-form-header">
                    <h1 class="auth-form-title">Get Started</h1>
                    <p class="auth-form-subtitle">Lengkapi data di bawah ini untuk membuat akun baru.</p>
                </div>

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

                {{-- Register Form --}}
                <form method="POST" action="{{ url('/register') }}" id="register-form">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div class="auth-form-group">
                        <label for="name" class="auth-form-label">Nama Lengkap</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="auth-input" 
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('name') }}"
                                required
                                maxlength="100"
                                autocomplete="name"
                            >
                        </div>
                        @error('name')
                            <div class="auth-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">Email</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="auth-input" 
                                placeholder="nama@email.com"
                                value="{{ old('email') }}"
                                required
                                maxlength="255"
                                autocomplete="email"
                            >
                        </div>
                        @error('email')
                            <div class="auth-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="auth-form-group">
                        <label for="password" class="auth-form-label">Kata Sandi</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="auth-input" 
                                placeholder="Minimal 6 karakter"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="auth-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div class="auth-form-group">
                        <label for="password_confirmation" class="auth-form-label">Konfirmasi Kata Sandi</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-shield-halved input-icon"></i>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="auth-input" 
                                placeholder="Ulangi kata sandi"
                                required
                                minlength="6"
                                autocomplete="new-password"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div class="auth-form-group">
                        <label class="auth-remember">
                            <input type="checkbox" name="terms" required>
                            <span>Saya menyetujui <a href="#" class="auth-forgot-link">Syarat & Ketentuan</a> serta <a href="#" class="auth-forgot-link">Kebijakan Privasi</a></span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="auth-btn-submit" id="btn-register">
                        Buat Akun <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <span class="auth-divider-text">Atau Daftar Dengan</span>
                    <div class="auth-divider-line"></div>
                </div>

                {{-- Social Register --}}
                <div class="auth-social-buttons">
                    <a href="{{ url('/auth/google') }}" class="auth-btn-social" id="btn-google-register">
                        <i class="fab fa-google"></i>
                        Google
                    </a>
                </div>

                {{-- Footer --}}
                <p class="auth-footer-text">
                    Sudah punya akun? <a href="{{ url('/login') }}" class="auth-footer-link">Masuk di sini</a>
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
