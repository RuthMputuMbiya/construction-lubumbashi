{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ConstrucTrack - Solution pro de gestion de projets de construction | Lubumbashi</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS (Animations) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background-color: #f8fafc;
        }

        /* ========== COULEURS PROFESSIONNELLES ========== */
        :root {
            --primary: #2e6d57;
            --primary-dark: #4f8773;
            --primary-light: #2c604e;
            --secondary: #d4af37;
            --secondary-dark: #b8941e;
            --accent: #e67e22;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f1f5f9;
            --white: #ffffff;
            --danger: #ef4444;
            --success: #10b981;
        }

        /* ========== NAVBAR ========== */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            padding: 0.8rem 0;
        }

        .navbar-custom.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .logo-text {
            font-size: 1.7rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-sub {
            font-size: 0.7rem;
            letter-spacing: 1px;
        }

        .nav-link-custom {
            font-weight: 500;
            color: var(--dark);
            transition: 0.3s;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .nav-link-custom:hover {
            color: var(--primary);
            background: rgba(15, 59, 44, 0.05);
        }

        .btn-login {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 40px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: var(--primary);
            color: white;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: 40px;
            padding: 0.5rem 1.8rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(15, 59, 44, 0.25);
            transition: 0.3s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 59, 44, 0.35);
            color: white;
        }

        /* ========== HERO SECTION ========== */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            position: relative;
            overflow: hidden;
            min-height: 85vh;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.08)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .hero-title {
            font-size: 3.3rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero-highlight {
            color: var(--secondary);
            position: relative;
            display: inline-block;
        }

        .hero-highlight::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--secondary);
            opacity: 0.3;
            border-radius: 10px;
        }

        .btn-hero {
            background: var(--secondary);
            color: var(--primary-dark);
            border-radius: 50px;
            padding: 0.9rem 2rem;
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-hero:hover {
            background: var(--secondary-dark);
            transform: translateY(-3px);
            color: white;
        }

        .btn-hero-outline {
            border: 2px solid white;
            color: white;
            border-radius: 50px;
            padding: 0.85rem 2rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-hero-outline:hover {
            background: white;
            color: var(--primary);
        }

        /* ========== CARTES FONCTIONNALITÉS ========== */
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 2rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
            border: 1px solid #eef2ff;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 40px rgba(15, 59, 44, 0.12);
            border-color: var(--primary-light);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 2rem;
            color: white;
        }

        /* ========== STATS ========== */
        .stats-section {
            background: linear-gradient(120deg, #f8fafc 0%, #eef2ff 100%);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* ========== PRIX ========== */
        .pricing-card {
            background: white;
            border-radius: 30px;
            padding: 2rem;
            transition: 0.3s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .pricing-card.popular {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            transform: scale(1.02);
            border: 2px solid var(--secondary);
        }

        .popular-badge {
            background: var(--secondary);
            color: var(--primary-dark);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .price {
            font-size: 3rem;
            font-weight: 800;
        }

        /* ========== TÉMOIGNAGES ========== */
        .testimonial-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-avatar {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
        }

        /* ========== FOOTER ========== */
        .footer {
            background: #0f172a;
            color: #cbd5e1;
        }

        .footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer a:hover {
            color: var(--secondary);
        }

        /* ========== ANIMATIONS ========== */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .float-animation {
            animation: float 4s ease-in-out infinite;
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
            animation: fadeInUp 0.6s ease-out forwards;
        }
    </style>
</head>
<body>

    <!-- ========== NAVBAR ========== -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-hard-hat fs-2" style="color: var(--primary);"></i>
                    <div>
                        <span class="logo-text">Construc<span style="color:var(--secondary);">Track</span></span>
                        <div class="logo-sub text-muted">Lubumbashi • RDC</div>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#accueil">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#fonctionnalites">Fonctionnalités</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#avantages">Avantages</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#tarifs">Tarifs</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#contact">Contact</a></li>
                </ul>
                <div class="d-flex gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-register"><i class="fas fa-tachometer-alt me-2"></i>Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-login">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-register">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== HERO SECTION ========== -->
    <section id="accueil" class="hero-section pt-5 mt-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white" data-aos="fade-right" data-aos-duration="1000">
                    <div class="mb-4">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                            <i class="fas fa-rocket me-2"></i> Solution innovante RDC
                        </span>
                    </div>
                    <h1 class="hero-title mb-4">
                        Gérez vos projets de<br>
                        <span class="hero-highlight">construction</span> en toute simplicité
                    </h1>
                    <p class="lead mb-4 opacity-90">
                        ConstrucTrack : Site digitale de planification et suivi de chantiers à Lubumbashi. 
                        Pilotage en temps réel, budget maîtrisé, équipes connectées.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-hero"><i class="fas fa-chart-line me-2"></i>Accéder au dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn-hero"><i class="fas fa-user-plus me-2"></i>Commencer gratuitement</a>
                            <a href="#fonctionnalites" class="btn-hero-outline"><i class="fas fa-play me-2"></i>Découvrir</a>
                        @endauth
                    </div>
                    <div class="mt-5 d-flex align-items-center gap-3">
                        <div class="d-flex">
                            
                        
                        </div>
                        <div class="small">
                            <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                            <div class="fw-bold">Innovation Construction</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-warning rounded-circle opacity-10" style="filter: blur(50px);"></div>
                        <img src="https://placehold.co/550x500/0f3b2c/ffffff?text=Dashboard+ConstrucTrack" class="img-fluid rounded-4 shadow-lg float-animation" alt="Dashboard">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CLIENTS ========== -->
    <div class="container py-4">
        <div class="row justify-content-center align-items-center g-4 opacity-50">
            <div class="col-auto"><img src="https://placehold.co/100x40/e2e8f0/64748b?text=Groupe+JS" class="img-fluid grayscale"></div>
            <div class="col-auto"><img src="https://placehold.co/100x40/e2e8f0/64748b?text=BTP+Congo" class="img-fluid grayscale"></div>
            <div class="col-auto"><img src="https://placehold.co/100x40/e2e8f0/64748b?text=MK+Group" class="img-fluid grayscale"></div>
            <div class="col-auto"><img src="https://placehold.co/100x40/e2e8f0/64748b?text=Socimat" class="img-fluid grayscale"></div>
            <div class="col-auto"><img src="https://placehold.co/100x40/e2e8f0/64748b?text=Egis+RDC" class="img-fluid grayscale"></div>
        </div>
    </div>

    <!-- ========== FONCTIONNALITÉS ========== -->
    <section id="fonctionnalites" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill">Pourquoi ConstrucTrack ?</span>
                <h2 class="display-6 fw-bold mt-3">Tout ce dont vous avez besoin</h2>
                <p class="lead text-secondary">Une solution 100% adaptée au contexte de Lubumbashi</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-project-diagram"></i></div>
                        <h4 class="fw-bold">Gestion de projets</h4>
                        <p class="text-secondary">Créez, planifiez et suivez tous vos chantiers. Vue d'ensemble complète et fiches détaillées.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h4 class="fw-bold">Suivi en temps réel</h4>
                        <p class="text-secondary">Avancement, retards, productivité : des graphiques dynamiques pour piloter vos équipes.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                        <h4 class="fw-bold">Planification intelligente</h4>
                        <p class="text-secondary">Affectez les chefs de chantier, visualisez le planning Gantt et anticipez les retards.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                        <h4 class="fw-bold">Budget & finances</h4>
                        <p class="text-secondary">Contrôlez les dépenses, suivez le budget consommé et générez des rapports financiers.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-hard-hat"></i></div>
                        <h4 class="fw-bold">Mise à jour chantier</h4>
                        <p class="text-secondary">Les chefs de chantier mettent à jour l'avancement en direct depuis le terrain.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-file-alt"></i></div>
                        <h4 class="fw-bold">Reporting PDF</h4>
                        <p class="text-secondary">Exportez vos tableaux de bord et partagez-les avec vos partenaires.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== STATISTIQUES ========== -->
    <div class="stats-section py-5">
        <div class="container py-4">
            <div class="row text-center g-4">
                <div class="col-md-3" data-aos="zoom-in">
                    <div class="stat-number">5+</div>
                    <p class="text-secondary fw-semibold">Projets accompagnés</p>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-number">5+</div>
                    <p class="text-secondary fw-semibold">Entreprises clientes</p>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-number">98%</div>
                    <p class="text-secondary fw-semibold">Satisfaction </p>
                </div>
                <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-number">24/7</div>
                    <p class="text-secondary fw-semibold">Support disponible</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== AVANTAGES ========== -->
    <section id="avantages" class="py-5">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                    <img src="https://placehold.co/600x500/1e5a45/ffffff?text=Equipe+ConstrucTrack" class="img-fluid rounded-4 shadow-lg" alt="Avantages">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill">Pourquoi nous choisir</span>
                    <h2 class="display-6 fw-bold mt-3">La solution pensée pour Lubumbashi</h2>
                    <div class="mt-4 d-flex gap-3 mb-3">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div><strong>100% local</strong><br>Adapté aux réalités du marché congolais</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div><strong>Support en français</strong><br>Une équipe basée à Lubumbashi</div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div><strong>Données hébergées en RDC</strong><br>Conformité et sécurité maximale</div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-check-circle fs-3 text-success"></i>
                        <div><strong>Formation incluse</strong><br>Nous formons vos équipes sur site</div>
                    </div>
                    @guest
                    <a href="{{ route('register') }}" class="btn-register px-5 py-3"><i class="fas fa-user-plus me-2"></i>Inscription gratuite</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>


    <!-- ========== FOOTER ========== -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-hard-hat fs-2 text-secondary"></i>
                        <div><span class="fw-bold fs-4">ConstrucTrack</span><br><small>Lubumbashi - RDC</small></div>
                    </div>
                    <p>plateforme digitale de gestion de projets de construction à Lubumbashi.</p>
                </div>
                <div class="col-md-2"><h5>Liens</h5><ul class="list-unstyled"><li><a href="#accueil" class="text-decoration-none">Accueil</a></li><li><a href="#fonctionnalites" class="text-decoration-none">Fonctionnalités</a></li><li><a href="#tarifs" class="text-decoration-none">Tarifs</a></li></ul></div>
                <div class="col-md-2"><h5>Support</h5><ul class="list-unstyled"><li><a href="#">FAQ</a></li><li><a href="#">Conditions</a></li><li><a href="#">Confidentialité</a></li></ul></div>
                <div class="col-md-4"><h5>Restez connecté</h5><div class="input-group"><input type="email" class="form-control" placeholder="Votre email"><button class="btn btn-secondary">OK</button></div><div class="mt-3 d-flex gap-3 fs-4"><i class="fab fa-facebook"></i><i class="fab fa-linkedin"></i><i class="fab fa-whatsapp"></i></div></div>
            </div>
            <hr class="mt-4">
            <div class="text-center small">© 2025 ConstrucTrack – Tous droits réservés. Conçu pour les professionnels de Lubumbashi.</div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        window.addEventListener('scroll', function() {
            document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 50);
        });
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>
</html>