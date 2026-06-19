{{-- resources/views/layouts/chef-chantier.blade.php --}}
{{-- 
    LAYOUT SPÉCIFIQUE POUR LE CHEF DE CHANTIER
    EXPLICATION: Interface dédiée aux chefs de chantier pour le suivi terrain
--}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chef Chantier - ConstrucTrack Lubumbashi')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <!-- Flatpickr pour date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        /* ============================================
           VARIABLES & RESET
        ============================================ */
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #10b981;
            --secondary-dark: #059669;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --white: #ffffff;
            --sidebar-width: 280px;
            --header-height: 65px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e7e9f2 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        /* ============================================
           SIDEBAR
        ============================================ */
        .cc-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }
        
        .cc-sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar-logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }
        
        .sidebar-logo .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
        }
        
        .sidebar-logo .logo-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .sidebar-logo .logo-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
        }
        
        .sidebar-logo .logo-sub {
            font-size: 0.6rem;
            color: rgba(255,255,255,0.5);
        }
        
        .cc-sidebar.collapsed .sidebar-logo .logo-text,
        .cc-sidebar.collapsed .sidebar-logo .logo-sub {
            display: none;
        }
        
        /* Navigation */
        .sidebar-nav {
            padding: 0 0.75rem;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link-cc {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            gap: 12px;
        }
        
        .nav-link-cc i {
            width: 22px;
            font-size: 1.1rem;
        }
        
        .nav-link-cc span {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .nav-link-cc:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link-cc.active {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        }
        
        .cc-sidebar.collapsed .nav-link-cc span {
            display: none;
        }
        
        .cc-sidebar.collapsed .nav-link-cc {
            justify-content: center;
            padding: 0.75rem;
        }
        
        /* Badge retard */
        .nav-badge-danger {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 0.6rem;
            padding: 0.15rem 0.4rem;
            border-radius: 50px;
        }
        
        /* ============================================
           MAIN CONTENT
        ============================================ */
        .cc-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .cc-main.expanded {
            margin-left: 80px;
        }
        
        /* ============================================
           TOPBAR
        ============================================ */
        .cc-topbar {
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
        
        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 10px;
        }
        
        .toggle-sidebar:hover {
            background: #f1f5f9;
            color: #f59e0b;
        }
        
        .page-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .page-header p {
            font-size: 0.7rem;
            color: #64748b;
            margin: 0;
        }
        
        /* User menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info .name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .user-info .role {
            font-size: 0.65rem;
            color: #64748b;
            margin: 0;
        }
        
        /* Date et heure */
        .current-datetime {
            background: #f1f5f9;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            color: #1e293b;
        }
        
        /* ============================================
           CONTENT
        ============================================ */
        .cc-content {
            padding: 1.5rem;
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* ============================================
           CARTES SPÉCIFIQUES CHEF CHANTIER
        ============================================ */
        .task-card {
            background: white;
            border-radius: 20px;
            padding: 1.25rem;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid;
            margin-bottom: 1rem;
        }
        
        .task-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .task-card.urgent { border-left-color: #ef4444; }
        .task-card.progress { border-left-color: #f59e0b; }
        .task-card.completed { border-left-color: #10b981; }
        .task-card.pending { border-left-color: #3b82f6; }
        
        .task-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        
        .task-project {
            font-size: 0.7rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }
        
        .task-deadline {
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
        }
        
        .deadline-warning {
            color: #ef4444;
            font-weight: 500;
        }
        
        .progress-container {
            margin: 0.75rem 0;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            margin-bottom: 0.25rem;
        }
        
        .progress-bar-custom {
            background: #e2e8f0;
            border-radius: 10px;
            height: 6px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #10b981, #3b82f6);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        /* ============================================
           ALERTES
        ============================================ */
        .alert-cc {
            border-radius: 16px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success-cc {
            background: #d1fae5;
            color: #059669;
            border-left: 4px solid #059669;
        }
        
        .alert-danger-cc {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .cc-sidebar {
                transform: translateX(-100%);
            }
            
            .cc-sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .cc-main {
                margin-left: 0;
            }
            
            .user-info {
                display: none;
            }
            
            .current-datetime {
                display: none;
            }
        }
        
        /* Overlay mobile */
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
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Scrollbar */
        .cc-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .cc-sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        
        .cc-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- ============================================
         SIDEBAR CHEF CHANTIER
    ============================================ -->
    <aside class="cc-sidebar" id="ccSidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-hard-hat"></i>
            </div>
            <div class="logo-text">ConstrucTrack</div>
            <div class="logo-sub">Chef de Chantier</div>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('chef-chantier.dashboard') }}" class="nav-link-cc {{ request()->routeIs('chef-chantier.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('chef-chantier.etapes.index') }}" class="nav-link-cc {{ request()->routeIs('chef-chantier.etapes.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Suivi avancement</span>
                    @php
                        $etapesRetard = \App\Models\Planification::where('id_chef_chantier', auth()->id())
                            ->where('statut', 'en_retard')
                            ->count();
                    @endphp
                    @if($etapesRetard > 0)
                        <span class="nav-badge-danger">{{ $etapesRetard }}</span>
                    @endif
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('chef-chantier.planning.index') }}" class="nav-link-cc {{ request()->routeIs('chef-chantier.planning.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Mon planning</span>
                </a>
            </div>
           
            <div class="nav-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" class="nav-link-cc" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </form>
            </div>
        </div>
    </aside>
    
    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- ============================================
         MAIN CONTENT
    ============================================ -->
    <main class="cc-main" id="ccMain">
        <header class="cc-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-header">
                    <h1>@yield('page-title', 'Tableau de bord')</h1>
                    <p>@yield('page-subtitle', 'Gérez vos étapes et votre avancement terrain')</p>
                </div>
            </div>
            
            <div class="user-menu">
                <div class="current-datetime" id="currentDateTime"></div>
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-3" data-bs-toggle="dropdown">
                        <div class="user-info">
                            <p class="name">{{ Auth::user()->nom }}</p>
                            <p class="role">Chef de Chantier</p>
                        </div>
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->nom, 0, 2)) }}
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i>Mon profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <div class="cc-content">
            @if(session('success'))
                <div class="alert-cc alert-success-cc">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-cc alert-danger-cc">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('ccSidebar');
        const main = document.getElementById('ccMain');
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
        
        // Date et heure en temps réel
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').innerHTML = now.toLocaleDateString('fr-FR', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);
    </script>
    
    @stack('scripts')
</body>
</html>