{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - ConstrucTrack Lubumbashi</title>
    
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
            background: linear-gradient(135deg, #6387b7 0%, #1a5e45 100%);
            padding: 40px 20px;
        }
        
        .register-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #1f483a, #6e8bbe);
            padding: 32px;
            text-align: center;
            color: white;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .logo-text h2 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
        }
        
        .logo-text p {
            font-size: 12px;
            opacity: 0.8;
            margin: 0;
        }
        
        .register-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .register-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }
        
        .form-label .required {
            color: #ef4444;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #0f3b2c;
            box-shadow: 0 0 0 3px rgba(15,59,44,0.1);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .input-icon .form-input {
            padding-left: 45px;
        }
        
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .role-selector {
            display: flex;
            gap: 16px;
            margin-top: 8px;
        }
        
        .role-option {
            flex: 1;
            position: relative;
        }
        
        .role-option input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .role-card {
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .role-option input:checked + .role-card {
            border-color: #0f3b2c;
            background: rgba(15,59,44,0.05);
        }
        
        .role-card i {
            font-size: 28px;
            color: #0f3b2c;
            margin-bottom: 8px;
        }
        
        .role-card h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .role-card p {
            font-size: 12px;
            color: #6b7280;
        }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f3b2c, #1a5e45);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15,59,44,0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        
        .login-link a {
            color: #0f3b2c;
            text-decoration: none;
            font-weight: 600;
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
            color: #6b7280;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }
        
        .btn-back-home:hover {
            color: #0f3b2c;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #dc2626;
            font-size: 14px;
        }
        
        .password-requirements {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }
        
        .password-requirements ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 8px;
        }
        
        .password-requirements li {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .requirement-valid {
            color: #10b981;
        }
        
        .requirement-invalid {
            color: #9ca3af;
        }
        
        @media (max-width: 768px) {
            .register-body {
                padding: 24px;
            }
            .row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="logo-text">
                    <h2>ConstrucTrack</h2>
                    <p>Lubumbashi - RDC</p>
                </div>
            </div>
            <h1>Créer un compte</h1>
            <p>Rejoignez la première plateforme de gestion de projets de construction</p>
        </div>
        
        <div class="register-body">
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
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">
                        Nom complet <span class="required">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nom" class="form-input @error('nom') error @enderror" 
                               value="{{ old('nom') }}" required placeholder="Jenovic">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Email <span class="required">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input @error('email') error @enderror" 
                               value="{{ old('email') }}" required placeholder="exemple@entreprise.com">
                    </div>
                </div>
                
                <div class="row-2">
                    <div class="form-group">
                        <label class="form-label">
                            Téléphone
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" name="telephone" class="form-input" 
                                   value="{{ old('telephone') }}" placeholder="+243 XXX XXX XXX">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            Entreprise / Société
                        </label>
                        <div class="input-icon">
                            <i class="fas fa-building"></i>
                            <input type="text" name="entreprise" class="form-input" 
                                   value="{{ old('entreprise') }}" placeholder="Nom de votre entreprise">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Vous êtes ? <span class="required">*</span>
                    </label>
                    <div class="role-selector">
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_projet" {{ old('role') == 'chef_projet' ? 'checked' : '' }} required>
                            <div class="role-card">
                                <i class="fas fa-chart-line"></i>
                                <h4>Chef de Projet</h4>
                                <p>Gestion complète des projets</p>
                            </div>
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role" value="chef_chantier" {{ old('role') == 'chef_chantier' ? 'checked' : '' }} required>
                            <div class="role-card">
                                <i class="fas fa-hard-hat"></i>
                                <h4>Chef de Chantier</h4>
                                <p>Suivi et mise à jour terrain</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Mot de passe <span class="required">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-input @error('password') error @enderror" 
                               required placeholder="••••••••">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    <div class="password-requirements">
                        <ul>
                            <li id="length-check"><i class="fas fa-circle"></i> 8 caractères min</li>
                            <li id="number-check"><i class="fas fa-circle"></i> 1 chiffre</li>
                            <li id="uppercase-check"><i class="fas fa-circle"></i> 1 majuscule</li>
                            <li id="special-check"><i class="fas fa-circle"></i> 1 caractère spécial</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Confirmer le mot de passe <span class="required">*</span>
                    </label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-input" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Créer mon compte
                </button>
                
                <div class="login-link">
                    Déjà un compte ?
                    <a href="{{ route('login') }}">
                        Se connecter
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
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        }
        
        // Password validation
        const passwordInput = document.getElementById('password');
        const lengthCheck = document.getElementById('length-check');
        const numberCheck = document.getElementById('number-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const specialCheck = document.getElementById('special-check');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const val = this.value;
                
                if (val.length >= 8) {
                    lengthCheck.classList.add('requirement-valid');
                    lengthCheck.classList.remove('requirement-invalid');
                    lengthCheck.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    lengthCheck.classList.remove('requirement-valid');
                    lengthCheck.classList.add('requirement-invalid');
                    lengthCheck.querySelector('i').className = 'fas fa-circle';
                }
                
                if (/\d/.test(val)) {
                    numberCheck.classList.add('requirement-valid');
                    numberCheck.classList.remove('requirement-invalid');
                    numberCheck.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    numberCheck.classList.remove('requirement-valid');
                    numberCheck.classList.add('requirement-invalid');
                    numberCheck.querySelector('i').className = 'fas fa-circle';
                }
                
                if (/[A-Z]/.test(val)) {
                    uppercaseCheck.classList.add('requirement-valid');
                    uppercaseCheck.classList.remove('requirement-invalid');
                    uppercaseCheck.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    uppercaseCheck.classList.remove('requirement-valid');
                    uppercaseCheck.classList.add('requirement-invalid');
                    uppercaseCheck.querySelector('i').className = 'fas fa-circle';
                }
                
                if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) {
                    specialCheck.classList.add('requirement-valid');
                    specialCheck.classList.remove('requirement-invalid');
                    specialCheck.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    specialCheck.classList.remove('requirement-valid');
                    specialCheck.classList.add('requirement-invalid');
                    specialCheck.querySelector('i').className = 'fas fa-circle';
                }
            });
        }
    </script>
</body>
</html>