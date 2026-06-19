{{-- resources/views/auth/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - ConstrucTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .reset-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .reset-header {
            background: linear-gradient(135deg, #1a56db, #0e3a8a);
            padding: 40px;
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
        
        .reset-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .reset-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .reset-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
            font-size: 14px;
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
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,0.1);
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
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
        }
        
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a56db, #0e3a8a);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(26,86,219,0.3);
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
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="logo-text">
                    <h2>ConstrucTrack</h2>
                    <p>Lubumbashi - RDC</p>
                </div>
            </div>
            <h1>Nouveau mot de passe</h1>
            <p>Choisissez un mot de passe sécurisé</p>
        </div>
        
        <div class="reset-body">
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
            
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $request->email) }}" required readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-input" required placeholder="••••••••">
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
                    <label class="form-label">Confirmer le mot de passe</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" class="form-input" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fas fa-key me-2"></i>
                    Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
    
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
        
        // Password validation
        const passwordInput = document.getElementById('password');
        const lengthCheck = document.getElementById('length-check');
        const numberCheck = document.getElementById('number-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const specialCheck = document.getElementById('special-check');
        
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
    </script>
</body>
</html>