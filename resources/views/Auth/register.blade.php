<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account — {{ config('app.name', 'Ace') }}</title>
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
            <h1>Create your account</h1>
            <p class="lead">Join others using one wallet for VTU top-ups and SMS verification numbers — fast checkout and clear pricing.</p>
            <ul class="auth-portal__highlights">
                <li><span class="auth-portal__dot" aria-hidden="true"></span> One dashboard for airtime, data &amp; OTPs</li>
                <li><span class="auth-portal__dot" aria-hidden="true"></span> Fund your wallet and track every order</li>
                <li><span class="auth-portal__dot" aria-hidden="true"></span> Built for individuals and small teams</li>
            </ul>
        </div>
        <p class="auth-portal__foot">
            <a href="{{ url('/') }}">← Back to home</a>
        </p>
    </aside>

    <div class="auth-portal__main">
        <div class="auth-portal__card auth-portal__card--register">
            <div class="auth-portal__card-head">
                <p class="auth-portal__eyebrow">Account</p>
                <h2>Sign up</h2>
                <p>Choose a username, email, and password to get started.</p>
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

            <form action="{{ url('/register_now') }}" method="post" id="register-form">
                @csrf

                <div class="auth-portal__field">
                    <label for="register-username">Username</label>
                    <input class="auth-portal__input" type="text" name="username" id="register-username" autocomplete="username"
                           placeholder="Choose a username" required autofocus value="{{ old('username') }}">
                </div>

                <div class="auth-portal__field">
                    <label for="register-email">Email</label>
                    <input class="auth-portal__input" name="email" type="email" id="register-email" autocomplete="email"
                           placeholder="you@example.com" required value="{{ old('email') }}">
                </div>

                <div class="auth-portal__field">
                    <label for="register-password">Password</label>
                    <div class="auth-portal__pw-wrap">
                        <input class="auth-portal__input" name="password" type="password" id="register-password"
                               autocomplete="new-password" placeholder="Create a password" required>
                        <button type="button" class="auth-portal__pw-toggle" id="toggle-register-password" aria-label="Show password">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="auth-portal__field">
                    <label for="register-password-confirm">Confirm password</label>
                    <div class="auth-portal__pw-wrap">
                        <input class="auth-portal__input" name="password_confirmation" type="password" id="register-password-confirm"
                               autocomplete="new-password" placeholder="Repeat your password" required>
                        <button type="button" class="auth-portal__pw-toggle" id="toggle-register-password-confirm" aria-label="Show password confirmation">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                @if(config('services.cloudflare.turnstile.site_key'))
                    <div class="cf-turnstile auth-portal__turnstile"
                         data-sitekey="{{ config('services.cloudflare.turnstile.site_key') }}"
                         data-callback="onRegisterTurnstileSuccess">
                    </div>
                @endif

                <button type="submit" class="auth-portal__submit" id="register-submit" @if(config('services.cloudflare.turnstile.site_key')) disabled @endif>Create account</button>
            </form>

            <p class="auth-portal__bottom">
                Already have an account? <a href="{{ route('login') }}">Log in</a>
            </p>
        </div>
    </div>
</div>

<script>
@if(config('services.cloudflare.turnstile.site_key'))
window.onRegisterTurnstileSuccess = function () {
    var btn = document.getElementById('register-submit');
    if (btn) btn.disabled = false;
};
@endif

(function () {
    function bindToggle(btnId, inputId, labelShow, labelHide) {
        var input = document.getElementById(inputId);
        var btn = document.getElementById(btnId);
        if (!input || !btn) return;
        btn.addEventListener('click', function () {
            var show = input.getAttribute('type') === 'password';
            input.setAttribute('type', show ? 'text' : 'password');
            btn.setAttribute('aria-label', show ? labelHide : labelShow);
        });
    }
    bindToggle('toggle-register-password', 'register-password', 'Show password', 'Hide password');
    bindToggle('toggle-register-password-confirm', 'register-password-confirm', 'Show password confirmation', 'Hide password confirmation');
})();
</script>
</body>
</html>
