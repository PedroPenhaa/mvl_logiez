<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Logiez</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #63499E;
            --primary-light: #8a6cc9;
            --primary-dark: #4e3978;
            --secondary-color: #f8f9fa;
            --text-light: #ffffff;
            --text-dark: #333333;
            --border-radius: 8px;
            --box-shadow: 0 8px 24px rgba(99, 73, 158, 0.15);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #f5f7fc;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        
        .login-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: row;
        }
        
        .login-form-container {
            padding: 3rem;
            width: 50%;
        }
        
        .login-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            padding: 3rem;
            color: white;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImcxIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjZmZmZmZmIiBzdG9wLW9wYWNpdHk9IjAuMSIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIgc3RvcC1vcGFjaXR5PSIwIiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB4PSIwIiB5PSIwIiB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPjxwYXRoIGZpbGw9InVybCgjZzEpIiBkPSJNMCwwIGwxMCwwIGwwLDEwIGwtMTAsMCB6IiAvPjwvcGF0dGVybj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIiBvcGFjaXR5PSIwLjQiIC8+PC9zdmc+') repeat;
            opacity: 0.1;
        }
        
        .logo {
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo i {
            font-size: 2rem;
            margin-right: 0.5rem;
            color: var(--text-light);
        }
        
        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            transition: var(--transition);
            font-size: 0.95rem;
            background-color: #f9fafc;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(99, 73, 158, 0.25);
            background-color: white;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            width: 100%;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(99, 73, 158, 0.2);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 73, 158, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(99, 73, 158, 0.2);
        }
        
        .login-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .login-header h3 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            opacity: 0.8;
        }
        
        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .login-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #a0aec0;
        }
        
        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .separator span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
        
        .form-check-label {
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .brand-logo {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #64748b;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .google-btn:hover {
            color: #DB4437;
            background-color: #fff0f0;
        }
        
        .github-btn:hover {
            color: #333;
            background-color: #f1f1f1;
        }
        
        .alert-danger {
            background-color: #fff0f3;
            border-color: #ffccd5;
            color: #e11d48;
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger ul {
            padding-left: 1.5rem;
            margin-bottom: 0;
        }
        
        /* Media Queries para Responsividade */
        @media (max-width: 991px) {
            .login-container {
                max-width: 850px;
            }
            
            .login-form-container {
                padding: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 550px;
            }
            
            .login-form-container, 
            .login-banner {
                width: 100%;
            }
            
            .login-banner {
                padding: 2.5rem;
                order: -1;
                text-align: center;
            }
            
            .login-form-container {
                padding: 2.5rem;
            }
            
            .banner-content {
                align-items: center;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                box-shadow: none;
                background-color: transparent;
            }
            
            .login-form-container {
                background-color: white;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                padding: 2rem 1.5rem;
            }
            
            .login-banner {
                border-radius: var(--border-radius);
                margin-bottom: 1.5rem;
                padding: 2rem 1.5rem;
            }
            
            .banner-text {
                display: none;
            }
            
            .banner-title {
                text-align: center;
                margin-bottom: 0;
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-banner d-none d-md-flex">
            <div class="text-center banner-content">
                <h1 class="logo mb-4">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Logiez</span>
                </h1>
                <img src="{{ asset('images/logiez-logo.png') }}" alt="Logiez" class="img-fluid rounded mb-4" onerror="this.src='https://via.placeholder.com/400x250/63499E/ffffff?text=Logiez'; this.onerror='';">
                <h2 class="h4 mb-3 fw-light">Plataforma de Envios Internacionais</h2>
                <p class="mb-0 opacity-75">Simplifique suas remessas internacionais com a Logiez. Cotação, envio e rastreamento em uma única plataforma.</p>
            </div>
        </div>
        
        <div class="login-form-container">
            <div class="login-form">
                <div class="login-header">
                    <i class="fas fa-shipping-fast brand-logo"></i>
                    <h3>Bem-vindo!</h3>
                    <p class="text-muted">Entre na sua conta e inicie nossa jornada.</p>
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
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Senha</label>
                            <a href="#" class="text-muted small">Esqueceu a senha?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Sua senha">
                        </div>
                    </div>
                    
                    <div class="form-check mb-4 mt-2">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Lembrar-me
                        </label>
                    </div>
                    
                    <!-- Botão que redireciona diretamente para o dashboard sem validação -->
                    <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">Entrar</a>
                </form>
                
                <div class="login-footer">
                    <p>Novo na plataforma? <a href="{{ route('register.form') }}">Criar uma conta</a></p>
                </div>
                
                <div class="separator">
                    <span>ou</span>
                </div>
                
                <div class="social-login">
                    <a href="#" class="social-btn google-btn">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn github-btn">
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
