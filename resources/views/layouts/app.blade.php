<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logiez - Plataforma de Envios</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/v4-shims.css">
    
    <!-- Seção para estilos específicos da página -->
    @yield('styles')
    
    <style>
        /* Aplicar Rubik globalmente */
        * {
            font-family: 'Rubik', sans-serif !important;
        }

        /* Exceção para ícones Font Awesome */
        .fas, .far, .fab, .fa {
            font-family: "Font Awesome 6 Free" !important;
        }
        
        /* Garantir que os ícones sólidos tenham o peso correto */
        .fas {
            font-weight: 900 !important;
        }
        
        /* Estilos Globais */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f5f8fa;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
        }
        
        /* Barra Lateral (Sidebar) */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #6f42c1;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            height: 100px;
            padding: 0px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
            color: #3498db;
        }
        
        .menu-item {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        
        .menu-item i {
            font-size: 18px;
            min-width: 30px;
            margin-right: 10px;
        }
        
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: #3498db;
        }
        
        .menu-item.active {
            background: rgba(52, 152, 219, 0.2);
            border-left-color: #3498db;
        }
        
        .menu-item .menu-text {
            font-size: 14px;
            white-space: nowrap;
        }
        
        /* Conteúdo Principal */
        .main-content {
            flex: 1;
            margin-left: 250px;
            min-height: 100vh;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
            padding: 20px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        /* Botão de toggle para o menu em telas pequenas */
        .toggle-sidebar {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 999;
            background: #3498db;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Loader de conteúdo */
        #content-loader {
            text-align: center;
            padding: 50px 0;
            display: none;
        }
        
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Media Queries para Responsividade */
        @media (max-width: 992px) {
            .sidebar {
                width: 60px;
                overflow-x: hidden;
            }
            
            .sidebar.expanded {
                width: 250px;
            }
            
            .menu-item .menu-text {
                opacity: 0;
                display: none;
                transition: opacity 0.2s;
            }
            
            .sidebar.expanded .menu-item .menu-text {
                opacity: 1;
                display: inline;
                color: #fff !important;

            }
   
            .menu-item .menu-text {
                color: #fff !important;
            }
            .sidebar-header h3 {
                display: none;
            }
            
            .sidebar.expanded .sidebar-header h3 {
                display: block;
            }
            
            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            
            .toggle-sidebar {
                display: flex;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 0;
                overflow-x: hidden;
            }
            
            .sidebar.expanded {
                width: 250px;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            
            .content-header {
                padding: 10px 15px;
            }
            
            .toggle-sidebar {
                display: flex;
            }
        }

        /* Forçar cor branca nos ícones e textos do menu lateral */
        .sidebar .menu-item i,
        .sidebar .menu-item .menu-text,
        .sidebar .menu-item span {
            color: #fff !important;
            font-weight: 600;
        }

        /* Aumentar o tamanho da fonte do texto do menu ao passar o mouse */
        .sidebar .menu-item:hover .menu-text,
        .sidebar .menu-item:hover span {
            font-size: 1em;
            transition: font-size 0.2s;
        }
    </style>
</head>
<body>
    <!-- Botão toggle para menu lateral em telas pequenas -->
    <button class="toggle-sidebar" id="toggle-sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar / Menu Lateral -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('img/logo_logiez1.png') }}" alt="Logo Logiez" style="width: 100%; height: 160%; margin-top: -25px; object-fit: contain;">
        </div>
        
        <nav>
            <!-- Menu do Dashboard -->
            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                <div class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </div>
            </a>
            <a href="/cotacao" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-calculator"></i>
                    <span class="menu-text">Cotação</span>
                </div>
            </a>
            <a href="/envio" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-shipping-fast"></i>
                    <span class="menu-text">Envio</span>
                </div>
            </a>
            <a href="/pagamento" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-credit-card"></i>
                    <span class="menu-text">Pagamento</span>
                </div>
            </a>
            <a href="/etiqueta" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-tag"></i>
                    <span class="menu-text">Etiqueta</span>
                </div>
            </a>
            <a href="/rastreamento" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="menu-text">Rastreamento</span>
                </div>
            </a>
            <a href="/perfil" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-user"></i>
                    <span class="menu-text">Meu Perfil</span>
                </div>
            </a>
            
            <!-- Menu Admin - apenas para usuários admin -->
            @if(auth()->check() && auth()->user()->admin)
            <div style="border-top: 1px solid rgba(255,255,255,0.2); margin: 10px 0;"></div>
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                <div class="menu-item">
                    <i class="fas fa-cogs"></i>
                    <span class="menu-text">Admin</span>
                </div>
            </a>
            @endif
            
            <form action="{{ route('logout') }}" method="POST" style="margin:0;padding:0;">
                @csrf
                <button type="submit" class="menu-item" style="background:none;border:none;width:100%;text-align:left;color:inherit;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-text">Sair</span>
                </button>
            </form>
        </nav>
    </div>
    
    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div id="alert-container"></div>
        
        @if(request()->segment(1) !== 'dashboard')
            <div class="section-header">
                <h1 id="section-title" class="section-title" style="color: #6f42c1;"></h1>
            </div>
        @endif
        
        <div class="loader" id="content-loader"></div>
        
        <div id="content-container">
            <!-- O conteúdo será carregado dinamicamente via AJAX ou diretamente pelo yield content -->
            @yield('content')
        </div>
    </main>
    
    <!-- Footer fixo -->
    <div style="position: fixed; bottom: 0; left: 0; right: 0; background: linear-gradient(45deg, #4a1d96, #6f42c1); color: #fff; text-align: center; padding: 12px; z-index: 1000; font-weight: 500; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); font-family: 'Poppins', sans-serif;">
        <i class="fas fa-code-branch" style="margin-right: 8px;"></i>
        Sistema em Fase de Desenvolvimento
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset('js/envio-pagamento.js') }}"></script>
    @yield('scripts')
</body>
</html> 