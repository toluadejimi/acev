<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Ace') }}</title>
    <link rel="icon" href="{{ url('') }}/public/assets/fav.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=DM+Sans:ital,opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            <a href="{{ route('vas.airtime') }}" class="dash-nav__link{{ request()->is('vas/*') ? ' dash-nav__link--active' : '' }}"><i class="bi bi-receipt-cutoff dash-nav__ico" aria-hidden="true"></i><span>Airtime &amp; bills</span></a>
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

<footer class="dash-footer"></footer>

<a href="https://t.me/acesmsverify" class="dash-float" target="_blank" rel="noopener" aria-label="Telegram">
    <i class="bi bi-telegram"></i>
</a>

<button type="button" id="assistant-fab" class="assistant-fab" aria-label="Open assistant">
    <i class="bi bi-robot"></i>
    <span>Assistant</span>
</button>
<section id="assistant-panel" class="assistant-panel" hidden aria-label="Order assistant">
    <header class="assistant-panel__head">
        <div>
            <h3>Order Assistant</h3>
            <p>Type what you want to do</p>
        </div>
        <button type="button" id="assistant-close" aria-label="Close assistant"><i class="bi bi-x-lg"></i></button>
    </header>
    <div id="assistant-messages" class="assistant-panel__messages">
        <div class="assistant-msg assistant-msg--bot">Try <code>order usa whatsapp</code>, <code>vtu airtime</code>, or <code>contact support</code>.</div>
    </div>
    <div class="assistant-panel__quick">
        <button type="button" class="assistant-quick" data-cmd="order usa whatsapp">WhatsApp</button>
        <button type="button" class="assistant-quick" data-cmd="order usa telegram">Telegram</button>
        <button type="button" class="assistant-quick" data-cmd="vtu airtime">VTU Airtime</button>
        <button type="button" class="assistant-quick" data-cmd="vtu data">VTU Data</button>
        <button type="button" class="assistant-quick" data-cmd="contact support">Support</button>
        <button type="button" class="assistant-quick" data-cmd="balance">Balance</button>
    </div>
    <form id="assistant-form" class="assistant-panel__form">
        <input id="assistant-input" type="text" placeholder="e.g. order usa whatsapp" maxlength="500" autocomplete="off">
        <button type="submit">Send</button>
    </form>
</section>

