{{-- 
    LAYOUT SPÉCIFIQUE POUR LE CHEF DE PROJET
    🔥 LIEN PLANIFICATION CORRECT (AVANT PROJETS)
--}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chef Projet - ConstrucTrack Lubumbashi')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --sidebar-width: 280px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #d5dddd 0%, #d3d1d5 100%); min-height: 100vh; }
        
        .cp-sidebar {
            position: fixed; left: 0; top: 0; height: 100%; width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: all 0.3s ease; z-index: 1000; overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .cp-sidebar.collapsed { width: 80px; }
        
        .sidebar-logo { padding: 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 1.5rem; }
        .sidebar-logo .logo-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; }
        .sidebar-logo .logo-icon i { font-size: 1.5rem; color: white; }
        .sidebar-logo .logo-text { font-size: 1.1rem; font-weight: 700; color: white; }
        .sidebar-logo .logo-sub { font-size: 0.6rem; color: rgba(255,255,255,0.5); }
        
        .cp-sidebar.collapsed .sidebar-logo .logo-text,
        .cp-sidebar.collapsed .sidebar-logo .logo-sub { display: none; }
        
        .sidebar-nav { padding: 0 0.75rem; }
        .nav-item { margin-bottom: 0.25rem; }
        .nav-link-cp { display: flex; align-items: center; padding: 0.75rem 1rem; border-radius: 12px; color: rgba(255,255,255,0.7); text-decoration: none; transition: all 0.2s; gap: 12px; }
        .nav-link-cp i { width: 22px; font-size: 1.1rem; }
        .nav-link-cp span { font-size: 0.85rem; font-weight: 500; }
        .nav-link-cp:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-link-cp.active { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16,185,129,0.3); }
        
        .cp-sidebar.collapsed .nav-link-cp span { display: none; }
        .cp-sidebar.collapsed .nav-link-cp { justify-content: center; padding: 0.75rem; }
        
        .cp-main { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.3s ease; }
        .cp-main.expanded { margin-left: 80px; }
        
        .cp-topbar { background: white; padding: 0.75rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 999; }
        .toggle-sidebar { background: none; border: none; font-size: 1.25rem; color: #64748b; cursor: pointer; padding: 0.5rem; border-radius: 10px; }
        .page-header h1 { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin: 0; }
        .page-header p { font-size: 0.7rem; color: #64748b; margin: 0; }
        
        .user-menu { display: flex; align-items: center; gap: 1rem; cursor: pointer; }
        .user-avatar { width: 42px; height: 42px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem; }
        .user-info { text-align: right; }
        .user-info .name { font-size: 0.85rem; font-weight: 600; color: #1e293b; margin: 0; }
        .user-info .role { font-size: 0.65rem; color: #64748b; margin: 0; }
        
        .cp-content { padding: 1.5rem; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .alert-cp { border-radius: 16px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }
        .alert-success-cp { background: #d1fae5; color: #059669; border-left: 4px solid #059669; }
        .alert-danger-cp { background: #fee2e2; color: #dc2626; border-left: 4px solid #dc2626; }
        
        @media (max-width: 768px) {
            .cp-sidebar { transform: translateX(-100%); }
            .cp-sidebar.mobile-open { transform: translateX(0); }
            .cp-main { margin-left: 0; }
            .user-info { display: none; }
        }
        .sidebar-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; display: none; }
        .sidebar-overlay.active { display: block; }
    </style>
    @stack('styles')
</head>
<body>

    <aside class="cp-sidebar" id="cpSidebar">
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-chart-line"></i></div>
            <div class="logo-text">ConstrucTrack</div>
            <div class="logo-sub">Chef de Projet</div>
        </div>
        <div class="sidebar-nav">
            {{-- Tableau de bord --}}
            <div class="nav-item">
                <a href="{{ route('chef-projet.dashboard') }}" class="nav-link-cp {{ request()->routeIs('chef-projet.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span>
                </a>
            </div>
            
            {{-- 🔥 PLANIFICATION (AVANT PROJETS) 🔥 --}}
            <div class="nav-item">
                <a href="{{ route('chef-projet.planification.index') }}" class="nav-link-cp {{ request()->routeIs('chef-projet.planification.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i><span>Planification</span>
                </a>
            </div>
            
            {{-- Mes projets (après planification) --}}
            <div class="nav-item">
                <a href="{{ route('chef-projet.projets.index') }}" class="nav-link-cp {{ request()->routeIs('chef-projet.projets.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i><span>Mes projets</span>
                </a>
            </div>
        
            {{-- Suivi avancement --}}
            <div class="nav-item">
                <a href="{{ route('chef-projet.suivi.index') }}" class="nav-link-cp {{ request()->routeIs('chef-projet.suivi.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i><span>Suivi avancement</span>
                </a>
            </div>
            
            {{-- Déconnexion --}}
            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" class="nav-link-cp" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span>
                    </a>
                </form>
            </div>
        </div>
    </aside>
    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <main class="cp-main" id="cpMain">
        <header class="cp-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-sidebar" id="toggleSidebar"><i class="fas fa-bars"></i></button>
                <div class="page-header">
                    <h1>@yield('page-title', 'Tableau de bord')</h1>
                    <p>@yield('page-subtitle', 'Gérez vos planifications et projets')</p>
                </div>
            </div>
            <div class="dropdown">
                <div class="user-menu" data-bs-toggle="dropdown">
                    <div class="user-info">
                        <p class="name">{{ Auth::user()->nom }}</p>
                        <p class="role">Chef de Projet</p>
                    </div>
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->nom, 0, 2)) }}</div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                </ul>
            </div>
        </header>
        
        <div class="cp-content">
            @if(session('success'))
                <div class="alert-cp alert-success-cp"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-cp alert-danger-cp"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('cpSidebar');
        const main = document.getElementById('cpMain');
        const toggleBtn = document.getElementById('toggleSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded');
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