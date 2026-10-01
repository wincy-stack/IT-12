<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Aqua De Smiley Staff Portal – Manage orders, customers and supplies.">
    <title>@yield('title', 'Staff Portal – Aqua De Smiley')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #0ea5e9;
            --primary-dk:   #0369a1;
            --accent:       #38bdf8;
            --dark:         #070f1e;
            --sidebar-bg:   #0b1a30;
            --sidebar-w:    255px;
            --topbar-h:     62px;
            --card-bg:      rgba(15,30,60,0.7);
            --glass-border: rgba(56,189,248,0.13);
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
        }

        /* ─── TOP BAR ─────────────────────────────────────────────── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 200;
            height: var(--topbar-h);
            background: rgba(7,15,30,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            display: flex; align-items: center; padding: 0 1.5rem;
            gap: 1rem;
        }
        .topbar__hamburger {
            display: none; /* shown on mobile */
            background: none; border: none; color: var(--text-muted);
            font-size: 1.2rem; cursor: pointer; padding: .3rem .5rem;
        }
        .topbar__brand {
            display: flex; align-items: center; gap: .6rem;
            font-size: 1.1rem; font-weight: 800;
            text-decoration: none;
            background: linear-gradient(135deg, #fff 0%, var(--accent) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            min-width: calc(var(--sidebar-w) - 3rem);
        }
        .topbar__brand i { -webkit-text-fill-color: var(--accent); font-size: 1.25rem; }
        .topbar__spacer { flex: 1; }
        .topbar__right { display: flex; align-items: center; gap: .85rem; }
        .role-chip {
            padding: .2rem .65rem; border-radius: 99px; font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            background: rgba(14,165,233,.18); color: #38bdf8; border: 1px solid rgba(14,165,233,.35);
        }
        .topbar__user { font-size: .85rem; color: var(--text-muted); display:flex;align-items:center;gap:.4rem; }
        .btn-logout {
            display: flex; align-items: center; gap: .35rem;
            padding: .35rem .9rem; border-radius: 8px;
            border: 1px solid rgba(239,68,68,.3); background: rgba(239,68,68,.08);
            color: #fca5a5; font-size: .78rem; font-weight: 600; cursor: pointer;
            transition: all .2s; text-decoration: none; font-family: 'Outfit',sans-serif;
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
        .sidebar::-webkit-scrollbar-thumb { background: rgba(56,189,248,.2); border-radius: 99px; }

        .sidebar__section-label {
            padding: 1.25rem 1.25rem .4rem;
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .1em; color: rgba(148,163,184,.4);
        }

        .nav-item {
            display: flex; align-items: center; gap: .75rem;
            padding: .65rem 1.25rem; margin: .15rem .6rem;
            border-radius: 10px; text-decoration: none;
            color: var(--text-muted); font-size: .875rem; font-weight: 500;
            transition: all .2s; position: relative;
        }
        .nav-item i { width: 18px; text-align: center; font-size: .95rem; flex-shrink: 0; }
        .nav-item:hover { background: rgba(56,189,248,.08); color: var(--text); }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(14,165,233,.22), rgba(56,189,248,.1));
            color: #38bdf8; font-weight: 600;
            border: 1px solid rgba(56,189,248,.2);
        }
        .nav-item.active i { color: #38bdf8; }
        .nav-item .badge {
            margin-left: auto; padding: .15rem .5rem; border-radius: 99px;
            font-size: .65rem; font-weight: 700;
            background: rgba(239,68,68,.2); color: #f87171; border: 1px solid rgba(239,68,68,.3);
        }
        .nav-item .badge-blue {
            margin-left: auto; padding: .15rem .5rem; border-radius: 99px;
            font-size: .65rem; font-weight: 700;
            background: rgba(14,165,233,.2); color: #38bdf8; border: 1px solid rgba(14,165,233,.3);
        }

        /* Sidebar footer */
        .sidebar__footer {
            margin-top: auto; padding: 1rem;
            border-top: 1px solid var(--glass-border);
        }
        .sidebar__profile {
            display: flex; align-items: center; gap: .75rem;
            padding: .65rem .75rem; border-radius: 10px;
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
        }
        .sidebar__avatar {
            width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dk));
            display: grid; place-items: center; font-weight: 700; font-size: .85rem;
        }
        .sidebar__name { font-size: .85rem; font-weight: 600; line-height: 1.2; }
        .sidebar__email { font-size: .72rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }

        /* ─── MAIN CONTENT AREA ────────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            min-height: calc(100vh - var(--topbar-h));
            padding: 2rem;
        }

        /* ─── PAGE HEADER ──────────────────────────────────────────── */
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-size: 1.6rem; font-weight: 700; display:flex;align-items:center;gap:.6rem; }
        .page-header p { color: var(--text-muted); margin-top: .25rem; font-size: .9rem; }

        /* ─── STAT CARDS ───────────────────────────────────────────── */
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card {
            background: var(--card-bg); border: 1px solid var(--glass-border);
            border-radius: 14px; padding: 1.25rem 1.35rem;
            display: flex; align-items: center; gap: 1rem;
            transition: transform .2s, box-shadow .2s; backdrop-filter: blur(10px);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.3); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display:grid;place-items:center; font-size:1.3rem; flex-shrink:0; }
        .si-blue   { background:rgba(14,165,233,.18); color:#38bdf8; }
        .si-green  { background:rgba(16,185,129,.18); color:#34d399; }
        .si-amber  { background:rgba(245,158,11,.18);  color:#fbbf24; }
        .si-purple { background:rgba(139,92,246,.18); color:#a78bfa; }
        .si-red    { background:rgba(239,68,68,.18);   color:#f87171; }
        .stat-value { font-size: 1.6rem; font-weight: 700; line-height:1; }
        .stat-label { color: var(--text-muted); font-size: .78rem; margin-top:.15rem; }

        /* ─── CARDS ────────────────────────────────────────────────── */
        .card {
            background: var(--card-bg); border: 1px solid var(--glass-border);
            border-radius: 14px; padding: 1.35rem; backdrop-filter: blur(10px);
        }
        .card + .card { margin-top: 1.25rem; }
        .card-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem; }
        .card-title { font-size:1rem;font-weight:600;display:flex;align-items:center;gap:.5rem; }

        /* ─── TABLE ────────────────────────────────────────────────── */
        .table-wrap { overflow-x:auto;border-radius:10px;border:1px solid var(--glass-border); }
        table { width:100%;border-collapse:collapse; }
        thead tr { background:rgba(14,165,233,.08); }
        th { padding:.75rem 1.1rem;text-align:left;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted); }
        td { padding:.8rem 1.1rem;border-top:1px solid var(--glass-border);font-size:.875rem; }
        tr:hover td { background:rgba(255,255,255,.02); }

        /* ─── STATUS BADGES ────────────────────────────────────────── */
        .status-badge {
            display:inline-flex;align-items:center;gap:.35rem;
            padding:.22rem .75rem;border-radius:99px;font-size:.72rem;font-weight:700;
        }

        /* ─── BUTTONS ──────────────────────────────────────────────── */
        .btn { display:inline-flex;align-items:center;gap:.45rem;padding:.55rem 1.2rem;border-radius:9px;font-size:.85rem;font-weight:600;cursor:pointer;text-decoration:none;transition:all .2s;border:none;font-family:'Outfit',sans-serif; }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-dk));color:#fff;box-shadow:0 3px 12px rgba(14,165,233,.35); }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 5px 18px rgba(14,165,233,.5); }
        .btn-sm { padding:.35rem .85rem;font-size:.78rem; }
        .btn-outline { background:transparent;border:1px solid var(--glass-border);color:var(--text); }
        .btn-outline:hover { background:rgba(255,255,255,.06); }
        .btn-ghost { background:transparent;color:var(--text-muted);padding:.3rem .6rem;font-size:.8rem; }
        .btn-ghost:hover { color:var(--text); }
        .btn-danger { background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#f87171; }
        .btn-danger:hover { background:rgba(239,68,68,.3); }

        /* ─── FORM ─────────────────────────────────────────────────── */
        .form-row { display:grid;gap:1.1rem;margin-bottom:1.1rem; }
        .form-row-2 { grid-template-columns:1fr 1fr; }
        .form-row-3 { grid-template-columns:1fr 1fr 1fr; }
        .form-group { display:flex;flex-direction:column;gap:.35rem; }
        .form-label { font-size:.8rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em; }
        .form-input,.form-select,.form-textarea {
            width:100%;padding:.65rem .9rem;border-radius:9px;
            background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.11);
            color:var(--text);font-family:'Outfit',sans-serif;font-size:.875rem;
            transition:border-color .2s,box-shadow .2s;
        }
        .form-input:focus,.form-select:focus,.form-textarea:focus {
            outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(14,165,233,.16);
        }
        .form-input::placeholder,.form-textarea::placeholder { color:rgba(148,163,184,.45); }
        .form-select option { background:#0b1a30; }
        .form-textarea { resize:vertical;min-height:80px; }
        .form-error { color:#fca5a5;font-size:.78rem;display:flex;align-items:center;gap:.3rem; }

        /* ─── ALERTS ───────────────────────────────────────────────── */
        .alert { padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.1rem;font-size:.875rem;display:flex;align-items:center;gap:.65rem; }
        .alert-success { background:rgba(16,185,129,.13);border:1px solid rgba(16,185,129,.28);color:#34d399; }
        .alert-error   { background:rgba(239,68,68,.13);border:1px solid rgba(239,68,68,.28);color:#fca5a5; }
        .alert-warning  { background:rgba(245,158,11,.13);border:1px solid rgba(245,158,11,.28);color:#fbbf24; }

        /* ─── LOW STOCK PILL ───────────────────────────────────────── */
        .low-stock { color:#f87171;display:flex;align-items:center;gap:.3rem; }
        .ok-stock  { color:#34d399; }

        /* ─── MOBILE ───────────────────────────────────────────────── */
        .sidebar-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:140; }

        @media (max-width: 768px) {
            .topbar__hamburger { display:block; }
            .topbar__brand { min-width: unset; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.show { display:block; }
            .main-wrap { margin-left:0;padding:1.25rem; }
            .form-row-2,.form-row-3 { grid-template-columns:1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ── TOP BAR ── -->
<header class="topbar">
    <button class="topbar__hamburger" id="hamburger-btn" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars"></i>
    </button>
    <a href="{{ route('staff.dashboard') }}" class="topbar__brand">
        <i class="fa-solid fa-droplet"></i> Aqua De Smiley
    </a>
    <div class="topbar__spacer"></div>
    <div class="topbar__right">
        <span class="role-chip">Staff</span>
        <span class="topbar__user">
            <i class="fa-regular fa-user"></i>
            {{ auth()->user()?->name ?? 'Staff' }}
        </span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</header>

<!-- ── SIDEBAR ── -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar__section-label">Main Menu</div>

    <a href="{{ route('staff.dashboard') }}"
       class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i>
        Dashboard
    </a>

    <a href="{{ route('staff.orders.create') }}"
       class="nav-item {{ request()->routeIs('staff.orders.create') ? 'active' : '' }}">
        <i class="fa-solid fa-circle-plus"></i>
        New Order
    </a>

    <a href="{{ route('staff.orders.index') }}"
       class="nav-item {{ request()->routeIs('staff.orders.*') && !request()->routeIs('staff.orders.create') ? 'active' : '' }}">
        <i class="fa-solid fa-clipboard-list"></i>
        Order Tracking
        @php $pendingCount = \App\Models\Order::whereIn('status',['pending','confirmed'])->count(); @endphp
        @if($pendingCount > 0)
            <span class="badge-blue">{{ $pendingCount }}</span>
        @endif
    </a>

    <a href="{{ route('staff.payments.index') }}"
       class="nav-item {{ request()->routeIs('staff.payments.*') ? 'active' : '' }}">
        <i class="fa-solid fa-cash-register"></i>
        Payment Process
        @php $awaitingCount = \App\Models\Order::where('status', '!=', 'cancelled')->where(fn($q) => $q->where('payment_status', 'unpaid')->orWhere('status', '!=', 'completed')->orWhereNull('released_at'))->count(); @endphp
        @if($awaitingCount > 0)
            <span class="badge" style="background:#0284c7;color:#fff;font-weight:700;">{{ $awaitingCount }}</span>
        @endif
    </a>

    <a href="{{ route('staff.customers.index') }}"
       class="nav-item {{ request()->routeIs('staff.customers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i>
        Customers
    </a>

    <a href="{{ route('staff.supplies.index') }}"
       class="nav-item {{ request()->routeIs('staff.supplies.*') ? 'active' : '' }}">
        <i class="fa-solid fa-boxes-stacked"></i>
        Supplies &amp; Stock
        @php $lowStockCount = \App\Models\Supply::whereColumn('quantity','<=','minimum_stock')->count(); @endphp
        @if($lowStockCount > 0)
            <span class="badge">{{ $lowStockCount }}</span>
        @endif
    </a>

    <!-- Profile footer -->
    <div class="sidebar__footer">
        <div class="sidebar__profile">
            <div class="sidebar__avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'S',0,1)) }}</div>
            <div>
                <div class="sidebar__name">{{ auth()->user()?->name ?? 'Staff' }}</div>
                <div class="sidebar__email">{{ auth()->user()?->email ?? '' }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- ── MAIN CONTENT ── -->
<main class="main-wrap">
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }
</script>
@stack('scripts')
</body>
</html>
