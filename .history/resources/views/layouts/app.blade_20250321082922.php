<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logiez - Plataforma de Envios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a0dad;
            --secondary-color: #f8f9fa;
            --highlight-color: #8a2be2;
            --text-light: #ffffff;
            --text-dark: #333333;
            --border-radius: 6px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: var(--box-shadow);
            padding: 20px 0;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            color: var(--text-light);
            text-decoration: none;
        }

        .menu-item i {
            margin-right: 15px;
            width: 24px;
            text-align: center;
        }

        .menu-item.active, .menu-item:hover {
            background-color: var(--highlight-color);
            border-left: 4px solid var(--text-light);
        }

        main {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 15px 20px;
            font-weight: 600;
            font-size: 18px;
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--highlight-color);
            border-color: var(--highlight-color);
        }

        .form-control:focus {
            border-color: var(--highlight-color);
            box-shadow: 0 0 0 0.25rem rgba(138, 43, 226, 0.25);
        }

        /* Estilos personalizados adicionais */
        .content-header {
            margin-bottom: 20px;
        }

        .content-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar-header h3, .menu-text {
                display: none;
            }

            .menu-item i {
                margin-right: 0;
            }

            main {
                margin-left: 70px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar / Menu Lateral -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Logiez</h3>
        </div>
        
        <nav>
            <a href="{{ route('cotacao.index') }}" class="menu-item {{ request()->routeIs('cotacao*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span class="menu-text">Cotação</span>
            </a>
            
            <a href="{{ route('envio.index') }}" class="menu-item {{ request()->routeIs('envio*') ? 'active' : '' }}">
                <i class="fas fa-shipping-fast"></i>
                <span class="menu-text">Envio</span>
            </a>
            
            <a href="{{ route('pagamento.index') }}" class="menu-item {{ request()->routeIs('pagamento*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                <span class="menu-text">Pagamento</span>
            </a>
            
            <a href="{{ route('etiqueta.index') }}" class="menu-item {{ request()->routeIs('etiqueta*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i>
                <span class="menu-text">Etiqueta</span>
            </a>
            
            <a href="{{ route('rastreamento.index') }}" class="menu-item {{ request()->routeIs('rastreamento*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i>
                <span class="menu-text">Rastreamento</span>
            </a>
            
            <a href="{{ route('perfil.index') }}" class="menu-item {{ request()->routeIs('perfil*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span class="menu-text">Meu Perfil</span>
            </a>
            
            <a href="{{ route('login.form') }}" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Sair</span>
            </a>
        </nav>
    </div>
    
    <!-- Conteúdo Principal -->
    <main>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="content-header">
            <h1>@yield('title')</h1>
        </div>
        
        @yield('content')
    </main>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Script para definir a altura mínima do conteúdo principal
        $(document).ready(function() {
            let windowHeight = $(window).height();
            let headerHeight = $('.content-header').outerHeight();
            $('main').css('min-height', windowHeight);
        });
    </script>
    
    @yield('scripts')
</body>
</html> 