<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mot de passe oublié - ConstrucTrack Lubumbashi</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: linear-gradient(135deg, #0f3b2c 0%, #1a5e45 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .forgot-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .forgot-header {
            background: linear-gradient(135deg, #0f3b2c, #1a5e45);
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
        
        .logo-text p {
            font-size: 12px;
            opacity: 0.8;
            margin: 0;
        }
        
        .forgot-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .forgot-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .forgot-body {
            padding: 40px;
        }
        
        .info-text {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            font-size: 14px;
            color: #0f3b2c;
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
        
        .btn-submit {
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
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15,59,44,0.3);
        }
        
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        
        .back-link a {
            color: #0f3b2c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
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
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #059669;
            font-size: 14px;
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
    <div class="forgot-container">
        <div class="forgot-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="logo-text">
                    <h2>ConstrucTrack</h2>
                    <p>Lubumbashi - RDC</p>
                </div>
            </div>
            <h1>Mot de passe oublié ?</h1>
            <p>Ne vous inquiétez pas, ça arrive !</p>
        </div>
        
        <div class="forgot-body">
            @if (session('status'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            
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
            
            <div class="info-text">
                <i class="fas fa-envelope"></i>
                <span>Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</span>
            </div>
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Votre adresse email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" required placeholder="exemple@email.com">
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane me-2"></i>
                    Envoyer le lien de réinitialisation
                </button>
                
                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour à la connexion
                    </a>
                </div>
                
                <a href="{{ url('/') }}" class="btn-back-home">
                    <i class="fas fa-home me-2"></i>
                    Retour à l'accueil
                </a>
            </form>
        </div>
    </div>
</body>
</html>