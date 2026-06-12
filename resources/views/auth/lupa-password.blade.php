<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Gardakala Outdoor</title>
    <meta name="description" content="Reset password akun Gardakala Outdoor Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .reset-token-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 2px dashed #2D5A27;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 18px;
            animation: authFadeInUp 0.5s ease-out;
        }
        .reset-token-box .token-label {
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .reset-token-box .token-code {
            font-size: 1.6rem;
            font-weight: 800;
            color: #2D5A27;
            letter-spacing: 0.15em;
            font-family: 'Courier New', monospace;
            margin-bottom: 8px;
        }
        .reset-token-box .token-hint {
            font-size: 0.72rem;
            color: #9ca3af;
            margin-bottom: 14px;
        }
        .btn-go-reset {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: #2D5A27;
            color: white;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-go-reset:hover {
            background: #1a3a17;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(45,90,39,0.3);
        }
    </style>
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
                    Lupa password? Tenang, kami bantu untuk mengatur ulang 
                    password akun Anda agar bisa kembali berpetualang.
                </p>
            </div>
        </div>

        {{-- RIGHT PANEL: Forgot Password Form --}}
        <div class="auth-right">
            <div class="auth-form-wrapper">
                {{-- Header --}}
                <div class="auth-form-header">
                    <h1 class="auth-form-title">Lupa Password?</h1>
                    <p class="auth-form-subtitle">Masukkan email Anda dan kami akan mengirimkan token untuk reset password.</p>
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

                {{-- Token Display (after generate) --}}
                @if(session('reset_token'))
                    <div class="reset-token-box">
                        <div class="token-label">Token Reset Password Anda</div>
                        <div class="token-code">{{ session('reset_token') }}</div>
                        <div class="token-hint">Salin token ini dan gunakan untuk mereset password</div>
                        <a href="{{ route('password.reset.form', ['email' => session('reset_email'), 'token' => session('reset_token')]) }}" class="btn-go-reset">
                            <i class="fas fa-arrow-right"></i> Reset Password Sekarang
                        </a>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('password.send-token') }}" id="forgot-form">
                    @csrf

                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">Alamat Email</label>
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
                                autofocus
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <button type="submit" class="auth-btn-submit" id="btn-send-token">
                        <i class="fas fa-paper-plane"></i> Kirim Token Reset
                    </button>
                </form>

                {{-- Divider --}}
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <span class="auth-divider-text">Opsi Lainnya</span>
                    <div class="auth-divider-line"></div>
                </div>

                {{-- Links --}}
                <p class="auth-footer-text" style="margin-top: 0;">
                    Sudah punya token? <a href="{{ route('password.reset.form') }}" class="auth-footer-link">Reset Password</a>
                </p>
                <p class="auth-footer-text" style="margin-top: 10px;">
                    Ingat password Anda? <a href="/login" class="auth-footer-link">Kembali ke Login</a>
                </p>
            </div>

            <div class="auth-copyright">
                &copy; {{ date('Y') }} Gardakala Outdoor
            </div>
        </div>
    </div>
</body>
</html>
