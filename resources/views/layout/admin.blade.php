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


    <style>
        /* ===== Top Popup Banner ===== */
        .popup-banner {
            position: fixed;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, #2563eb, #1e40af);
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 9999;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: top 0.6s ease;
            width: fit-content;
            max-width: 90%;
        }
        .popup-banner.show { top: 20px; }

        .popup-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            font-weight: bold;
            margin-left: 15px;
            cursor: pointer;
        }
        .popup-close:hover { opacity: 0.7; }

        /* ===== Center Popup ===== */
        .popup-center {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            z-index: 10000;
        }
        .popup-center.show {
            opacity: 1;
            visibility: visible;
        }

        .popup-box {
            background: white;
            color: #333;
            border-radius: 14px;
            padding: 25px 30px;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            animation: fadeInUp 0.6s ease;
        }

        .popup-box span {
            display: block;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }

        .popup-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .popup-btn:hover { background: #1e40af; }

        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>


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

                    <li class="nav-item">
                        <a href="notify">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Notification</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </aside>

    @if(session('topMessage'))
        <div id="top-popup" class="popup-banner">
            <div class="popup-content">
                <span>{{ session('topMessage') }}</span>
                <button class="popup-close" onclick="closeTopPopup()">&times;</button>
            </div>
        </div>
    @endif



    @yield('content')


</div>

<script>
    document.querySelector('.menu-toggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const topPopup = document.getElementById("top-popup");
        const centerPopup = document.getElementById("center-popup");

        // Show top banner
        if (topPopup) {
            setTimeout(() => topPopup.classList.add("show"), 400);
            setTimeout(() => topPopup.classList.remove("show"), 8000);
        }

        // Show center popup a few seconds later
        if (centerPopup) {
            setTimeout(() => centerPopup.classList.add("show"), 3500);
        }
    });

    function closeTopPopup() {
        document.getElementById("top-popup")?.classList.remove("show");
    }

    function closeCenterPopup() {
        document.getElementById("center-popup")?.classList.remove("show");
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>
