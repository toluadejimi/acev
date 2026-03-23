<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AceSMSVerify - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{url('')}}/public/assets/styles.css">
</head>
<body class="admin-shell">
<div class="admin-mobile-bar d-lg-none">
    <button type="button" class="admin-mobile-toggle" id="admin-mobile-toggle" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>
    <strong>ACEVERIFY Admin</strong>
</div>

<div class="dashboard-container">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ url('admin-dashboard') }}" class="logo">
                <span class="logo-mark"><i class="fas fa-shield-alt"></i></span>
                <span class="logo-text">
                    <strong>ACEVERIFY</strong>
                    <small>Admin Console</small>
                </span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">MAIN</div>
                <ul class="nav-list list-unstyled">
                    <li class="nav-item{{ request()->is('admin-dashboard') ? ' active' : '' }}">
                        <a href="{{ url('admin-dashboard') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item{{ request()->is('users', 'view-user') ? ' active' : '' }}">
                        <a href="{{ url('users') }}">
                            <i class="fas fa-users"></i>
                            <span>Manage Users</span>
                        </a>
                    </li>
                    <li class="nav-item{{ request()->is('manual-payment') ? ' active' : '' }}">
                        <a href="{{ url('manual-payment') }}">
                            <i class="fas fa-credit-card"></i>
                            <span>Deposit Requests</span>
                        </a>
                    </li>
                    <li class="nav-item{{ request()->is('transactions') ? ' active' : '' }}">
                        <a href="{{ url('transactions') }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item{{ request()->is('price-setting') ? ' active' : '' }}">
                        <a href="{{ url('price-setting') }}">
                            <i class="fas fa-gear"></i>
                            <span>Price Setting</span>
                        </a>
                    </li>
                    <li class="nav-item{{ request()->is('notify') ? ' active' : '' }}">
                        <a href="{{ url('notify') }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Notification</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="{{ url('log-out') }}" class="sidebar-logout">
                <i class="fas fa-right-from-bracket"></i>
                <span>Log out</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebar-backdrop" hidden></div>

    @if (session('topMessage'))
        <div id="top-popup" class="popup-banner">
            <span>{{ session('topMessage') }}</span>
            <button class="popup-close" type="button" aria-label="Dismiss">&times;</button>
        </div>
    @endif

    @yield('content')
</div>

<script>
    (function () {
        const toggleBtn = document.getElementById('admin-mobile-toggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const popup = document.getElementById('top-popup');
        const popupClose = document.querySelector('.popup-close');

        function openSidebar() {
            sidebar.classList.add('active');
            backdrop.hidden = false;
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            backdrop.hidden = true;
        }

        if (toggleBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', openSidebar);
            backdrop.addEventListener('click', closeSidebar);
        }

        if (popup) {
            setTimeout(function () {
                popup.classList.add('show');
            }, 200);
            setTimeout(function () {
                popup.classList.remove('show');
            }, 8000);
            if (popupClose) {
                popupClose.addEventListener('click', function () {
                    popup.classList.remove('show');
                });
            }
        }
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>