<style>
    .dash-float {
        right: auto !important;
        left: 1.1rem !important;
        bottom: 1.1rem !important;
    }
    .assistant-fab{position:fixed;right:1.1rem;bottom:1.1rem;z-index:1200;display:flex;gap:.45rem;align-items:center;border:0;border-radius:999px;padding:.7rem .95rem;background:linear-gradient(135deg,#0f172a,#1d4ed8);color:#fff;font-weight:700;box-shadow:0 12px 30px rgba(15,23,42,.35)}
    .assistant-fab i{font-size:1rem}
    .assistant-panel{position:fixed;right:1.1rem;bottom:4.8rem;z-index:1200;width:min(380px,calc(100vw - 1.5rem));background:#fff;border:1px solid #dbe3ef;border-radius:16px;box-shadow:0 22px 50px rgba(15,23,42,.2);overflow:hidden}
    .assistant-panel__head{display:flex;justify-content:space-between;align-items:flex-start;padding:.85rem .95rem;border-bottom:1px solid #eef2f7;background:#f8fafc}
    .assistant-panel__head h3{margin:0;font-size:.95rem;font-weight:800;color:#0f172a}
    .assistant-panel__head p{margin:.15rem 0 0;font-size:.76rem;color:#475569}
    .assistant-panel__head button{border:0;background:transparent;color:#64748b}
    .assistant-panel__messages{max-height:280px;overflow:auto;padding:.75rem;display:flex;flex-direction:column;gap:.5rem;background:#fff}
    .assistant-msg{font-size:.84rem;line-height:1.35;padding:.55rem .65rem;border-radius:12px;max-width:92%}
    .assistant-msg--bot{align-self:flex-start;background:#f1f5f9;color:#0f172a}
    .assistant-msg--user{align-self:flex-end;background:#1d4ed8;color:#fff}
    .assistant-choice-row{display:flex;gap:.45rem;flex-wrap:wrap;margin-top:.2rem}
    .assistant-choice{border:1px solid #cbd5e1;background:#fff;border-radius:999px;padding:.35rem .7rem;font-size:.75rem;font-weight:700;color:#1e293b;text-decoration:none}
    .assistant-choice:hover{border-color:#1d4ed8;color:#1d4ed8}
    .assistant-action{border:1px solid #93c5fd;background:#eff6ff;border-radius:999px;padding:.35rem .7rem;font-size:.75rem;font-weight:800;color:#1d4ed8}
    .assistant-panel__quick{display:flex;flex-wrap:wrap;gap:.35rem;padding:.45rem .75rem;border-top:1px solid #eef2f7}
    .assistant-quick{border:1px solid #cbd5e1;background:#fff;border-radius:999px;padding:.3rem .6rem;font-size:.72rem;font-weight:700;color:#1e293b}
    .assistant-panel__form{display:flex;gap:.45rem;padding:.7rem;border-top:1px solid #eef2f7}
    .assistant-panel__form input{flex:1;border:1px solid #cbd5e1;border-radius:10px;padding:.52rem .62rem;font-size:.84rem}
    .assistant-panel__form button{border:0;background:#0f172a;color:#fff;border-radius:10px;padding:.52rem .8rem;font-size:.8rem;font-weight:700}
</style>

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

document.addEventListener('DOMContentLoaded', function () {
    var fab = document.getElementById('assistant-fab');
    var panel = document.getElementById('assistant-panel');
    var closeBtn = document.getElementById('assistant-close');
    var form = document.getElementById('assistant-form');
    var input = document.getElementById('assistant-input');
    var msgs = document.getElementById('assistant-messages');
    if (!fab || !panel || !form || !input || !msgs) return;

    function toggle(open) {
        panel.hidden = !open;
        if (open) input.focus();
    }
    fab.addEventListener('click', function () { toggle(panel.hidden); });
    if (closeBtn) closeBtn.addEventListener('click', function () { toggle(false); });

    function addMsg(kind, text) {
        var d = document.createElement('div');
        d.className = 'assistant-msg assistant-msg--' + kind;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function addActionLinks(links) {
        if (!Array.isArray(links) || links.length === 0) return;
        var wrap = document.createElement('div');
        wrap.className = 'assistant-msg assistant-msg--bot';
        var row = document.createElement('div');
        row.className = 'assistant-choice-row';
        links.forEach(function (item) {
            if (!item || !item.url) return;
            var a = document.createElement('a');
            a.className = 'assistant-choice';
            a.href = item.url;
            a.textContent = item.label || 'Open';
            row.appendChild(a);
        });
        wrap.appendChild(row);
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function addActionCommands(actions) {
        if (!Array.isArray(actions) || actions.length === 0) return;
        var wrap = document.createElement('div');
        wrap.className = 'assistant-msg assistant-msg--bot';
        var row = document.createElement('div');
        row.className = 'assistant-choice-row';
        actions.forEach(function (item) {
            if (!item || !item.command) return;
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'assistant-action';
            b.textContent = item.label || item.command;
            b.addEventListener('click', function () {
                sendCommand(item.command);
            });
            row.appendChild(b);
        });
        wrap.appendChild(row);
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    async function sendCommand(text) {
        addMsg('user', text);
        var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            var r = await fetch('{{ url('assistant/command') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ message: text })
            });
            var j = await r.json();
            addMsg('bot', j.reply || 'Done.');
            if (Array.isArray(j.links) && j.links.length) {
                addActionLinks(j.links);
            }
            if (Array.isArray(j.actions) && j.actions.length) {
                addActionCommands(j.actions);
            }
            if (j.reload) setTimeout(function () { location.reload(); }, 900);
        } catch (e) {
            addMsg('bot', 'Something went wrong. Please try again.');
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var v = (input.value || '').trim();
        if (!v) return;
        input.value = '';
        sendCommand(v);
    });

    panel.querySelectorAll('.assistant-quick[data-cmd]').forEach(function (b) {
        b.addEventListener('click', function () {
            var cmd = b.getAttribute('data-cmd');
            if (cmd) sendCommand(cmd);
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
