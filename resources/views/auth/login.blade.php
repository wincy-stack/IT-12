<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to Aqua De Smiley – Water Refilling Station management portal.">
    <title>Login – Aqua De Smiley</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #0ea5e9;
            --primary-dark: #0369a1;
            --accent:       #38bdf8;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: #0a1628;
            color: #e2e8f0;
        }

        /* ── BACKGROUND ── */
        .bg-image {
            position: fixed; inset: 0;
            background-image: url('/images/login-bg.png');
            background-size: cover;
            background-position: center;
            filter: brightness(.75) saturate(1.2);
            z-index: 0;
        }

        .bg-overlay {
            position: fixed; inset: 0;
            background: linear-gradient(
                135deg,
                rgba(2, 8, 30, 0.72) 0%,
                rgba(5, 25, 55, 0.55) 50%,
                rgba(2, 15, 40, 0.7) 100%
            );
            z-index: 1;
        }

        /* Animated floating bubbles */
        .bubble {
            position: fixed;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(56,189,248,.35), rgba(14,165,233,.05));
            border: 1px solid rgba(56,189,248,.2);
            animation: floatBubble linear infinite;
            z-index: 1;
        }
        .bubble:nth-child(1) { width: 100px; height: 100px; left: 10%; animation-duration: 14s; animation-delay: 0s; }
        .bubble:nth-child(2) { width: 60px;  height: 60px;  left: 25%; animation-duration: 10s; animation-delay: -4s; }
        .bubble:nth-child(3) { width: 140px; height: 140px; left: 70%; animation-duration: 18s; animation-delay: -6s; }
        .bubble:nth-child(4) { width: 40px;  height: 40px;  left: 85%; animation-duration: 9s;  animation-delay: -2s; }
        .bubble:nth-child(5) { width: 80px;  height: 80px;  left: 50%; animation-duration: 13s; animation-delay: -8s; }

        @keyframes floatBubble {
            0%   { transform: translateY(110vh) scale(1); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: .7; }
            100% { transform: translateY(-10vh) scale(1.1); opacity: 0; }
        }

        /* ── LOGIN CARD ── */
        .login-wrapper {
            position: relative; z-index: 10;
            width: 100%; max-width: 440px;
            padding: 1.5rem;
            animation: slideUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: rgba(8, 20, 50, 0.65);
            border: 1px solid rgba(56,189,248,.22);
            border-radius: 24px;
            padding: 2.5rem 2.25rem;
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            box-shadow:
                0 32px 80px rgba(0,0,0,.5),
                inset 0 1px 0 rgba(255,255,255,.08);
        }

        /* ── LOGO AREA ── */
        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 1rem;
            background: linear-gradient(135deg, rgba(14,165,233,.3), rgba(56,189,248,.15));
            border: 1px solid rgba(56,189,248,.4);
            display: grid; place-items: center;
            font-size: 2rem; color: var(--accent);
            position: relative;
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(56,189,248,.4); }
            50%       { box-shadow: 0 0 0 12px rgba(56,189,248,0); }
        }
        .logo-title {
            font-size: 1.7rem; font-weight: 800; letter-spacing: -.01em;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .logo-subtitle { color: rgba(148,163,184,.8); font-size: .85rem; margin-top: .3rem; font-weight: 400; }

        /* ── DIVIDER ── */
        .divider { border-top: 1px solid rgba(56,189,248,.15); margin: .5rem 0 1.75rem; }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block; font-size: .83rem; font-weight: 500;
            color: rgba(148,163,184,.9); margin-bottom: .45rem; letter-spacing: .01em;
        }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
            color: rgba(56,189,248,.5); font-size: .9rem; pointer-events: none;
            transition: color .2s;
        }
        .form-input {
            width: 100%; padding: .75rem .9rem .75rem 2.4rem;
            border-radius: 12px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            color: #e2e8f0; font-family: 'Outfit', sans-serif; font-size: .9rem;
            transition: border-color .25s, background .25s, box-shadow .25s;
        }
        .form-input:focus {
            outline: none;
            background: rgba(255,255,255,.1);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(56,189,248,.18);
        }
        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon { color: var(--accent); }
        .form-input::placeholder { color: rgba(148,163,184,.5); }

        /* password toggle */
        .eye-toggle {
            position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
            color: rgba(148,163,184,.5); cursor: pointer; font-size: .9rem; transition: color .2s;
            background: none; border: none; padding: 0;
        }
        .eye-toggle:hover { color: var(--accent); }

        .form-error { color: #fca5a5; font-size: .78rem; margin-top: .35rem; display: flex; align-items: center; gap: .3rem; }

        /* ── REMEMBER ROW ── */
        .remember-row {
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .remember-row input[type="checkbox"] { accent-color: var(--primary); width: 15px; height: 15px; }
        .remember-row label { color: rgba(148,163,184,.8); font-size: .83rem; cursor: pointer; }

        /* ── SUBMIT BTN ── */
        .btn-login {
            width: 100%; padding: .85rem 1rem;
            border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(14,165,233,.4);
            transition: all .25s;
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0; transition: opacity .25s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(14,165,233,.55); }
        .btn-login:hover::before { opacity: 1; }
        .btn-login:active { transform: translateY(0); }

        /* ── ROLE HINT ── */
        .role-hints {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 12px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
        }
        .role-hints-title { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: rgba(148,163,184,.6); margin-bottom: .75rem; }
        .role-row { display: flex; align-items: center; gap: .6rem; margin-bottom: .4rem; font-size: .8rem; }
        .role-row:last-child { margin-bottom: 0; }
        .role-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .role-dot--admin    { background: #a78bfa; }
        .role-dot--staff    { background: #38bdf8; }
        .role-dot--customer { background: #34d399; }
        .role-name { font-weight: 600; color: #e2e8f0; min-width: 70px; }
        .role-email { color: rgba(148,163,184,.7); font-family: monospace; font-size: .78rem; }

        /* ── FOOTER ── */
        .card-footer { text-align: center; margin-top: 1.5rem; color: rgba(148,163,184,.5); font-size: .78rem; }

        /* Alert */
        .alert-box {
            background: rgba(239,68,68,.15);
            border: 1px solid rgba(239,68,68,.35);
            border-radius: 10px;
            padding: .75rem 1rem;
            color: #fca5a5;
            font-size: .85rem;
            margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: .6rem;
        }
    </style>
</head>
<body>
    <!-- Background -->
    <div class="bg-image"></div>
    <div class="bg-overlay"></div>

    <!-- Floating bubbles -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-wrapper">
        <div class="login-card">

            <!-- Logo -->
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <div class="logo-title">Aqua De Smiley</div>
                <div class="logo-subtitle">Water Refilling Station Management Portal</div>
            </div>

            <div class="divider"></div>

            <!-- Error alert -->
            @if($errors->any())
                <div class="alert-box">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form id="login-form" action="{{ route('login.post') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-input"
                            placeholder="you@aquadesmiley.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                        <i class="fa-regular fa-envelope input-icon" style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);pointer-events:none;"></i>
                    </div>
                    @error('email')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper" style="position:relative;">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <i class="fa-regular fa-lock input-icon" style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);pointer-events:none;"></i>
                        <button type="button" class="eye-toggle" id="eye-toggle" onclick="togglePassword()" title="Show/hide password">
                            <i class="fa-regular fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login" id="submit-btn">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                </button>
            </form>

             <!-- Demo credentials hint 
            <div class="role-hints">
                <div class="role-hints-title"><i class="fa-solid fa-key" style="margin-right:.4rem"></i>Demo Accounts (password: <code style="color:#38bdf8">password</code>)</div>
                <div class="role-row">
                    <span class="role-dot role-dot--admin"></span>
                    <span class="role-name">Admin</span>
                    <span class="role-email">admin@aquadesmiley.com</span>
                </div>
                <div class="role-row">
                    <span class="role-dot role-dot--staff"></span>
                    <span class="role-name">Staff</span>
                    <span class="role-email">staff@aquadesmiley.com</span>
                </div>
                <div class="role-row">
                    <span class="role-dot role-dot--customer"></span>
                    <span class="role-name">Customer</span>
                    <span class="role-email">customer@aquadesmiley.com</span>
                </div>
            </div> -->

            <div class="card-footer">
                &copy; {{ date('Y') }} Aqua De Smiley · All rights reserved
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        // Loading state on submit
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In…';
            btn.disabled = true;
        });
    </script>
</body>
</html>
