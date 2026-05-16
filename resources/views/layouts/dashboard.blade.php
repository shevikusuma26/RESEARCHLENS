<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ResearchLens</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --bg-base:        #f0f4ff;
            --bg-surface:     #ffffff;
            --bg-card:        #ffffff;
            --bg-card-hover:  #f8faff;
            --bg-sidebar:     #ffffff;
            --border:         rgba(99,102,241,0.12);
            --border-hover:   rgba(99,102,241,0.35);
            --text-primary:   #1e293b;
            --text-secondary: #475569;
            --text-muted:     #94a3b8;
            --accent:         #6366f1;
            --accent-light:   #eef2ff;
            --accent-cyan:    #06b6d4;
            --accent-cyan-l:  #ecfeff;
            --accent-purple:  #8b5cf6;
            --accent-purple-l:#f5f3ff;
            --accent-green:   #10b981;
            --accent-green-l: #ecfdf5;
            --accent-amber:   #f59e0b;
            --accent-amber-l: #fffbeb;
            --accent-pink:    #ec4899;
            --accent-pink-l:  #fdf2f8;
            --accent-red:     #ef4444;
            --accent-red-l:   #fef2f2;
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.05),0 1px 2px rgba(0,0,0,0.04);
            --shadow:         0 4px 12px rgba(99,102,241,0.08),0 1px 3px rgba(0,0,0,0.05);
            --shadow-hover:   0 8px 24px rgba(99,102,241,0.14),0 2px 6px rgba(0,0,0,0.06);
            --radius:         14px;
            --sidebar-w:      256px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg-base); color:var(--text-primary); min-height:100vh; overflow-x:hidden; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position:fixed; top:0; left:0; width:var(--sidebar-w); height:100vh;
            background:var(--bg-sidebar); border-right:1px solid var(--border);
            display:flex; flex-direction:column; z-index:100;
            box-shadow:2px 0 8px rgba(99,102,241,0.06);
        }
        .sidebar-logo {
            padding:20px 18px; display:flex; align-items:center; gap:10px;
            border-bottom:1px solid var(--border);
        }
        .logo-icon {
            width:38px; height:38px; border-radius:10px;
            background:linear-gradient(135deg,var(--accent),var(--accent-cyan));
            display:flex; align-items:center; justify-content:center; color:#fff; font-size:17px;
            box-shadow:0 4px 10px rgba(99,102,241,0.3);
        }
        .logo-text { font-size:17px; font-weight:800; color:var(--text-primary); }
        .logo-sub  { font-size:10px; color:var(--text-muted); }
        .sidebar-nav { flex:1; padding:14px 10px; overflow-y:auto; }
        .nav-section-label {
            font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase;
            letter-spacing:0.8px; padding:6px 8px 4px;
        }
        .nav-item {
            display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:10px;
            color:var(--text-secondary); text-decoration:none; font-size:13.5px; font-weight:500;
            transition:all 0.18s; margin-bottom:2px;
        }
        .nav-item:hover { background:var(--accent-light); color:var(--accent); }
        .nav-item.active {
            background:linear-gradient(135deg,var(--accent-light),#e0f2fe);
            color:var(--accent); font-weight:600;
            box-shadow:inset 3px 0 0 var(--accent);
        }
        .nav-item i { width:16px; text-align:center; font-size:14px; }
        .nav-badge {
            margin-left:auto; background:var(--accent-pink); color:#fff;
            font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px;
        }
        .sidebar-footer { padding:14px 10px; border-top:1px solid var(--border); }
        .user-card {
            display:flex; align-items:center; gap:10px; padding:10px 12px;
            border-radius:10px; background:var(--bg-base); border:1px solid var(--border);
        }
        .user-avatar { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--accent); }
        .user-name { font-size:13px; font-weight:600; color:var(--text-primary); }
        .user-role { font-size:11px; color:var(--text-muted); }

        /* ─── MAIN WRAPPER ─── */
        .main-wrapper { margin-left:var(--sidebar-w); min-height:100vh; display:flex; flex-direction:column; }

        /* ─── TOPBAR ─── */
        .topbar {
            position:sticky; top:0; z-index:90;
            background:rgba(255,255,255,0.92); backdrop-filter:blur(14px);
            border-bottom:1px solid var(--border); padding:0 24px; height:60px;
            display:flex; align-items:center; justify-content:space-between;
            box-shadow:0 1px 4px rgba(99,102,241,0.06);
        }
        .topbar-title { font-size:17px; font-weight:700; color:var(--text-primary); }
        .topbar-right  { display:flex; align-items:center; gap:12px; }
        .icon-btn {
            width:36px; height:36px; background:var(--bg-base); border:1px solid var(--border);
            border-radius:9px; display:flex; align-items:center; justify-content:center;
            color:var(--text-secondary); cursor:pointer; text-decoration:none; transition:all 0.2s;
        }
        .icon-btn:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }
        .notif-dot {
            position:absolute; top:5px; right:5px; width:8px; height:8px;
            background:var(--accent-pink); border-radius:50%; border:2px solid #fff;
        }
        .notif-wrapper { position:relative; }

        /* ─── PAGE CONTENT ─── */
        .page-content { flex:1; padding:24px; }

        /* ─── CARDS ─── */
        .card {
            background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius);
            padding:22px; box-shadow:var(--shadow); transition:all 0.25s;
        }
        .card:hover { box-shadow:var(--shadow-hover); border-color:var(--border-hover); }
        .stat-card {
            background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius);
            padding:20px; box-shadow:var(--shadow); transition:all 0.25s;
            display:flex; align-items:center; justify-content:space-between;
        }
        .stat-card:hover { box-shadow:var(--shadow-hover); transform:translateY(-2px); }
        .stat-label { font-size:12px; color:var(--text-muted); font-weight:500; margin-bottom:6px; }
        .stat-value { font-size:28px; font-weight:800; color:var(--text-primary); line-height:1; }
        .stat-icon {
            width:46px; height:46px; border-radius:12px;
            display:flex; align-items:center; justify-content:center; font-size:19px;
        }
        .si-blue   { background:var(--accent-light);    color:var(--accent); }
        .si-cyan   { background:var(--accent-cyan-l);   color:var(--accent-cyan); }
        .si-purple { background:var(--accent-purple-l); color:var(--accent-purple); }
        .si-green  { background:var(--accent-green-l);  color:var(--accent-green); }
        .si-amber  { background:var(--accent-amber-l);  color:var(--accent-amber); }
        .si-pink   { background:var(--accent-pink-l);   color:var(--accent-pink); }

        /* ─── BADGES ─── */
        .badge {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
        }
        .badge-blue   { background:var(--accent-light);    color:var(--accent); }
        .badge-cyan   { background:var(--accent-cyan-l);   color:var(--accent-cyan); }
        .badge-purple { background:var(--accent-purple-l); color:var(--accent-purple); }
        .badge-green  { background:var(--accent-green-l);  color:var(--accent-green); }
        .badge-amber  { background:var(--accent-amber-l);  color:var(--accent-amber); }
        .badge-pink   { background:var(--accent-pink-l);   color:var(--accent-pink); }
        .badge-red    { background:var(--accent-red-l);    color:var(--accent-red); }

        /* ─── BUTTONS ─── */
        .btn {
            display:inline-flex; align-items:center; gap:7px; padding:9px 18px;
            border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer;
            border:none; text-decoration:none; transition:all 0.2s; font-family:'Inter',sans-serif;
        }
        .btn-primary {
            background:linear-gradient(135deg,var(--accent),#818cf8);
            color:#fff; box-shadow:0 4px 12px rgba(99,102,241,0.3);
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(99,102,241,0.4); }
        .btn-primary:disabled { opacity:0.65; cursor:not-allowed; transform:none; }
        .btn-outline {
            background:var(--bg-card); border:1.5px solid var(--border); color:var(--text-secondary);
        }
        .btn-outline:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-light); }
        .btn-danger {
            background:var(--accent-red-l); border:1px solid rgba(239,68,68,0.25); color:var(--accent-red);
        }
        .btn-danger:hover { background:#fee2e2; }

        /* ─── FORM INPUTS ─── */
        .form-input {
            width:100%; background:var(--bg-base); border:1.5px solid var(--border);
            border-radius:9px; padding:10px 14px; color:var(--text-primary);
            font-size:13.5px; font-family:'Inter',sans-serif; outline:none; transition:all 0.2s;
        }
        .form-input:focus { border-color:var(--accent); background:#fff; box-shadow:0 0 0 3px rgba(99,102,241,0.1); }
        .form-input::placeholder { color:var(--text-muted); }
        select.form-input option { background:#fff; color:var(--text-primary); }
        textarea.form-input { resize:vertical; min-height:90px; }
        .form-label { font-size:12px; font-weight:600; color:var(--text-secondary); display:block; margin-bottom:5px; }

        /* ─── PROGRESS ─── */
        .progress-bar { height:6px; background:var(--bg-base); border-radius:20px; overflow:hidden; border:1px solid var(--border); }
        .progress-fill { height:100%; border-radius:20px; transition:width 1s ease; }

        /* ─── TABLE ─── */
        .data-table { width:100%; border-collapse:collapse; }
        .data-table th {
            padding:11px 14px; text-align:left; font-size:11px; font-weight:700;
            color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;
            border-bottom:2px solid var(--border); background:var(--bg-base);
        }
        .data-table th:first-child { border-radius:8px 0 0 0; }
        .data-table th:last-child  { border-radius:0 8px 0 0; }
        .data-table td {
            padding:13px 14px; font-size:13.5px; color:var(--text-primary);
            border-bottom:1px solid var(--border);
        }
        .data-table tr:hover td { background:var(--bg-card-hover); }

        /* ─── SECTION HEADER ─── */
        .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
        .section-title { font-size:15px; font-weight:700; color:var(--text-primary); }
        .section-sub   { font-size:12px; color:var(--text-muted); margin-top:2px; }

        /* ─── NOTIFICATION DROPDOWN ─── */
        .notif-dropdown {
            position:absolute; top:44px; right:0; width:320px;
            background:var(--bg-card); border:1px solid var(--border); border-radius:14px;
            box-shadow:0 16px 40px rgba(99,102,241,0.15); display:none; z-index:200;
        }
        .notif-dropdown.open { display:block; }
        .notif-header {
            padding:14px 18px 10px; border-bottom:1px solid var(--border);
            display:flex; justify-content:space-between; align-items:center;
        }
        .notif-item {
            padding:11px 18px; display:flex; gap:10px; align-items:flex-start;
            border-bottom:1px solid rgba(99,102,241,0.06); cursor:pointer; transition:background 0.15s;
        }
        .notif-item:hover { background:var(--bg-card-hover); }
        .notif-item.unread { background:var(--accent-light); }
        .notif-icon-wrap {
            width:34px; height:34px; border-radius:9px;
            display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px;
        }
        .notif-text { font-size:13px; color:var(--text-primary); font-weight:500; }
        .notif-sub  { font-size:11px; color:var(--text-muted); margin-top:2px; }

        /* ─── FLASH MESSAGES ─── */
        .flash-success {
            background:var(--accent-green-l); border:1px solid rgba(16,185,129,0.25);
            color:#047857; padding:11px 16px; border-radius:9px; margin-bottom:18px;
            display:flex; align-items:center; gap:8px; font-size:13.5px;
        }
        .flash-error {
            background:var(--accent-red-l); border:1px solid rgba(239,68,68,0.25);
            color:var(--accent-red); padding:11px 16px; border-radius:9px; margin-bottom:18px;
            display:flex; align-items:center; gap:8px; font-size:13.5px;
        }

        /* ─── QUICK ACTION ─── */
        .quick-action {
            display:flex; align-items:center; gap:12px; padding:13px; border-radius:11px;
            background:var(--bg-base); border:1.5px solid var(--border); text-decoration:none;
            color:var(--text-primary); transition:all 0.2s; margin-bottom:9px;
        }
        .quick-action:hover { border-color:var(--accent); background:var(--accent-light); transform:translateX(3px); }
        .qa-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; }

        /* ─── MODAL ─── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.35); z-index:500; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
        .modal-overlay.open { display:flex; }
        .modal-box { background:var(--bg-card); border:1px solid var(--border); border-radius:18px; padding:28px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 24px 60px rgba(99,102,241,0.18); }

        /* ─── ANIMATIONS ─── */
        @keyframes fadeInUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        @keyframes spin { from{transform:rotate(0)} to{transform:rotate(360deg)} }
        .animate-in { animation:fadeInUp 0.45s ease forwards; }
        .d1{animation-delay:0.05s;opacity:0} .d2{animation-delay:0.1s;opacity:0}
        .d3{animation-delay:0.15s;opacity:0} .d4{animation-delay:0.2s;opacity:0}

        /* ─── SCROLLBAR ─── */
        ::-webkit-scrollbar { width:5px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:rgba(99,102,241,0.18); border-radius:10px; }

        /* ─── RESPONSIVE ─── */
        @media(max-width:768px) {
            .sidebar { transform:translateX(-100%); transition:transform 0.3s; }
            .sidebar.open { transform:translateX(0); }
            .main-wrapper { margin-left:0; }
            .page-content { padding:14px; }
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fas fa-microscope"></i></div>
        <div>
            <div class="logo-text">ResearchLens</div>
            <div class="logo-sub">Academic Analytics</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high"></i> Dashboard
        </a>
        <a href="{{ route('dashboard.projects') }}" class="nav-item {{ request()->routeIs('dashboard.projects') ? 'active' : '' }}">
            <i class="fas fa-folder-open"></i> Proyek Saya
        </a>
        <a href="{{ route('dashboard.similarity') }}" class="nav-item {{ request()->routeIs('dashboard.similarity') ? 'active' : '' }}">
            <i class="fas fa-code-compare"></i> Analisis Similarity
        </a>
        <a href="{{ route('dashboard.novelty') }}" class="nav-item {{ request()->routeIs('dashboard.novelty') ? 'active' : '' }}">
            <i class="fas fa-lightbulb"></i> Novelty & Rekomendasi
        </a>

        <div class="nav-section-label" style="margin-top:14px">Akun</div>
        <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section-label" style="margin-top:14px">Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="fas fa-shield-halved"></i> Panel Admin
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" class="user-avatar">
            <div style="flex:1;min-width:0">
                <div class="user-name">{{ Str::limit(auth()->user()->name, 18) }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- MAIN -->
<div class="main-wrapper">
    <!-- TOPBAR -->
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:10px">
            <button id="mobileMenuBtn" onclick="document.getElementById('sidebar').classList.toggle('open')"
                style="display:none;background:none;border:none;color:var(--text-secondary);font-size:18px;cursor:pointer">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">@yield('page-title','Dashboard')</div>
        </div>

        <div class="topbar-right">
            <!-- Notification Bell -->
            <div class="notif-wrapper">
                <div class="icon-btn" style="position:relative" id="notifBtn" onclick="toggleNotif()">
                    <i class="fas fa-bell" style="font-size:15px"></i>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="notif-dot"></span>
                    @endif
                </div>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span style="font-size:13px;font-weight:700">Notifikasi</span>
                        @if(isset($unreadNotifications) && $unreadNotifications > 0)
                            <span class="badge badge-pink">{{ $unreadNotifications }} baru</span>
                        @endif
                    </div>
                    @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                        @foreach($recentNotifications as $notif)
                        <div class="notif-item {{ !$notif->is_read ? 'unread' : '' }}">
                            <div class="notif-icon-wrap {{ $notif->type==='success'?'si-green':($notif->type==='warning'?'si-amber':'si-blue') }}">
                                <i class="fas {{ $notif->type==='success'?'fa-check':($notif->type==='warning'?'fa-triangle-exclamation':'fa-bell') }}"></i>
                            </div>
                            <div>
                                <div class="notif-text">{{ $notif->title }}</div>
                                <div class="notif-sub">{{ Str::limit($notif->message,55) }}</div>
                                <div class="notif-sub" style="margin-top:3px;color:var(--accent)">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:13px">
                            <i class="fas fa-bell-slash" style="font-size:22px;margin-bottom:8px;display:block;opacity:0.4"></i>
                            Belum ada notifikasi
                        </div>
                    @endif
                </div>
            </div>

            <!-- User + Logout -->
            <div style="display:flex;align-items:center;gap:8px">
                <img src="{{ auth()->user()->profile_photo_url }}" alt=""
                    style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--accent)">
                <span style="font-size:13px;font-weight:600;color:var(--text-primary)">
                    {{ Str::limit(auth()->user()->name,15) }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="padding:7px 12px;font-size:12px">
                        <i class="fas fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div style="padding:0 24px;padding-top:14px">
        @if(session('success'))
            <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="flash-error"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif
    </div>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        @yield('content')
    </main>
</div>

<script>
function toggleNotif() {
    document.getElementById('notifDropdown').classList.toggle('open');
}
document.addEventListener('click', e => {
    const w = document.getElementById('notifWrapper') || document.querySelector('.notif-wrapper');
    if (!w?.contains(e.target)) document.getElementById('notifDropdown')?.classList.remove('open');
});
// Mobile menu
const mb = document.getElementById('mobileMenuBtn');
const showMobile = () => { if(mb) mb.style.display = window.innerWidth<=768?'block':'none'; };
showMobile(); window.addEventListener('resize', showMobile);
// Stat counters
document.querySelectorAll('[data-count]').forEach(el => {
    const t = parseFloat(el.dataset.count), isF = el.dataset.count.includes('.');
    let s = 0, step = t/40;
    const timer = setInterval(() => {
        s += step; if(s >= t){ s = t; clearInterval(timer); }
        el.textContent = isF ? s.toFixed(1) : Math.floor(s);
    }, 28);
});
</script>
@yield('scripts')
</body>
</html>
