<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AceSMSVerify - Admin Dashboard</title>
    <link rel="stylesheet" href="{{url('')}}/public/assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>
<body>
<div class="mobile-header d-md-none">
    <span class="menu-toggle"><i class="fas fa-bars"></i></span>
    <span><strong>ACE SMS VERIFY</strong></span>
</div>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-shield-alt"></i>
                <span class="logo-text"><strong>ACE SMS VERIFY</strong></span>
            </div>
        </div>

        <a href="admin-dashboard" class="admin-panel">
            <i class="fas fa-user-shield"></i>
            <span>Administrator Panel</span>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">DASHBOARD</div>
                <ul class="nav-list list-unstyled">
                    <li class="nav-item active">
                        <a href="admin-dashboard">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="manual-payment">
                            <i class="fas fa-credit-card"></i>
                            <span>Deposit Requests</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="transactions">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="orders">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    @yield('content')
</div>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>
