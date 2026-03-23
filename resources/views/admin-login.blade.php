<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        :root {
            --al-bg: #0b1220;
            --al-bg2: #18233b;
            --al-card: rgba(255, 255, 255, 0.92);
            --al-line: rgba(15, 23, 42, 0.12);
            --al-text: #0f172a;
            --al-muted: #64748b;
            --al-primary: #2563eb;
            --al-primary-dark: #1d4ed8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(950px 420px at 0% -10%, rgba(59,130,246,.35), transparent 70%),
                radial-gradient(850px 380px at 100% 110%, rgba(129,140,248,.25), transparent 70%),
                linear-gradient(145deg, var(--al-bg), var(--al-bg2));
            font-family: "Inter", system-ui, sans-serif;
            color: var(--al-text);
            padding: 1.25rem;
        }

        .al-shell {
            width: min(980px, 100%);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(8px);
            box-shadow: 0 24px 60px rgba(2, 6, 23, .45);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
        }

        .al-hero {
            padding: clamp(1.5rem, 4vw, 2.5rem);
            color: #e2e8f0;
            background: linear-gradient(165deg, rgba(37,99,235,.35), rgba(15,23,42,.2));
            border-right: 1px solid rgba(255,255,255,.1);
        }

        .al-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.25);
            padding: .35rem .75rem;
            font-size: .75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .al-hero h1 {
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            line-height: 1.18;
            margin: 0;
            font-weight: 800;
            color: #fff;
        }

        .al-hero p {
            margin-top: 1rem;
            color: #cbd5e1;
            max-width: 40ch;
            line-height: 1.6;
            font-size: .95rem;
        }

        .al-card {
            background: var(--al-card);
            padding: clamp(1.35rem, 4vw, 2.2rem);
        }

        .al-card h2 {
            margin: 0 0 .35rem;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .al-card .al-sub {
            margin: 0 0 1.25rem;
            color: var(--al-muted);
            font-size: .9rem;
        }

        .al-form .form-label {
            font-size: .85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .4rem;
        }

        .al-form .input-group-text {
            background: #fff;
            border-color: var(--al-line);
            color: #64748b;
        }

        .al-form .form-control {
            height: 46px;
            border-color: var(--al-line);
            border-radius: 10px;
            font-size: .95rem;
            box-shadow: none !important;
        }

        .al-form .form-control:focus {
            border-color: rgba(37,99,235,.45);
        }

        .al-submit {
            width: 100%;
            border: 0;
            height: 46px;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--al-primary), var(--al-primary-dark));
        }

        .al-submit:hover { filter: brightness(.98); }

        .al-alert ul {
            margin: 0;
            padding-left: 1rem;
        }

        @media (max-width: 900px) {
            .al-shell { grid-template-columns: 1fr; }
            .al-hero { border-right: 0; border-bottom: 1px solid rgba(255,255,255,.1); }
        }
    </style>
</head>
<body>
    <div class="al-shell">
        <section class="al-hero">
            <span class="al-badge"><i class="fas fa-shield-alt"></i> ACEVERIFY ADMIN</span>
            <h1>Secure access to your operations dashboard.</h1>
            <p>Monitor users, deposits, transactions, and service activity from one control panel.</p>
        </section>

        <section class="al-card">
            @if ($errors->any())
                <div class="alert alert-danger al-alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">{{ session()->get('error') }}</div>
            @endif

            <h2>Sign in</h2>
            <p class="al-sub">Enter your admin username and password.</p>

            <form action="{{ url('/admin-login') }}" method="POST" class="al-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="admin-username">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input id="admin-username" class="form-control" name="username" type="text" placeholder="Username" autocomplete="username" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="admin-password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="admin-password" class="form-control" name="password" type="password" placeholder="Password" autocomplete="current-password" required>
                    </div>
                </div>
                <button type="submit" class="al-submit">Continue to dashboard</button>
            </form>
        </section>
    </div>
</body>
</html>