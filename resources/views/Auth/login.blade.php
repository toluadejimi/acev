<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — {{ config('app.name', 'Ace') }}</title>
    <link rel="icon" href="{{ url('') }}/public/assets/fav.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/css/auth-portal.css">
    @if(config('services.cloudflare.turnstile.site_key'))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</head>
<body class="auth-portal">
<div class="auth-portal__shell">
    <aside class="auth-portal__aside" aria-hidden="false">
        <div>
            <a href="{{ url('/') }}" class="auth-portal__brand">
                <span class="auth-portal__mark" aria-hidden="true">A</span>
                <span>{{ config('app.name', 'Ace') }}</span>
            </a>
            <h1>Welcome back</h1>
            <p class="lead">Sign in to manage VTU orders, fund your wallet, and receive SMS verifications in one place.</p>
            <ul class="auth-portal__highlights">
                <li><span class="auth-portal__dot" aria-hidden="true"></span> Airtime &amp; data from your balance</li>
                <li><span class="auth-portal__dot" aria-hidden="true"></span> Virtual numbers for OTPs</li>
                <li><span class="auth-portal__dot" aria-hidden="true"></span> Secure session &amp; wallet history</li>
            </ul>
        </div>
        <p class="auth-portal__foot">
            <a href="{{ url('/') }}">← Back to home</a>
        </p>
    </aside>

    <div class="auth-portal__main">
        <div class="auth-portal__card">
            <div class="auth-portal__card-head">
                <p class="auth-portal__eyebrow">Account</p>
                <h2>Log in</h2>
                <p>Enter your email and password to continue.</p>
            </div>

            @if ($errors->any())
                <div class="auth-portal__alert auth-portal__alert--error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="auth-portal__alert auth-portal__alert--success" role="status">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="auth-portal__alert auth-portal__alert--error" role="alert">
                    {{ session()->get('error') }}
                </div>
            @endif

            <form action="{{ url('/login_now') }}" method="post" id="login-form">
                @csrf

                <div class="auth-portal__field">
                    <label for="login-email">Email</label>
                    <input class="auth-portal__input" name="email" type="email" id="login-email" autocomplete="email"
                           placeholder="you@example.com" required autofocus value="{{ old('email') }}">
                </div>

                <div class="auth-portal__field">
                    <label for="login-password">Password</label>
                    <div class="auth-portal__pw-wrap">
                        <input class="auth-portal__input" name="password" type="password" id="login-password"
                               autocomplete="current-password" placeholder="Enter your password" required>
                        <button type="button" class="auth-portal__pw-toggle" id="toggle-password" aria-label="Show password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="auth-portal__row">
                    <label class="auth-portal__check">
                        <input type="checkbox" name="rememberMe" id="rememberMe" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ url('/forgot-password') }}">Forgot password?</a>
                </div>

                @if(config('services.cloudflare.turnstile.site_key'))
                    <div class="cf-turnstile auth-portal__turnstile"
                         data-sitekey="{{ config('services.cloudflare.turnstile.site_key') }}"
                         data-callback="onTurnstileSuccess">
                    </div>
                @endif

                <button type="submit" class="auth-portal__submit" id="login-submit" @if(config('services.cloudflare.turnstile.site_key')) disabled @endif>Log in</button>
            </form>

            <p class="auth-portal__bottom">
                Don’t have an account? <a href="{{ url('/register') }}">Create account</a>
            </p>
        </div>
    </div>
</div>

<script>
@if(config('services.cloudflare.turnstile.site_key'))
window.onTurnstileSuccess = function () {
    var btn = document.getElementById('login-submit');
    if (btn) btn.disabled = false;
};
@endif

(function () {
    var input = document.getElementById('login-password');
    var btn = document.getElementById('toggle-password');
    if (!input || !btn) return;
    btn.addEventListener('click', function () {
        var show = input.getAttribute('type') === 'password';
        input.setAttribute('type', show ? 'text' : 'password');
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
})();
</script>
</body>
</html>
