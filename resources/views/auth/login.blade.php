<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Gardakala Outdoor</title>
    <meta name="description" content="Masuk ke akun Gardakala Outdoor Anda.">
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
                    Nikmati petualangan alam terbuka bersama perlengkapan terbaik. 
                    Sewa alat outdoor berkualitas untuk perjalanan tak terlupakan Anda.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL: Login Form --}}
        <div class="auth-right">
            <div class="auth-form-wrapper">
                {{-- Header --}}
                <div class="auth-form-header">
                    <h1 class="auth-form-title">Welcome Back</h1>
                    <p class="auth-form-subtitle">Silakan masuk untuk melanjutkan petualangan Anda.</p>
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

                {{-- Login Form --}}
                <form method="POST" action="{{ url('/login') }}" id="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">Email atau Username</label>
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
                                autocomplete="email"
                            >
                        </div>
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
                                placeholder="••••••••"
                                required
                                minlength="6"
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="auth-options-row">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="auth-forgot-link">Lupa kata sandi?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="auth-btn-submit" id="btn-login">
                        Login <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <span class="auth-divider-text">Atau Masuk Dengan</span>
                    <div class="auth-divider-line"></div>
                </div>

                {{-- Social Login --}}
                <div class="auth-social-buttons">
                    <a href="{{ url('/auth/google') }}" class="auth-btn-social" id="btn-google-login">
                        <i class="fab fa-google"></i>
                        Google
                    </a>
                </div>

                {{-- Footer --}}
                <p class="auth-footer-text">
                    Belum punya akun? <a href="{{ url('/register') }}" class="auth-footer-link">Daftar di sini</a>
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
