<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Verify — {{ config('app.name', 'Ace') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/css/landing-modern.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        .lm-gate { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; background: var(--paper); }
        .lm-gate__card {
            width: 100%; max-width: 420px; background: var(--white); border-radius: var(--radius-lg);
            box-shadow: var(--shadow); padding: 2rem 1.75rem; text-align: center;
            border: 1px solid var(--line);
        }
        .lm-gate__card h1 { font-family: var(--font-serif); font-size: 1.75rem; margin: 0 0 0.5rem; color: var(--ink); }
        .lm-gate__card p { margin: 0 0 1.25rem; color: var(--ink-muted); font-size: 0.95rem; }
        .lm-gate__turnstile { display: flex; justify-content: center; margin-bottom: 1.25rem; min-height: 65px; }
        .lm-gate__submit {
            width: 100%; border: none; border-radius: var(--radius); padding: 0.85rem 1.25rem;
            font-family: var(--font-sans); font-weight: 600; font-size: 1rem; cursor: pointer;
            background: linear-gradient(135deg, var(--blue), var(--blue-deep)); color: var(--white);
        }
        .lm-gate__submit:disabled { opacity: 0.45; cursor: not-allowed; }
        .lm-gate__alert { background: var(--red-soft); color: var(--red-deep); padding: 0.75rem 1rem; border-radius: var(--radius); margin-bottom: 1rem; font-size: 0.9rem; text-align: left; }
    </style>
</head>
<body class="landing-modern">
<div class="lm-gate">
    <div class="lm-gate__card">
        <h1>Quick check</h1>
        <p>Confirm you are human to continue to the site.</p>

        @if (session()->has('error'))
            <div class="lm-gate__alert" role="alert">{{ session('error') }}</div>
        @endif

        <form action="{{ route('landing.human-check.verify') }}" method="post">
            @csrf
            <div class="cf-turnstile lm-gate__turnstile"
                 data-sitekey="{{ config('services.cloudflare.turnstile.site_key') }}"
                 data-callback="onLandingTurnstileSuccess"></div>
            <button type="submit" class="lm-gate__submit" id="landing-gate-submit" disabled>Continue</button>
        </form>
    </div>
</div>
<script>
window.onLandingTurnstileSuccess = function () {
    var btn = document.getElementById('landing-gate-submit');
    if (btn) btn.disabled = false;
};
</script>
</body>
</html>
