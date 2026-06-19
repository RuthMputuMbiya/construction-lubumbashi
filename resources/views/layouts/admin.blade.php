{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ConstrucTrack Lubumbashi')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        
        /* ========== SIDEBAR ========== */
        .admin-sidebar {
            position: fixed; left: 0; top: 0; height: 100%; width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s; z-index: 1000; overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        .sidebar-logo { padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 1.5rem; }
        .sidebar-logo .logo-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #1a56db, #0e3a8a); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; }
        .sidebar-logo .logo-icon i { font-size: 1.5rem; color: white; }
        .sidebar-logo .logo-text { font-size: 1.1rem; font-weight: 700; color: white; text-align: center; }
        .sidebar-logo .logo-sub { font-size: 0.6rem; color: rgba(255,255,255,0.5); text-align: center; }
        
        .sidebar-nav { padding: 0 0.75rem; }
        .nav-item { margin-bottom: 0.25rem; }
        .nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; border-radius: 12px; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.2s; gap: 12px; }
        .nav-link i { width: 22px; font-size: 1.1rem; }
        .nav-link span { font-size: 0.85rem; font-weight: 500; }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white; box-shadow: 0 4px 12px rgba(26,86,219,0.3); }
        
        /* ========== MAIN CONTENT ========== */
        .admin-main { margin-left: 280px; min-height: 100vh; }
        .admin-topbar { background: white; padding: 0.75rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 999; }
        .page-title { font-size: 1.1rem; font-weight: 600; color: #1f2937; margin: 0; }
        .user-menu { display: flex; align-items: center; gap: 1rem; cursor: pointer; }
        .user-avatar { width: 42px; height: 42px; background: linear-gradient(135deg, #1a56db, #0e3a8a); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem; }
        .user-info { text-align: right; }
        .user-info .name { font-size: 0.85rem; font-weight: 600; color: #1f2937; margin: 0; }
        .user-info .role { font-size: 0.65rem; color: #64748b; margin: 0; }
        .admin-content { padding: 1.5rem; animation: fadeIn 0.4s ease-out; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Alertes */
        .alert-custom { border-radius: 16px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }
        .alert-success { background: #d1fae5; color: #059669; border-left: 4px solid #059669; }
        .alert-danger { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        .alert-warning { background: #fef3c7; color: #d97706; border-left: 4px solid #d97706; }
        
        /* Cartes */
        .stat-card { background: white; border-radius: 20px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; border: 1px solid #edf2f7; }
        .stat-icon { width: 55px; height: 55px; border-radius: 16px; display: flex; align-items: center; justify-content: center; }
        .stat-icon i { font-size: 1.5rem; }
        .stat-value { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .stat-label { font-size: 0.7rem; color: #6b7280; margin: 0; }
        
        /* Tableaux */
        .table-container { background: white; border-radius: 20px; overflow: hidden; border: 1px solid #edf2f7; }
        .table-custom { width: 100%; border-collapse: collapse; }
        .table-custom th { padding: 1rem; background: #f8fafc; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-align: left; }
        .table-custom td { padding: 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        /* Boutons */
        .btn-primary-custom { background: linear-gradient(135deg, #1a56db, #0e3a8a); color: white; padding: 0.5rem 1.2rem; border-radius: 40px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem; border: none; cursor: pointer; }
        .btn-outline-custom { background: transparent; border: 1px solid #1a56db; color: #1a56db; padding: 0.5rem 1.2rem; border-radius: 40px; text-decoration: none; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .btn-danger-custom { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 0.5rem 1.2rem; border-radius: 40px; border: none; cursor: pointer; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.mobile-open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .user-info { display: none; }
        }
        .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; display: none; }
        .sidebar-overlay.active { display: block; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR ADMIN --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text">ConstrucTrack</div>
            <div class="logo-sub">Administration • Lubumbashi</div>
        </div>
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i><span>Utilisateurs</span>
                </a>
            </div>
            {{-- 🔥 AJOUT DU LIEN PROJETS 🔥 --}}
            <div class="nav-item">
                <a href="{{ route('admin.projets.index') }}" class="nav-link {{ request()->routeIs('admin.projets.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i><span>Projets</span>
                </a>
           
            <div class="nav-item">
                <a href="{{ route('admin.profil.index') }}" class="nav-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i><span>Mon profil</span>
                </a>
            </div>
            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span>
                    </a>
                </form>
            </div>
        </div>
    </aside>
    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <main class="admin-main" id="adminMain">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-sidebar" id="toggleSidebar" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Tableau de bord')</h1>
            </div>
            <div class="dropdown">
                <div class="user-menu" data-bs-toggle="dropdown">
                    <div class="user-info">
                        <p class="name">{{ Auth::user()->nom }}</p>
                        <p class="role">Administrateur</p>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->nom, 0, 2)) }}
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.profil.index') }}"><i class="fas fa-user-circle me-2"></i>Mon profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                </ul>
            </div>
        </header>
        
        <div class="admin-content">
            @if(session('success'))
                <div class="alert-custom alert-success"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-custom alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert-custom alert-warning"><i class="fas fa-exclamation-circle me-2"></i> {{ session('warning') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    </script>
    @stack('scripts')
</body>
</html>