{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - ConstrucTrack Lubumbashi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2c1f 0%, #1a3a2a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animation de fond */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=1600&h=900&fit=crop') center/cover no-repeat;
            opacity: 0.08;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: linear-gradient(to top, rgba(15, 44, 31, 0.95), transparent);
            z-index: 0;
        }
        
        .login-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out;
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
        
        /* LEFT SIDE - DÉGRADÉ VERT/BLEU */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0f3b2c 0%, #1a5e45 50%, #2563eb 100%);
            padding: 48px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .logo-icon {
            width: 55px;
            height: 55px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .logo-text h2 {
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .logo-text p {
            font-size: 11px;
            opacity: 0.8;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .welcome-title {
            font-size: 34px;
            font-weight: 800;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        
        .welcome-text {
            font-size: 15px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .features-list {
            list-style: none;
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        
        .features-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .features-list li i {
            width: 28px;
            height: 28px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        
        /* RIGHT SIDE - FORMULAIRE */
        .login-right {
            flex: 1;
            background: white;
            padding: 48px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-header h3 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #0f3b2c, #1a5e45, #2563eb);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #1e293b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #0f3b2c;
            box-shadow: 0 0 0 3px rgba(15, 59, 44, 0.1);
        }
        
        .form-input.error {
            border-color: #ef4444;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i:first-child {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
        }
        
        .input-icon .form-input {
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: #0f3b2c;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }
        
        .checkbox-label input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0f3b2c;
        }
        
        .forgot-link {
            font-size: 13px;
            color: #0f3b2c;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
            color: #1a5e45;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f3b2c, #1a5e45, #2563eb);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 59, 44, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
        }
        
        .register-link a {
            color: #0f3b2c;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .register-link a:hover {
            text-decoration: underline;
            color: #1a5e45;
        }
        
        .btn-back-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px;
            background: none;
            border: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            border-radius: 40px;
        }
        
        .btn-back-home:hover {
            color: #0f3b2c;
            background: #f1f5f9;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #dc2626;
            font-size: 13px;
        }
        
        .alert-error i {
            font-size: 18px;
        }
        
        /* Animation des inputs */
        .form-input {
            animation: slideInRight 0.4s ease-out;
            animation-fill-mode: both;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-15px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .form-group:nth-child(1) .form-input { animation-delay: 0.05s; }
        .form-group:nth-child(2) .form-input { animation-delay: 0.1s; }
        
        @media (max-width: 992px) {
            .login-left {
                padding: 32px;
            }
            
            .welcome-title {
                font-size: 28px;
            }
            
            .login-right {
                padding: 32px;
            }
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 32px 24px;
            }
            
            .login-container {
                max-width: 450px;
            }
        }
        
        @media (max-width: 480px) {
            .login-right {
                padding: 24px 20px;
            }
            
            .login-header h3 {
                font-size: 24px;
            }
            
            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding (Dégradé Vert/Bleu) -->
        <div class="login-left">
            <div>
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <div class="logo-text">
                        <h2>Construc<span style="color: #fbbf24;">Track</span></h2>
                        <p>Lubumbashi - RDC</p>
                    </div>
                </div>
                
                <h1 class="welcome-title">Bienvenue !</h1>
                <p class="welcome-text">
                    La première plateforme de gestion de projets de construction à Lubumbashi.
                    Gérez vos chantiers efficacement en un seul endroit.
                </p>
                
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> Suivi en temps réel</li>
                    <li><i class="fas fa-check-circle"></i> Planification intelligente</li>
                    <li><i class="fas fa-check-circle"></i> Gestion budgétaire (FC & USD)</li>
                    
                </ul>
            </div>
            
            <div class="mt-auto pt-4">
                <div class="testimonial-text small fst-italic opacity-75">
                    <i class="fas fa-quote-left me-1"></i> ConstrucTrack a révolutionné la gestion de nos chantiers à Lubumbashi.
                </div>
                <div class="d-flex align-items-center gap-2 mt-2">
                    <i class="fas fa-building"></i>
                    <small>Construction</small>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h3>Connexion</h3>
                <p>Entrez vos identifiants pour accéder à votre compte</p>
            </div>
            
            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </span>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-envelope me-1"></i> Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input @error('email') error @enderror" 
                               value="{{ old('email') }}" required autofocus placeholder="exemple@email.com">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-lock me-1"></i> Mot de passe</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-input @error('password') error @enderror" 
                               required placeholder="••••••••">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket me-2"></i>
                    Se connecter
                </button>
                
                <div class="register-link">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}">
                        Créer un compte gratuitement
                    </a>
                </div>
                
                <a href="{{ route('home') }}" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i>
                    Retour à l'accueil
                </a>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>