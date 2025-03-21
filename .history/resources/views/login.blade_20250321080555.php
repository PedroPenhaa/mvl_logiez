<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 900px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
        }
        .login-form {
            width: 50%;
            padding: 20px;
        }
        .login-image {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }
        .login-image img {
            width: 80%;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Lado esquerdo - Imagem -->
        <div class="login-image">
            <img src="https://via.placeholder.com/300x200?text=Logo" alt="Logo">
        </div>

        <!-- Lado direito - Formulário -->
        <div class="login-form">
            <h3 class="mb-3">Bem-vindo!</h3>
            <p class="text-muted">Entre na sua conta e inicie nossa jornada.</p>

            <!-- Mensagem de erro -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Lembrar-me</label>
                    </div>
                    <a href="#" class="text-decoration-none">Recuperar senha?</a>
                </div>

                <button type="submit" class="btn btn-success w-100 mt-3">Entrar</button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-1">Novo na plataforma? <a href="#" class="text-decoration-none">Criar uma conta</a></p>
                <p class="text-muted">ou</p>
                <button class="btn btn-light"><img src="https://cdn-icons-png.flaticon.com/24/300/300221.png"> Google</button>
                <button class="btn btn-light"><img src="https://cdn-icons-png.flaticon.com/24/25/25231.png"> GitHub</button>
            </div>
        </div>
    </div>

</body>
</html>
