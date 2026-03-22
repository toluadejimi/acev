<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VTU airtime & data, plus reliable SMS verification numbers. Fund your wallet, order in seconds.">
    <title>{{ config('app.name', 'Ace') }} — VTU &amp; SMS verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/css/landing-modern.css">
</head>
<body class="landing-modern">
<a class="lm-skip" href="#main">Skip to content</a>

<header class="lm-nav" role="banner">
    <div class="lm-nav__inner">
        <a href="{{ url('/') }}" class="lm-brand" aria-label="Home">
            <span class="lm-brand__mark" aria-hidden="true">A</span>
            <span>{{ config('app.name', 'Ace') }}</span>
        </a>
        <button type="button" class="lm-nav__toggle" id="lm-menu-toggle" aria-expanded="false" aria-controls="lm-drawer" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
        <div class="lm-nav__drawer" id="lm-drawer">
            <ul class="lm-nav__links">
                <li><a href="#services">Services</a></li>
                <li><a href="#how">How it works</a></li>
                <li><a href="{{ url('/faq') }}">FAQ</a></li>
            </ul>
            <div class="lm-nav__actions">
                @auth
                    <a href="{{ url('/home') }}" class="lm-btn lm-btn--primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="lm-btn lm-btn--ghost">Log in</a>
                    <a href="{{ url('/register') }}" class="lm-btn lm-btn--primary">Create account</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<main id="main">
    <section class="lm-hero" aria-labelledby="hero-heading">
        <div class="lm-hero__grid">
            <div>
                <p class="lm-eyebrow">Digital utilities &amp; identity</p>
                <h1 id="hero-heading">VTU &amp; SMS verification, <span class="lm-gradient-word">elevated.</span></h1>
                <p class="lm-hero__lead">
                    Top up airtime and data bundles at competitive rates, and receive OTPs on dedicated numbers for the apps and services you trust — one wallet, one dashboard.
                </p>
                <div class="lm-hero__cta">
                    @auth
                        <a href="{{ url('/home') }}" class="lm-btn lm-btn--primary lm-btn--lg">Open dashboard</a>
                        <a href="{{ url('/fund-wallet') }}" class="lm-btn lm-btn--ghost lm-btn--lg">Fund wallet</a>
                    @else
                        <a href="{{ url('/register') }}" class="lm-btn lm-btn--primary lm-btn--lg">Get started</a>
                        <a href="{{ route('login') }}" class="lm-btn lm-btn--ghost lm-btn--lg">I have an account</a>
                    @endauth
                </div>
                <div class="lm-trust">
                    <div class="lm-trust__item">
                        <strong>Wallet-based</strong>
                        Fund once, use for VTU and verifications
                    </div>
                    <div class="lm-trust__item">
                        <strong>Built for speed</strong>
                        Orders and delivery without friction
                    </div>
                    <div class="lm-trust__item">
                        <strong>Support</strong>
                        Help when you need it
                    </div>
                </div>
            </div>
            <div class="lm-hero-cards" id="services">
                <article class="lm-card lm-card--blue">
                    <div class="lm-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                    </div>
                    <h2>VTU services</h2>
                    <p>Airtime, data, and digital vouchers delivered from your balance. Ideal for personal use or resale — clear pricing and instant fulfilment where available.</p>
                    @auth
                        <a href="{{ url('/home') }}" class="lm-card__link">VTU from dashboard <span aria-hidden="true">→</span></a>
                    @else
                        <a href="{{ url('/register') }}" class="lm-card__link">Sign up to buy <span aria-hidden="true">→</span></a>
                    @endauth
                </article>
                <article class="lm-card lm-card--red">
                    <div class="lm-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <path d="M8 10h.01M12 10h.01M16 10h.01"/>
                        </svg>
                    </div>
                    <h2>SMS verification</h2>
                    <p>Virtual numbers for OTPs and app sign-ups. Choose regions and services, receive codes in your dashboard — no second SIM required.</p>
                    @auth
                        <a href="{{ url('/home') }}" class="lm-card__link">Order a number <span aria-hidden="true">→</span></a>
                    @else
                        <a href="{{ url('/register') }}" class="lm-card__link">Start verifying <span aria-hidden="true">→</span></a>
                    @endauth
                </article>
            </div>
        </div>
    </section>

    <section class="lm-section lm-section--alt" aria-labelledby="pillars-heading">
        <div class="lm-section__head">
            <h2 id="pillars-heading">Why teams and individuals choose us</h2>
            <p>We combine utility payments and phone verification in a single, calm experience — no clutter, no noise.</p>
        </div>
        <div class="lm-features">
            <div class="lm-feature lm-feature--blue">
                <div class="lm-feature__num">01</div>
                <h3>One balance</h3>
                <p>Your wallet powers both VTU purchases and SMS verification orders. Top up through supported channels and track every transaction.</p>
            </div>
            <div class="lm-feature lm-feature--blend">
                <div class="lm-feature__num">02</div>
                <h3>Transparent flow</h3>
                <p>See what you are buying before you commit. Clear steps from selection to delivery, with history you can audit.</p>
            </div>
            <div class="lm-feature lm-feature--red">
                <div class="lm-feature__num">03</div>
                <h3>Focused product</h3>
                <p>We are built around airtime, data, and verification — not a marketplace of unrelated extras. That focus keeps the product fast and dependable.</p>
            </div>
        </div>
    </section>

    <section class="lm-section" id="how" aria-labelledby="how-heading">
        <div class="lm-section__head">
            <h2 id="how-heading">How it works</h2>
            <p>Three steps from signup to your first successful order.</p>
        </div>
        <div class="lm-split">
            <div class="lm-panel lm-panel--blue">
                <h3>VTU in brief</h3>
                <ul>
                    <li>Create your account and verify your email if required.</li>
                    <li>Fund your wallet using the methods shown in the dashboard.</li>
                    <li>Open the product catalog, pick airtime or data, and complete checkout.</li>
                    <li>Receive delivery details or PINs according to the product type.</li>
                </ul>
            </div>
            <div class="lm-panel lm-panel--red">
                <h3>SMS verification in brief</h3>
                <ul>
                    <li>Ensure your wallet has enough balance for the number you need.</li>
                    <li>Select a country and supported service (e.g. social or messaging apps).</li>
                    <li>Order a number and use it where the platform asks for a phone.</li>
                    <li>Watch the dashboard for incoming OTPs; refresh or extend as the product allows.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="lm-section lm-section--alt">
        <div class="lm-cta-band">
            <h2>Ready when you are</h2>
            <p>Open an account in minutes and explore VTU inventory alongside SMS verification options from your dashboard.</p>
            @auth
                <a href="{{ url('/home') }}" class="lm-btn lm-btn--primary lm-btn--lg">Go to dashboard</a>
            @else
                <a href="{{ url('/register') }}" class="lm-btn lm-btn--primary lm-btn--lg">Create free account</a>
            @endauth
        </div>
    </section>
</main>

<footer class="lm-footer" role="contentinfo">
    <div class="lm-footer__inner">
        <div class="lm-footer__brand">{{ config('app.name', 'Ace') }}</div>
        <nav class="lm-footer__links" aria-label="Footer">
            <a href="{{ url('/faq') }}">FAQ</a>
            <a href="{{ url('/terms') }}">Terms</a>
            <a href="{{ url('/policy') }}">Privacy</a>
            <a href="{{ url('/rules') }}">Rules</a>
        </nav>
        <p class="lm-footer__copy">© {{ date('Y') }} {{ config('app.name', 'Ace') }}. All rights reserved.</p>
    </div>
</footer>

<script>
(function () {
    var toggle = document.getElementById('lm-menu-toggle');
    var drawer = document.getElementById('lm-drawer');
    if (!toggle || !drawer) return;
    toggle.addEventListener('click', function () {
        var open = drawer.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });
    drawer.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.matchMedia('(max-width: 900px)').matches) {
                drawer.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open menu');
            }
        });
    });
})();
</script>
</body>
</html>
