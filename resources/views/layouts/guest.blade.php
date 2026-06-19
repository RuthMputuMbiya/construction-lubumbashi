{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ConstrucTrack - Gestion Projets Construction Lubumbashi')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #0e3a8a;
            --primary-light: #3b82f6;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #111827;
            --gray: #6b7280;
            --light: #f3f4f6;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Navbar Guest */
        .guest-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .guest-navbar.scrolled {
            padding: 0.5rem 0;
            background: white;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark);
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }
        
        .nav-link:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(26,86,219,0.3);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            padding: 8px 23px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-gradient:hover {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-color: transparent;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }
        
        /* Feature Cards */
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .feature-icon i {
            font-size: 2rem;
            color: white;
        }
        
        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--light);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
        }
        
        .footer a {
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--primary-light);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Navbar Guest --}}
    <nav class="guest-navbar" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-building me-2"></i>
                    ConstrucTrack
                </a>
                
                <button class="navbar-toggler d-none" type="button" data-bs-toggle="collapse" data-bs-target="#guestNav">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('features') }}" class="nav-link d-none d-md-block">Fonctionnalités</a>
                    <a href="{{ route('about') }}" class="nav-link d-none d-md-block">À propos</a>
                    <a href="{{ route('contact') }}" class="nav-link d-none d-md-block">Contact</a>
                    
                    @if(Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-gradient">
                                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-outline-gradient">
                                <i class="fas fa-sign-in-alt me-2"></i>Connexion
                            </a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-gradient">
                                    <i class="fas fa-user-plus me-2"></i>Inscription
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white mb-3">ConstrucTrack</h5>
                    <p>Solution innovante de gestion de projets de construction à Lubumbashi, RDC.</p>
                    <div class="mt-3">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white mb-3">Liens rapides</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}">Accueil</a></li>
                        <li class="mb-2"><a href="{{ route('features') }}">Fonctionnalités</a></li>
                        <li class="mb-2"><a href="{{ route('about') }}">À propos</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Centre d'aide</a></li>
                        <li class="mb-2"><a href="#">FAQ</a></li>
                        <li class="mb-2"><a href="#">Politique de confidentialité</a></li>
                        <li class="mb-2"><a href="#">Conditions d'utilisation</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="text-white mb-3">Contact</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Lubumbashi, RDC</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+243 123 456 789</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>contact@construc-track.com</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary mt-3">
            <div class="text-center pt-3">
                <p class="mb-0">&copy; 2024 ConstrucTrack - Tous droits réservés. Lubumbashi, RDC.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>