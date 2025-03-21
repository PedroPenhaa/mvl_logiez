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
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #f8f9fa;
            --highlight-color: #2980b9;
            --dark-color: #34495e;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: row;
        }
        
        .login-form-container {
            padding: 2.5rem;
            width: 50%;
        }
        
        .login-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--highlight-color));
            padding: 2.5rem;
            color: white;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .logo {
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1rem;
            border-radius: 5px;
            width: 100%;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: var(--highlight-color);
            border-color: var(--highlight-color);
        }
        
        .login-footer {
            margin-top: 2rem;
            text-align: center;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            color: var(--highlight-color);
            text-decoration: underline;
        }
        
        /* Media Queries para Responsividade */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 480px;
            }
            
            .login-form-container, 
            .login-banner {
                width: 100%;
            }
            
            .login-banner {
                padding: 2rem;
                order: -1;
            }
            
            .login-form-container {
                padding: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                box-shadow: none;
                background-color: transparent;
            }
            
            .login-form-container {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 1.5rem;
            }
            
            .login-banner {
                border-radius: 10px;
                margin-bottom: 1rem;
                padding: 1.5rem;
            }
            
            .banner-text {
                display: none;
            }
            
            .banner-title {
                text-align: center;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-banner d-none d-md-flex">
            <div class="text-center">
                <h1 class="display-4 mb-4"><span class="text-dark">Logi</span><span style="color: var(--primary-color)">ez</span></h1>
                <img src="https://via.placeholder.com/400x300?text=Logiez" alt="Logiez" class="img-fluid rounded mb-4">
                <h2 class="h4 mb-3">Plataforma de Envios Internacionais</h2>
                <p class="mb-0">Simplifique suas remessas internacionais com a Logiez. Cotação, envio e rastreamento em uma única plataforma.</p>
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
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Sua senha">
                    </div>
                    
                    <div class="form-check mb-3">
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
