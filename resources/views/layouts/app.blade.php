{{-- resources/views/layouts/app.blade.php --}}
{{-- 
    LAYOUT UNIQUE POUR TOUS LES UTILISATEURS CONNECTÉS
    L'AFFICHAGE DU MENU DÉPEND DU RÔLE DE L'UTILISATEUR
    PAS DE DOUBLON - CHAQUE ACTEUR VOIT SON PROPRE MENU
--}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ConstrucTrack - Gestion de Projets Lubumbashi')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #0e3a8a;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #111827;
            --gray: #6b7280;
            --white: #ffffff;
            --sidebar-width: 280px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.2rem;
        }
        
        .sidebar-header p {
            color: rgba(255,255,255,0.6);
            font-size: 0.7rem;
            margin-top: 0.25rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 0;
        }
        
        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.2rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            gap: 12px;
        }
        
        .nav-link-custom i {
            width: 22px;
            font-size: 1rem;
        }
        
        .nav-link-custom span {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .nav-link-custom:hover,
        .nav-link-custom.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--secondary);
        }
        
        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* ========== TOP HEADER ========== */
        .top-header {
            background: white;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .page-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info .user-name {
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            font-size: 0.85rem;
        }
        
        .user-info .user-role {
            font-size: 0.65rem;
            color: var(--gray);
            margin: 0;
        }
        
        /* ========== CONTENT ========== */
        .content-wrapper {
            padding: 1.5rem;
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* ========== ALERTS ========== */
        .alert-custom {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: none;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #059669;
            border-left: 3px solid #059669;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border-left: 3px solid #dc2626;
        }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .user-info { display: none; }
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-overlay.active { display: block; }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-building"></i> ConstrucTrack</h3>
            <p>Lubumbashi - RDC</p>
        </div>
        
        <div class="sidebar-nav">
            @auth
                {{-- TABLEAU DE BORD (TOUS) --}}
                <div class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span>
                    </a>
                </div>
                
                {{-- ADMIN SEULEMENT --}}
                @if(auth()->user()->role == 'admin')
                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i><span>Utilisateurs</span>
                    </a>
                </div>
                @endif
                
                {{-- CHEF PROJET ET ADMIN --}}
                @if(in_array(auth()->user()->role, ['admin', 'chef_projet']))
                <div class="nav-item">
                    <a href="{{ route('chef-projet.projets.index') }}" class="nav-link-custom {{ request()->routeIs('chef-projet.projets.*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram"></i><span>Projets</span>
                    </a>
                </div>
                @endif
                
                {{-- CHEF CHANTIER ET ADMIN --}}
                @if(in_array(auth()->user()->role, ['admin', 'chef_chantier']))
                <div class="nav-item">
                    <a href="{{ route('chef-chantier.etapes.index') }}" class="nav-link-custom {{ request()->routeIs('chef-chantier.etapes.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i><span>Mes étapes</span>
                    </a>
                </div>
                @endif
                
                {{-- MON PROFIL (ADMIN SEULEMENT) --}}
                @if(auth()->user()->role == 'admin')
                <div class="nav-item">
                    <a href="{{ route('profile.edit') }}" class="nav-link-custom {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i><span>Mon profil</span>
                    </a>
                </div>
                @endif
                
                {{-- DÉCONNEXION (TOUS) --}}
                <div class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" class="nav-link-custom" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span>
                        </a>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    {{-- OVERLAY MOBILE --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- MAIN CONTENT --}}
    <div class="main-content" id="mainContent">
        <div class="top-header">
            <h4 class="page-title">@yield('page-title', 'Tableau de bord')</h4>
            
            @auth
            <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-info">
                    <p class="user-name">{{ auth()->user()->nom }}</p>
                    <p class="user-role">
                        @if(auth()->user()->role == 'admin') Administrateur
                        @elseif(auth()->user()->role == 'chef_projet') Chef de Projet
                        @else Chef de Chantier
                        @endif
                    </p>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                {{-- MON PROFIL (ADMIN SEULEMENT) --}}
                @if(auth()->user()->role == 'admin')
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-id-card me-2"></i> Mon profil</a></li>
                <li><hr class="dropdown-divider"></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
            @endauth
        </div>
        
        <div class="content-wrapper fade-in">
            @if(session('success'))
                <div class="alert-custom alert-success"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert-custom alert-danger"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert-custom alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('active');
        }
        
        overlay.addEventListener('click', toggleSidebar);
        
        // Fermer sidebar sur click en dehors
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const isClickInside = sidebar.contains(event.target);
                const isClickOnToggle = event.target.closest('.toggle-sidebar-btn');
                if (!isClickInside && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('active');
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>