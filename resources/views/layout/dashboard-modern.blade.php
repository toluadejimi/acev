<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Ace') }}</title>
    <link rel="icon" href="{{ url('') }}/public/assets/fav.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('') }}/public/css/dashboard-modern.css">
    @stack('styles')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="dashboard-app">
<header class="dash-header">
    <div class="dash-header__inner">
        <a href="{{ url('/home') }}" class="dash-header__brand">
            <span class="dash-header__mark" aria-hidden="true">A</span>
            <span>{{ config('app.name', 'Ace') }}</span>
        </a>
        <button type="button" class="dash-header__menu-btn" id="dash-nav-toggle" aria-expanded="false" aria-controls="dash-nav-drawer" aria-label="Open menu" aria-haspopup="true">
            <i class="bi bi-list dash-header__menu-icon dash-header__menu-icon--open" aria-hidden="true"></i>
            <i class="bi bi-x-lg dash-header__menu-icon dash-header__menu-icon--close" aria-hidden="true"></i>
        </button>
        <nav class="dash-nav" id="dash-nav-drawer" aria-label="Main">
            <div class="dash-drawer__header">
                <div class="dash-drawer__head-text">
                    <span class="dash-drawer__title">Menu</span>
                    @auth
                        <span class="dash-drawer__user">{{ Auth::user()->username }}</span>
                    @endauth
                </div>
                <button type="button" class="dash-drawer__close" id="dash-drawer-close" aria-label="Close menu">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
            <div class="dash-drawer__links">
            <a href="{{ url('/home') }}" class="dash-nav__link{{ request()->is('home') ? ' dash-nav__link--active' : '' }}"><i class="bi bi-speedometer2 dash-nav__ico" aria-hidden="true"></i><span>Dashboard</span></a>
            <a href="{{ route('verification.index') }}" class="dash-nav__link{{ request()->routeIs('verification.index') || request()->is('us', 'usa2', 'world') ? ' dash-nav__link--active' : '' }}"><i class="bi bi-chat-dots dash-nav__ico" aria-hidden="true"></i><span>SMS verification</span></a>
            <a href="{{ url('/fund-wallet') }}" class="dash-nav__link{{ request()->is('fund-wallet') ? ' dash-nav__link--active' : '' }}"><i class="bi bi-wallet2 dash-nav__ico" aria-hidden="true"></i><span>Fund wallet</span></a>
            <a href="{{ url('/api-docs') }}" class="dash-nav__link"><i class="bi bi-code-slash dash-nav__ico" aria-hidden="true"></i><span>API docs</span></a>
            <a href="https://aceboosts.com/" class="dash-nav__link" target="_blank" rel="noopener"><i class="bi bi-graph-up-arrow dash-nav__ico" aria-hidden="true"></i><span>Boost</span></a>
            @auth
                <a href="{{ url('/log-out') }}" class="dash-nav__cta"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Log out</a>
            @endauth
            </div>
        </nav>
    </div>
</header>
<div class="dash-drawer-backdrop" id="dash-drawer-backdrop" aria-hidden="true" hidden></div>

@if(!empty($topMessage ?? null))
    <div id="dash-flash-toast" class="dash-flash-toast" role="status" aria-live="polite" data-auto-dismiss="9000">
        <div class="dash-flash-toast__inner">
            <span class="dash-flash-toast__icon" aria-hidden="true"><i class="bi bi-info-circle-fill"></i></span>
            <p class="dash-flash-toast__text">{{ $topMessage }}</p>
            <button type="button" class="dash-flash-toast__close" aria-label="Dismiss">&times;</button>
        </div>
    </div>
@endif

