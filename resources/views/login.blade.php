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
            --primary-color: #430776;
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
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
           /* background: #3f0d71;*/
        }

        .login-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #3f0d71 0%, #63499E 100%);
        }

        .login-banner {
            max-width: 48%;
            /*background: url('{{ asset("img/login2.png") }}') no-repeat top center;*/
            background-size: contain;
      /*      background-color: #3f0d71;*/
            background-color: white;
            flex: 1;
            height: 100vh;
            max-height: 100vh;
        }

        .login-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }


        .login-form-container {
            width: 30%;
            height: 100vh;
            background-color: white;
            padding: 2rem 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2; /* Aumentando o z-index */
        }

        .login-form {
            position: relative;
            z-index: 3; /* Garantindo que o formulário fique acima */
        }

        .form-check {
            position: relative;
            z-index: 4; /* Garantindo que os checkboxes e links fiquem clicáveis */
        }

        .btn {
            position: relative;
            z-index: 4; /* Garantindo que os botões fiquem clicáveis */
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
        }

        .text-muted {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .text-muted:hover {
            color: var(--primary-color) !important;
            text-decoration: underline;
        }

        /* Ajustando o botão do Gmail */
        .btn-outline-danger {
            position: relative;
            z-index: 4;
            cursor: pointer;
        }

        .login-form-container::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 80%;
            background-color: #a0aec0;
        }

        .banner-content {
            height: 100%;
            width: 100%;
            position: relative;
        }

        .login-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(99, 73, 158, 0.1);
            z-index: 1;
        }

        /* Título do banner */
        .banner-title {
            display: none;
        }

        .banner-title h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .banner-title p {
            font-size: 1rem;
            opacity: 0.9;
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
            margin-bottom: 0.3rem;
            transition: var(--transition);
            font-size: 0.9rem;
            background-color: #f9fafc;
            height: auto;
            position: relative;
            z-index: 1;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(99, 73, 158, 0.25);
            background-color: white;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.1rem;
            font-size: 0.75rem;
            display: inline-block;
            width: auto;
        }

        .form-group {
            margin-bottom: 0.4rem;
        }

        .input-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .input-group-text {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.3rem 0.6rem;
            border-radius: var(--border-radius);
            width: 100%;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(99, 73, 158, 0.2);
            margin: 0.3rem 0;
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
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-header h3 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.2rem !important;
            font-size: 1.1rem !important;
        }

        .login-header p {
            opacity: 0.8;
            margin-bottom: 0.3rem;
            font-size: 0.7rem !important;
        }

        .login-footer {
            margin-top: 0.5rem !important;
            text-align: center;
            font-size: 0.8rem;
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
            margin: 0.5rem 0;
            color: #a0aec0;
        }

        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .separator span {
            padding: 0 0.5rem;
            font-size: 0.8rem;
        }

        .form-check-label {
            font-size: 0.75rem;
            color: #64748b;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .brand-logo {
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
            color: var(--primary-color);
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
            background-color: #fdf2f2;
            border-color: #fecaca;
            color: #ef4444;
            padding: 0.4rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            animation: fadeIn 0.5s ease-out;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #22c55e;
            padding: 0.4rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            box-shadow: 0 2px 5px rgba(34, 197, 94, 0.1);
            border-left: 4px solid #22c55e;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Media Queries para Responsividade */
        @media (max-width: 991px) {
            .login-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
            }

            .login-banner {
                max-width: 100%;
                height: 30vh;
                min-height: 200px;
            }

            .login-banner img {
                height: 100%;
                object-fit: cover;
            }

            .login-form-container {
                width: 100%;
                height: auto;
                padding: 2rem;
                z-index: 2;
            }

            .login-form-container::before {
                display: none;
            }

            .form-check.mb-4.mt-2 {
                flex-direction: row !important; /* Mantendo na mesma linha em mobile */
                justify-content: space-between;
                align-items: center;
                width: 100%;
            }

            .form-check a {
                align-self: flex-end;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 0;
            }

            .login-banner {
                height: 25vh;
            }

            .login-form-container {
                padding: 1.5rem;
            }

            .login-header h3 {
                font-size: 1.4rem !important;
            }

            .login-header p {
                font-size: 0.85rem !important;
            }

            .form-control, .input-group-text {
                font-size: 0.9rem !important;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .separator {
                margin: 1rem 0;
            }

            .login-footer {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .login-banner {
                height: 20vh;
            }

            .login-form-container {
                padding: 1rem;
            }

            .form-check.mb-4.mt-2 {
                gap: 1rem;
            }

            .form-check-label, .text-muted {
                font-size: 0.85rem !important;
            }

            .btn-outline-danger {
                font-size: 0.85rem;
            }

            .login-footer {
                margin-top: 1.5rem !important;
            }
        }
    </style>
</head>

<body>
    <div class="login-container d-flex">
        <div class="login-banner d-none d-md-flex justify-content-center align-items-center">
            <img src="{{ asset('img/login5.jpg') }}" alt="Login Banner" style="max-width: 100%; height: 100vh;">
        </div>



        <div class="login-form-container">
            <div class="login-form">
                <div class="login-header">
                    <i class="fas fa-shipping-fast brand-logo"></i>
                    <h3 style="font-size: 1.6rem;">Bem-vindo!</h3>
                    <p class="text-muted" style="font-size: 0.8rem;">Entre na sua conta e inicie nossa jornada.</p>
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

                @if (session('login_error'))
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2" style="font-size: 1.2rem;"></i>
                        <h5 class="mb-0 fw-bold">Erro de autenticação</h5>
                    </div>
                    <hr class="my-2">
                    <p class="mb-0">{{ session('login_error') }}</p>
                    <p class="mt-1 mb-0 small text-muted">Verifique suas credenciais e tente novamente.</p>
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2" style="font-size: 1.2rem;"></i>
                        <h5 class="mb-0 fw-bold">Sucesso!</h5>
                    </div>
                    <hr class="my-2">
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label" style="font-size: 0.9rem;">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input style="font-size: 0.9rem;" type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" placeholder="Seu email">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0" style="font-size: 0.9rem;">Senha</label>

                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input style="font-size: 0.9rem;" type="password" class="form-control border-start-0" id="password" name="password" placeholder="Sua senha">
                        </div>
                    </div>

                    <div class="form-check mb-4 mt-2 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" style="cursor: pointer;">
                            <label class="form-check-label" for="remember" style="font-size: 0.9rem; cursor: pointer;">
                                Lembrar-me
                            </label>
                        </div>
                        <a href="#" class="text-muted" style="font-size: 0.9rem; text-decoration: none;">Esqueceu a senha?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="position: relative; z-index: 4;">Entrar</button>
                </form>

                <div class="separator">
                    <span style="font-size: 0.9rem;">ou continue com</span>
                </div>

                <div class="row mt-3">
                    <div class="col-12 mb-3">
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-outline-danger w-100">
                            <i class="fab fa-google me-2" style="font-size: 1.2rem;"></i> Entrar com o Gmail
                        </a>
                    </div>
                    <!--<div class="col-12">
                        <a href="{{ route('social.redirect', ['provider' => 'apple']) }}" class="btn btn-outline-dark w-100">
                            <i class="fab fa-apple me-2" style="font-size: 1.2rem;"></i> Entrar com o iCloud
                        </a>
                    </div>-->
                </div>

                <div class="login-footer mt-4">
                    <p>Novo na plataforma? <a href="{{ route('register.form') }}">Criar uma conta</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>