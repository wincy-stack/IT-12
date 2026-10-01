<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aqua De Smiley – Pure & Clean Water Refilling Station. Manage your orders and account.">
    <title>@yield('title', 'Aqua De Smiley')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #0ea5e9;
            --primary-dark: #0369a1;
            --accent:       #38bdf8;
            --glass-bg:     rgba(255,255,255,0.06);
            --glass-border: rgba(255,255,255,0.14);
            --dark-bg:      #0a1628;
            --card-bg:      rgba(15, 30, 60, 0.7);
            --text:         #e2e8f0;
            --text-muted:   #94a3b8;
            --success:      #10b981;
            --danger:       #ef4444;
            --warning:      #f59e0b;
            --sidebar-w:    260px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--dark-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAV ── */
        .topnav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10, 22, 40, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0 2rem;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topnav__brand {
            display: flex; align-items: center; gap: .75rem;
            font-size: 1.25rem; font-weight: 700;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none;
        }
        .topnav__brand i { font-size: 1.5rem; -webkit-text-fill-color: var(--accent); }
        .topnav__right { display: flex; align-items: center; gap: 1rem; }
        .role-badge {
            padding: .25rem .75rem; border-radius: 99px; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
        }
        .role-badge--admin    { background: rgba(139,92,246,.2); color: #a78bfa; border: 1px solid rgba(139,92,246,.4); }
        .role-badge--staff    { background: rgba(14,165,233,.2); color: #38bdf8; border: 1px solid rgba(14,165,233,.4); }
        .role-badge--customer { background: rgba(16,185,129,.2); color: #34d399; border: 1px solid rgba(16,185,129,.4); }

        .topnav__user { color: var(--text-muted); font-size: .875rem; }
        .btn-logout {
            display: flex; align-items: center; gap: .4rem;
            padding: .4rem 1rem; border-radius: 8px; border: 1px solid rgba(239,68,68,.35);
            background: rgba(239,68,68,.1); color: #fca5a5; font-size: .8rem; font-weight: 500;
            cursor: pointer; transition: all .2s; text-decoration: none;
        }
        .btn-logout:hover { background: rgba(239,68,68,.25); color: #fff; }

        /* ── MAIN CONTENT ── */
        .page-wrapper { flex: 1; padding: 2rem; max-width: 1300px; margin: 0 auto; width: 100%; }

        /* ── CARDS ── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            backdrop-filter: blur(12px);
            padding: 1.5rem;
        }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.3); }
        .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: grid; place-items: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-icon--blue   { background: rgba(14,165,233,.2); color: #38bdf8; }
        .stat-icon--purple { background: rgba(139,92,246,.2); color: #a78bfa; }
        .stat-icon--green  { background: rgba(16,185,129,.2); color: #34d399; }
        .stat-icon--amber  { background: rgba(245,158,11,.2); color: #fbbf24; }
        .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
        .stat-label { color: var(--text-muted); font-size: .8rem; margin-top: .2rem; }

        /* ── TABLE ── */
        .table-wrapper { overflow-x: auto; border-radius: 12px; border: 1px solid var(--glass-border); }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: rgba(14,165,233,.1); }
        th { padding: .85rem 1.25rem; text-align: left; font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); }
        td { padding: .85rem 1.25rem; border-top: 1px solid var(--glass-border); font-size: .9rem; }
        tr:hover td { background: rgba(255,255,255,.03); }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .6rem 1.4rem; border-radius: 10px; font-size: .875rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all .2s; border: none;
        }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; box-shadow: 0 4px 14px rgba(14,165,233,.35); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,.5); }
        .btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text); }
        .btn-outline:hover { background: var(--glass-bg); }

        /* ── ALERTS ── */
        .alert { padding: 1rem 1.25rem; border-radius: 10px; margin-bottom: 1rem; font-size: .9rem; display: flex; align-items: center; gap: .75rem; }
        .alert-success { background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3); color: #34d399; }
        .alert-error   { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; }

        /* ── PAGE HEADER ── */
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.75rem; font-weight: 700; }
        .page-header p { color: var(--text-muted); margin-top: .25rem; }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .85rem; font-weight: 500; color: var(--text-muted); margin-bottom: .4rem; }
        .form-input {
            width: 100%; padding: .7rem 1rem; border-radius: 10px;
            background: rgba(255,255,255,.06); border: 1px solid var(--glass-border);
            color: var(--text); font-family: 'Outfit', sans-serif; font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(14,165,233,.18); }
        .form-input::placeholder { color: var(--text-muted); }
        .form-error { color: #fca5a5; font-size: .8rem; margin-top: .3rem; }
    </style>
    @stack('styles')
</head>
<body>

<nav class="topnav">
    <div style="display:flex;align-items:center;gap:2rem;">
        <a href="{{ auth()->check() && auth()->user()->role === 'customer' ? route('customer.dashboard') : '#' }}" class="topnav__brand">
            <i class="fa-solid fa-droplet"></i>
            Aqua De Smiley
        </a>
        @auth
            @if(auth()->user()->role === 'customer')
            <div style="display:flex;align-items:center;gap:1.25rem;">
                <a href="{{ route('customer.dashboard') }}" style="color:{{ request()->routeIs('customer.dashboard') ? '#38bdf8' : 'var(--text-muted)' }};text-decoration:none;font-size:.9rem;font-weight:600;display:flex;align-items:center;gap:.4rem;">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a href="{{ route('customer.orders.index') }}" style="color:{{ request()->routeIs('customer.orders.index') ? '#38bdf8' : 'var(--text-muted)' }};text-decoration:none;font-size:.9rem;font-weight:600;display:flex;align-items:center;gap:.4rem;">
                    <i class="fa-solid fa-receipt"></i> My Orders
                </a>
                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary" style="padding:.35rem .9rem;font-size:.8rem;">
                    <i class="fa-solid fa-plus"></i> Place Order
                </a>
            </div>
            @endif
        @endauth
    </div>
    <div class="topnav__right">
        @auth
            <span class="topnav__user"><i class="fa-regular fa-user" style="margin-right:.35rem"></i>{{ auth()->user()->name }}</span>
            <span class="role-badge role-badge--{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </form>
        @endauth
    </div>
</nav>

<div class="page-wrapper">
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
