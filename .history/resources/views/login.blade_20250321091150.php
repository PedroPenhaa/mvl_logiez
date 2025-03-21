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
        }
        
        .login-container {
            width: 100%;
            max-width: 1100px;
            display: flex;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .login-banner {
            flex: 1;
            background-color: #f0f0f0;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .login-form-container {
            flex: 1;
            background-color: white;
            padding: 2rem;
        }
        
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h3 {
            font-weight: 600;
            color: var(--dark-color);
            margin-top: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 12px;
            border-radius: 6px;
        }
        
        .form-control:focus {
            border-color: var(--highlight-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 6px;
        }
        
        .btn-primary:hover {
            background-color: var(--highlight-color);
            border-color: var(--highlight-color);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            color: var(--highlight-color);
            text-decoration: underline;
        }
        
        .brand-logo {
            font-size: 2.5rem;
            color: var(--primary-color);
        }
        
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
        }
        
        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .separator span {
            padding: 0 10px;
            color: #6c757d;
        }
        
        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .google-btn {
            background-color: #DB4437;
        }
        
        .github-btn {
            background-color: #333;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 100%;
                margin: 0 15px;
            }
            
            .login-banner {
                display: none;
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
                
                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Sua senha" required>
                        <div class="d-flex justify-content-end mt-1">
                            <a href="#" class="small">Recuperar senha?</a>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Lembrar-me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Entrar
                    </button>
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
