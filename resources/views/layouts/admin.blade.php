<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aqua De Smiley Admin Portal – Manage products, prices, and financial reports.">
    <title>@yield('title', 'Admin Portal – Aqua De Smiley')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #0ea5e9;
            --primary-dk:   #0369a1;
            --accent:       #38bdf8;
            --admin-purple: #8b5cf6;
            --admin-light:  #a78bfa;
            --dark:         #070f1e;
            --sidebar-bg:   #0b182d;
            --sidebar-w:    260px;
            --topbar-h:     64px;
            --card-bg:      rgba(15, 30, 60, 0.75);
            --glass-border: rgba(139, 92, 246, 0.18);
            --text:         #e2e8f0;
            --text-muted:   #94a3b8;
            --success:      #10b981;
            --warning:      #f59e0b;
            --danger:       #ef4444;
        }

        html, body { height: 100%; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--dark);
            color: var(--text);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ─── TOP BAR ─────────────────────────────────────────────── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            height: var(--topbar-h);
            background: rgba(7, 15, 30, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            display: flex; align-items: center; padding: 0 1.5rem;
            gap: 1rem;
        }
        .topbar__hamburger {
            display: none;
            background: none; border: none; color: var(--text-muted);
            font-size: 1.25rem; cursor: pointer; padding: .3rem .5rem;
        }
        .topbar__brand {
            display: flex; align-items: center; gap: .7rem;
            font-size: 1.15rem; font-weight: 800;
            text-decoration: none;
            background: linear-gradient(135deg, #fff 0%, var(--admin-light) 60%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            min-width: calc(var(--sidebar-w) - 3rem);
        }
        .topbar__brand i { -webkit-text-fill-color: var(--admin-light); font-size: 1.35rem; }
        .topbar__portal-pill {
            font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
            padding: .15rem .5rem; border-radius: 99px;
            background: rgba(139, 92, 246, 0.2); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.35);
        }
        .topbar__spacer { flex: 1; }
        .topbar__right { display: flex; align-items: center; gap: 1rem; }
        .role-chip {
            padding: .2rem .75rem; border-radius: 99px; font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            background: rgba(139,92,246,.2); color: #c4b5fd; border: 1px solid rgba(139,92,246,.4);
        }
        .topbar__user { font-size: .85rem; color: var(--text-muted); display: flex; align-items: center; gap: .4rem; }
        .btn-logout {
            display: flex; align-items: center; gap: .35rem;
            padding: .35rem .95rem; border-radius: 8px;
            border: 1px solid rgba(239,68,68,.3); background: rgba(239,68,68,.08);
            color: #fca5a5; font-size: .78rem; font-weight: 600; cursor: pointer;
            transition: all .2s; text-decoration: none; font-family: 'Outfit', sans-serif;
        }
        .btn-logout:hover { background: rgba(239,68,68,.22); color: #fff; }

        /* ─── SIDEBAR ──────────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: var(--topbar-h); left: 0; bottom: 0;
            width: var(--sidebar-w); z-index: 150;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--glass-border);
            display: flex; flex-direction: column;
            overflow-y: auto;
            transition: transform .3s ease;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(139,92,246,.25); border-radius: 99px; }

        .sidebar__section-label {
            padding: 1.25rem 1.25rem .4rem;
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: rgba(167, 139, 250, 0.6);
        }
        .sidebar__nav { list-style: none; padding: 0 .6rem; }
        .sidebar__nav li { margin-bottom: .2rem; }
        .sidebar__link {
            display: flex; align-items: center; gap: .75rem;
            padding: .65rem .85rem; border-radius: 10px;
            color: var(--text-muted); text-decoration: none;
            font-size: .88rem; font-weight: 500;
            transition: all .2s ease;
        }
        .sidebar__link i { font-size: 1rem; width: 20px; text-align: center; color: #64748b; transition: color .2s; }
        .sidebar__link:hover {
            background: rgba(139, 92, 246, 0.12);
            color: #fff;
        }
        .sidebar__link:hover i { color: var(--admin-light); }
        .sidebar__link.active {
            background: linear-gradient(135deg, rgba(139,92,246,.25), rgba(14,165,233,.15));
            color: #fff; font-weight: 600;
            border: 1px solid rgba(139,92,246,.35);
        }
        .sidebar__link.active i { color: var(--admin-light); }
        .sidebar__badge {
            margin-left: auto; padding: .15rem .5rem; border-radius: 99px;
            font-size: .72rem; font-weight: 700; background: rgba(139,92,246,.25); color: #c4b5fd;
        }

        .sidebar__footer {
            margin-top: auto; padding: 1.2rem;
            border-top: 1px solid var(--glass-border);
            font-size: .75rem; color: #64748b;
        }

        /* ─── MAIN CONTENT ─────────────────────────────────────────── */
        .main-content {
            margin-top: var(--topbar-h);
            margin-left: var(--sidebar-w);
            padding: 2rem;
            flex: 1;
            min-height: calc(100vh - var(--topbar-h));
            background: radial-gradient(circle at 80% 20%, rgba(139,92,246,.05) 0%, transparent 60%),
                        radial-gradient(circle at 10% 80%, rgba(14,165,233,.05) 0%, transparent 50%);
        }

        /* ─── PAGE HEADER ─────────────────────────────────────────── */
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 {
            font-size: 1.65rem; font-weight: 700; display: flex; align-items: center; gap: .6rem;
            letter-spacing: -.02em;
        }
        .page-header p { color: var(--text-muted); font-size: .88rem; margin-top: .3rem; }

        /* ─── CARDS ─────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            backdrop-filter: blur(14px);
            padding: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.25);
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.35rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.35); }
        .stat-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: grid; place-items: center; font-size: 1.45rem; flex-shrink: 0;
        }
        .stat-icon--purple { background: rgba(139,92,246,.2); color: #a78bfa; border: 1px solid rgba(139,92,246,.3); }
        .stat-icon--blue   { background: rgba(14,165,233,.2);  color: #38bdf8; border: 1px solid rgba(14,165,233,.3); }
        .stat-icon--green  { background: rgba(16,185,129,.2);  color: #34d399; border: 1px solid rgba(16,185,129,.3); }
        .stat-icon--amber  { background: rgba(245,158,11,.2);  color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
        .stat-icon--rose   { background: rgba(244,63,94,.2);   color: #fb7185; border: 1px solid rgba(244,63,94,.3); }
        .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.1; }
        .stat-label { color: var(--text-muted); font-size: .8rem; margin-top: .25rem; }

        /* ─── BUTTONS ───────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .6rem 1.25rem; border-radius: 10px; font-size: .875rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all .2s; border: none;
            font-family: 'Outfit', sans-serif;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--admin-purple), #6366f1);
            color: #fff; box-shadow: 0 4px 14px rgba(139,92,246,.35);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(139,92,246,.5); color: #fff; }
        .btn-cyan {
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            color: #fff; box-shadow: 0 4px 14px rgba(14,165,233,.35);
        }
        .btn-cyan:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,.5); color: #fff; }
        .btn-outline {
            background: rgba(255,255,255,.04); border: 1px solid var(--glass-border);
            color: var(--text);
        }
        .btn-outline:hover { background: rgba(255,255,255,.09); color: #fff; }
        .btn-sm { padding: .35rem .75rem; font-size: .78rem; border-radius: 7px; }

        /* ─── FORMS & INPUTS ────────────────────────────────────────── */
        .form-group { margin-bottom: 1.2rem; }
        .form-label { display: block; font-size: .83rem; font-weight: 600; color: var(--text-muted); margin-bottom: .4rem; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: .65rem .95rem; border-radius: 10px;
            background: rgba(255,255,255,.05); border: 1px solid var(--glass-border);
            color: var(--text); font-family: 'Outfit', sans-serif; font-size: .9rem;
            transition: all .2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none; border-color: var(--admin-light);
            box-shadow: 0 0 0 3px rgba(139,92,246,.2);
        }
        .form-select option { background: #0b182d; color: #fff; }
        .form-error { color: #fca5a5; font-size: .8rem; margin-top: .3rem; display: flex; align-items: center; gap: .35rem; }

        /* ─── TABLES ────────────────────────────────────────────────── */
        .table-wrapper { overflow-x: auto; border-radius: 14px; border: 1px solid var(--glass-border); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead tr { background: rgba(139,92,246,.12); }
        th {
            padding: .9rem 1.15rem; font-size: .78rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em; color: var(--admin-light);
        }
        td { padding: .85rem 1.15rem; border-top: 1px solid var(--glass-border); font-size: .88rem; }
        tr:hover td { background: rgba(255,255,255,.025); }

        /* ─── BADGES ────────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .22rem .65rem; border-radius: 99px;
            font-size: .74rem; font-weight: 700;
        }
        .badge--success { background: rgba(16,185,129,.18); color: #34d399; border: 1px solid rgba(16,185,129,.35); }
        .badge--warning { background: rgba(245,158,11,.18); color: #fbbf24; border: 1px solid rgba(245,158,11,.35); }
        .badge--danger  { background: rgba(239,68,68,.18);  color: #f87171; border: 1px solid rgba(239,68,68,.35); }
        .badge--info    { background: rgba(14,165,233,.18); color: #38bdf8; border: 1px solid rgba(14,165,233,.35); }
        .badge--purple  { background: rgba(139,92,246,.2);  color: #c4b5fd; border: 1px solid rgba(139,92,246,.35); }

        /* ─── ALERTS ────────────────────────────────────────────────── */
        .alert {
            padding: .9rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;
            font-size: .9rem; display: flex; align-items: center; gap: .75rem;
        }
        .alert-success { background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.35); color: #34d399; }
        .alert-error   { background: rgba(239,68,68,.15);  border: 1px solid rgba(239,68,68,.35);  color: #fca5a5; }

        /* ─── BACKDROP & MOBILE ─────────────────────────────────────── */
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.6); z-index: 140;
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-backdrop.open { display: block; }
            .main-content { margin-left: 0; padding: 1.25rem; }
            .topbar__hamburger { display: block; }
        }

        /* ─── PRINT OPTIMIZATIONS ──────────────────────────────────── */
        @media print {
            .topbar, .sidebar, .btn, .filter-bar, form { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; background: #fff !important; color: #000 !important; }
            .card { border: 1px solid #ccc !important; box-shadow: none !important; background: #fff !important; color: #000 !important; }
            table { color: #000 !important; }
            th { color: #000 !important; border-bottom: 2px solid #000 !important; background: #eee !important; }
            td { color: #000 !important; border-top: 1px solid #ddd !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
    <button class="topbar__hamburger" id="sidebarToggle" aria-label="Toggle Navigation">
        <i class="fa-solid fa-bars"></i>
    </button>
    <a href="{{ route('admin.dashboard') }}" class="topbar__brand">
        <i class="fa-solid fa-droplet"></i>
        <span>Aqua De Smiley</span>
        <span class="topbar__portal-pill"><i class="fa-solid fa-shield-halved"></i> Admin</span>
    </a>

    <div class="topbar__spacer"></div>

    <div class="topbar__right">
        <span class="topbar__user">
            <i class="fa-solid fa-circle-user" style="color:var(--admin-light)"></i>
            {{ auth()->user()->name }}
        </span>
        <span class="role-chip">Executive Admin</span>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</header>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <span class="sidebar__section-label">Management</span>
    <ul class="sidebar__nav">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.products.index') }}" class="sidebar__link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Products & Prices
            </a>
        </li>
    </ul>

    <span class="sidebar__section-label">Financial Reports</span>
    <ul class="sidebar__nav">
        <li>
            <a href="{{ route('admin.reports.sales') }}" class="sidebar__link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Sales Report
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reports.income') }}" class="sidebar__link {{ request()->routeIs('admin.reports.income') ? 'active' : '' }}">
                <i class="fa-solid fa-coins"></i> Income & Profit
            </a>
        </li>
    </ul>

    <span class="sidebar__section-label">Station Operations</span>
    <ul class="sidebar__nav">
        <li>
            <a href="{{ route('staff.orders.index') }}" class="sidebar__link" target="_blank">
                <i class="fa-solid fa-receipt"></i> Orders Tracking <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem;margin-left:auto;color:#64748b;"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.payments.index') }}" class="sidebar__link" target="_blank">
                <i class="fa-solid fa-cash-register"></i> Walk-in POS <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem;margin-left:auto;color:#64748b;"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.supplies.index') }}" class="sidebar__link" target="_blank">
                <i class="fa-solid fa-warehouse"></i> Supplies & Stock <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem;margin-left:auto;color:#64748b;"></i>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.customers.index') }}" class="sidebar__link" target="_blank">
                <i class="fa-solid fa-users"></i> Customers <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem;margin-left:auto;color:#64748b;"></i>
            </a>
        </li>
    </ul>

    <div class="sidebar__footer">
        <div style="font-weight:600;color:var(--text);margin-bottom:.2rem;">Aqua De Smiley Station</div>
        <div>System Version 2.2 • Admin Suite</div>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @yield('content')
</main>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('open');
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('open');
            backdrop.classList.remove('open');
        });
    }
</script>

@stack('scripts')
</body>
</html>
