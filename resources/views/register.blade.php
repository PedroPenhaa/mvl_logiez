<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro - Logiez</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Padrão de roxo para toda a interface - consistente com o projeto */
        :root {
            --roxo-principal: #764ba2;
            --roxo-secundario: #6f42c1;
            --roxo-claro: #a084e8;
            --roxo-escuro: #4b2c6f;
            --roxo-bg: #f5f3fa;
            --roxo-badge: #8f5fd6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--roxo-bg) 0%, #ffffff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Efeito de fundo decorativo */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(160, 132, 232, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(111, 66, 193, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px rgba(118, 75, 162, 0.15),
                0 0 0 1px rgba(118, 75, 162, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: row;
            min-height: 600px;
            position: relative;
        }
        
        .register-form-container {
            padding: 3rem;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-banner {
            background: linear-gradient(135deg, var(--roxo-principal) 0%, var(--roxo-secundario) 50%, var(--roxo-claro) 100%);
            padding: 3rem 2.5rem;
            color: white;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Efeitos decorativos no banner */
        .register-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        .register-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 160%;
            height: 160%;
            background: radial-gradient(circle, rgba(160, 132, 232, 0.2) 0%, transparent 60%);
            animation: float 8s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .banner-content {
            position: relative;
            z-index: 2;
        }
        
        .logo {
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--roxo-principal);
        }
        
        .form-control {
            border: 2px solid #e9e3f7;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            background-color: #fff;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--roxo-principal);
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.15);
            outline: none;
            transform: translateY(-2px);
        }
        
        .form-label {
            color: var(--roxo-escuro);
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--roxo-principal) 0%, var(--roxo-secundario) 100%);
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            width: 100%;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(118, 75, 162, 0.4);
        }
        
        .register-footer {
            margin-top: 2rem;
            text-align: center;
        }
        
        .register-footer a {
            color: var(--roxo-principal);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .register-footer a:hover {
            color: var(--roxo-escuro);
            text-decoration: underline;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .register-header .brand-logo {
            font-size: 3.5rem;
            color: var(--roxo-principal);
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .register-header h3 {
            color: var(--roxo-escuro);
            font-weight: 700;
            margin-bottom: 0.75rem;
            font-size: 1.8rem;
        }
        
        .register-header p {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .form-check-input:checked {
            background-color: var(--roxo-principal);
            border-color: var(--roxo-principal);
        }
        
        .form-check-input:focus {
            border-color: var(--roxo-principal);
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.15);
        }
        
        .form-check-label a {
            color: var(--roxo-principal);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .form-check-label a:hover {
            color: var(--roxo-escuro);
            text-decoration: underline;
        }
        
        .separator {
            text-align: center;
            margin: 2.5rem 0;
            position: relative;
        }
        
        .separator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e9e3f7, transparent);
        }
        
        .separator span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1.5rem;
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .social-register {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
        }
        
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #e9e3f7;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }
        
        .social-btn:hover {
            border-color: var(--roxo-principal);
            color: var(--roxo-principal);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border: 1px solid #fed7d7;
            color: #c53030;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        /* Media Queries para Responsividade */
        @media (max-width: 992px) {
            .register-container {
                max-width: 800px;
                min-height: 550px;
            }
            
            .register-form-container,
            .register-banner {
                padding: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
                min-height: 100vh;
            }
            
            .register-container {
                flex-direction: column;
                max-width: 500px;
                min-height: auto;
                margin: 1rem 0;
            }
            
            .register-form-container, 
            .register-banner {
                width: 100%;
                padding: 2rem;
            }
            
            .register-banner {
                order: -1;
                padding: 2rem 1.5rem;
            }
            
            .register-form-container {
                padding: 2rem 1.5rem;
            }
            
            .register-header .brand-logo {
                font-size: 3rem;
            }
            
            .register-header h3 {
                font-size: 1.6rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 0;
                background: var(--roxo-bg);
            }
            
            .register-container {
                border-radius: 0;
                box-shadow: none;
                background: transparent;
                margin: 0;
                min-height: 100vh;
            }
            
            .register-form-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px 20px 0 0;
                box-shadow: 0 -10px 30px rgba(118, 75, 162, 0.15);
                padding: 1.5rem;
                margin-top: -20px;
                position: relative;
                z-index: 2;
            }
            
            .register-banner {
                border-radius: 0;
                margin-bottom: 0;
                padding: 2rem 1rem;
                min-height: 40vh;
            }
            
            .banner-text {
                display: none;
            }
            
            .banner-title {
                text-align: center;
                margin-bottom: 0;
            }
            
            .social-register {
                gap: 1rem;
            }
            
            .social-btn {
                width: 50px;
                height: 50px;
                font-size: 1rem;
            }
        }
        
        @media (max-height: 600px) {
            .register-container {
                min-height: auto;
                margin: 1rem 0;
            }
            
            .register-form-container,
            .register-banner {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-banner d-none d-md-flex">
            <div class="banner-content text-center">
                <h1 class="display-4 mb-4">
                    <span class="text-white">Logi</span>
                    <span style="color: var(--roxo-claro)">ez</span>
                </h1>
                <div class="mb-4">
                    <i class="fas fa-shipping-fast" style="font-size: 4rem; color: var(--roxo-claro);"></i>
                </div>
                <h2 class="h4 mb-3">Plataforma de Envios Internacionais</h2>
                <p class="mb-0 opacity-90">Simplifique suas remessas internacionais com a Logiez. Cotação, envio e rastreamento em uma única plataforma.</p>
            </div>
        </div>
        
        <div class="register-form-container">
            <div class="register-form">
                <div class="register-header">
                    <i class="fas fa-shipping-fast brand-logo"></i>
                    <h3>Criar uma Conta</h3>
                    <p class="text-muted">Preencha os dados abaixo para se cadastrar na Logiez</p>
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('register.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Seu nome completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Crie uma senha" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirme a Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirme sua senha" required>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Concordo com os <a href="#" class="text-decoration-none">Termos de Uso</a> e <a href="#" class="text-decoration-none">Política de Privacidade</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Criar Conta
                    </button>
                </form>
                
                <div class="register-footer">
                    <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login</a></p>
                </div>
                
                <div class="separator">
                    <span>ou</span>
                </div>
                
                <div class="social-register">
                    <a href="#" class="social-btn google-btn" title="Continuar com Google">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn github-btn" title="Continuar com GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 