@if(!empty($dashAnnouncement))
    <div id="dash-announce-modal"
         class="dash-announce-modal"
         hidden
         role="dialog"
         aria-modal="true"
         aria-labelledby="dash-announce-title"
         data-fingerprint="{{ $dashAnnouncement['fingerprint'] }}">
        <div class="dash-announce-modal__backdrop" data-announce-close></div>
        <div class="dash-announce-modal__dialog">
            <button type="button" class="dash-announce-modal__x" aria-label="Close" data-announce-close>&times;</button>
            <span class="dash-announce-modal__eyebrow"><i class="bi bi-megaphone-fill" aria-hidden="true"></i> Announcement</span>
            <h2 id="dash-announce-title" class="dash-announce-modal__title">{{ $dashAnnouncement['title'] }}</h2>
            <div class="dash-announce-modal__body">{!! nl2br(e($dashAnnouncement['message'])) !!}</div>
            <button type="button" class="dash-announce-modal__cta" id="dash-announce-dismiss">Got it</button>
        </div>
    </div>
@endif

<main class="dash-main">
    @yield('content')
</main>

<footer class="dash-footer">
    <a href="https://t.me/acesmsverify" target="_blank" rel="noopener">Telegram — {{ config('app.name', 'Ace') }}</a>
</footer>

<a href="https://t.me/acesmsverify" class="dash-float" target="_blank" rel="noopener" aria-label="Telegram">
    <i class="bi bi-telegram"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
(function () {
    var btn = document.getElementById('dash-nav-toggle');
    var nav = document.getElementById('dash-nav-drawer');
    var backdrop = document.getElementById('dash-drawer-backdrop');
    var closeBtn = document.getElementById('dash-drawer-close');
    if (!btn || !nav) return;

    function isMobileNav() {
        return window.matchMedia('(max-width: 900px)').matches;
    }

    function setOpen(open) {
        nav.classList.toggle('is-open', open);
        if (backdrop) {
            backdrop.classList.toggle('is-open', open);
            backdrop.hidden = !open;
            backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
        }
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        document.body.classList.toggle('dash-drawer-open', open);
    }

    function closeDrawer() {
        setOpen(false);
    }

    btn.addEventListener('click', function (e) {
        if (!isMobileNav()) return;
        e.stopPropagation();
        setOpen(!nav.classList.contains('is-open'));
    });

    if (backdrop) {
        backdrop.addEventListener('click', closeDrawer);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeDrawer);
    }

    nav.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            if (isMobileNav()) closeDrawer();
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && nav.classList.contains('is-open')) closeDrawer();
    });

    window.addEventListener('resize', function () {
        if (!isMobileNav() && nav.classList.contains('is-open')) closeDrawer();
    });
})();
document.addEventListener('DOMContentLoaded', function () {
    var toast = document.getElementById('dash-flash-toast');
    if (toast) {
        var ms = parseInt(toast.getAttribute('data-auto-dismiss') || '9000', 10);
        requestAnimationFrame(function () { toast.classList.add('is-visible'); });
        var hide = function () { toast.classList.remove('is-visible'); };
        var t = setTimeout(hide, ms);
        var btn = toast.querySelector('.dash-flash-toast__close');
        if (btn) {
            btn.addEventListener('click', function () {
                clearTimeout(t);
                hide();
            });
        }
    }

    var ann = document.getElementById('dash-announce-modal');
    if (ann) {
        var fp = ann.getAttribute('data-fingerprint') || '';
        var key = 'dashAnnDismissed';
        var skip = false;
        try {
            skip = !!(fp && localStorage.getItem(key) === fp);
        } catch (e) {}
        if (!skip) {
            var openAnn = function () {
                ann.hidden = false;
                document.body.classList.add('dash-announce-open');
            };
            var closeAnn = function () {
                ann.hidden = true;
                document.body.classList.remove('dash-announce-open');
                try {
                    if (fp) localStorage.setItem(key, fp);
                } catch (e) {}
            };

            setTimeout(openAnn, 600);

            ann.querySelectorAll('[data-announce-close]').forEach(function (el) {
                el.addEventListener('click', closeAnn);
            });
            var cta = document.getElementById('dash-announce-dismiss');
            if (cta) cta.addEventListener('click', closeAnn);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !ann.hidden) closeAnn();
            });
        }
    }
});
</script>
@stack('scripts')
</body>
</html>
