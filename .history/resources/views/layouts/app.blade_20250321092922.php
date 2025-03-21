<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logiez - Plataforma de Envios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
            background: #2c3e50;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 20px;
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
    </style>
    @yield('styles')
</head>
<body>
    <!-- Botão toggle para menu lateral em telas pequenas -->
    <button class="toggle-sidebar" id="toggle-sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar / Menu Lateral -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Logiez</h3>
        </div>
        
        <nav>
            <div class="menu-item active" data-section="dashboard">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </div>
            
            <div class="menu-item" data-section="cotacao">
                <i class="fas fa-calculator"></i>
                <span class="menu-text">Cotação</span>
            </div>
            
            <div class="menu-item" data-section="envio">
                <i class="fas fa-shipping-fast"></i>
                <span class="menu-text">Envio</span>
            </div>
            
            <div class="menu-item" data-section="pagamento">
                <i class="fas fa-credit-card"></i>
                <span class="menu-text">Pagamento</span>
            </div>
            
            <div class="menu-item" data-section="etiqueta">
                <i class="fas fa-tag"></i>
                <span class="menu-text">Etiqueta</span>
            </div>
            
            <div class="menu-item" data-section="rastreamento">
                <i class="fas fa-map-marker-alt"></i>
                <span class="menu-text">Rastreamento</span>
            </div>
            
            <div class="menu-item" data-section="perfil">
                <i class="fas fa-user"></i>
                <span class="menu-text">Meu Perfil</span>
            </div>
            
            <a href="{{ route('logout') }}" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="menu-text">Sair</span>
            </a>
        </nav>
    </div>
    
    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div id="alert-container"></div>
        
        <div class="content-header">
            <h1 id="section-title" class="section-title">Dashboard</h1>
        </div>
        
        <div class="loader" id="content-loader"></div>
        
        <div id="content-container">
            <!-- O conteúdo será carregado dinamicamente via AJAX -->
        </div>
    </main>
    
    <!-- Bootstrap JS e dependências -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Definir loadSection como uma função global
        var loadSection;
        
        $(document).ready(function() {
            // Configure AJAX for CSRF protection
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Definir altura mínima do conteúdo
            function setMinHeight() {
                var windowHeight = $(window).height();
                var headerHeight = $('.content-header').outerHeight();
                $('#content-container').css('min-height', windowHeight - headerHeight - 40);
            }
            
            setMinHeight();
            $(window).resize(setMinHeight);
            
            // Toggle para o menu lateral em dispositivos móveis
            $('#toggle-sidebar').on('click', function() {
                $('#sidebar').toggleClass('expanded');
            });
            
            // Fechar o menu ao clicar fora dele em telas pequenas
            $(document).on('click', function(e) {
                if ($(window).width() <= 992) {
                    if (!$(e.target).closest('#sidebar').length && 
                        !$(e.target).closest('#toggle-sidebar').length && 
                        $('#sidebar').hasClass('expanded')) {
                        $('#sidebar').removeClass('expanded');
                    }
                }
            });
            
            // Função para mostrar mensagem de alerta
            window.showAlert = function(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alert-container').html(alertHtml);
                
                // Auto fechar o alerta após 5 segundos
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }
            
            // Definir a função loadSection globalmente
            loadSection = function(section) {
                // Mostrar indicador de carregamento
                $('#content-loader').show();
                $('#content-container').hide();
                
                // Atualizar o título da seção
                $('#section-title').text(section.charAt(0).toUpperCase() + section.slice(1));
                
                // Atualizar menu
                $('.menu-item').removeClass('active');
                $('.menu-item[data-section="' + section + '"]').addClass('active');
                
                // Carregar conteúdo via AJAX
                $.ajax({
                    url: '/api/sections/' + section,
                    method: 'GET',
                    success: function(response) {
                        // Atualizar conteúdo
                        $('#content-container').html(response);
                        $('#content-container').show();
                        
                        // Inicializar eventos específicos da seção
                        initSectionEvents();
                    },
                    error: function(xhr) {
                        // Mostrar erro
                        $('#content-container').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Erro ao carregar conteúdo. Por favor, tente novamente.
                            </div>
                        `);
                        $('#content-container').show();
                        console.error('Erro ao carregar seção:', xhr);
                    },
                    complete: function() {
                        $('#content-loader').hide();
                    }
                });
            }
            
            // Função para inicializar eventos específicos após carregar uma seção
            function initSectionEvents() {
                // Eventos para a seção de cotação
                if ($('#cotacao-form').length) {
                    $('#cotacao-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        // Mostrar loader
                        $('#form-loader').show();
                        $('#cotacao-results').hide();
                        
                        // Enviar formulário via AJAX
                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                // Mostrar resultados
                                $('#cotacao-results').html(response).show();
                            },
                            error: function(xhr) {
                                // Mostrar erro
                                showAlert('Erro ao processar cotação. Por favor, tente novamente.', 'danger');
                            },
                            complete: function() {
                                $('#form-loader').hide();
                            }
                        });
                    });
                }
                
                // Eventos para a seção de rastreamento
                if ($('#rastreamento-form').length) {
                    $('#rastreamento-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        // Mostrar loader
                        $('#rastreamento-loader').show();
                        $('#rastreamento-results').hide();
                        
                        // Enviar formulário via AJAX
                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                // Mostrar resultados
                                $('#rastreamento-results').html(response).show();
                            },
                            error: function(xhr) {
                                // Mostrar erro
                                showAlert('Erro ao rastrear envio. Por favor, tente novamente.', 'danger');
                            },
                            complete: function() {
                                $('#rastreamento-loader').hide();
                            }
                        });
                    });
                }
                
                // Eventos para a seção de perfil
                if ($('#perfil-form').length) {
                    $('#perfil-form').on('submit', function(e) {
                        e.preventDefault();
                        
                        // Enviar formulário via AJAX
                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                showAlert('Perfil atualizado com sucesso!', 'success');
                            },
                            error: function(xhr) {
                                showAlert('Erro ao atualizar perfil. Por favor, tente novamente.', 'danger');
                            }
                        });
                    });
                }
                
                // Evento para navegação entre seções
                $('.menu-item').off('click').on('click', function() {
                    var section = $(this).data('section');
                    if (section) {
                        // Atualizar menu ativo
                        $('.menu-item').removeClass('active');
                        $(this).addClass('active');
                        
                        // Em dispositivos móveis, fechar o menu lateral após a seleção
                        if ($(window).width() <= 992) {
                            $('#sidebar').removeClass('expanded');
                        }
                        
                        // Carregar a seção
                        loadSection(section);
                    }
                });
                
                // Evento para botões de navegação dentro das seções
                $('.nav-section').off('click').on('click', function() {
                    var section = $(this).data('section');
                    if (section) {
                        // Atualizar menu ativo
                        $('.menu-item').removeClass('active');
                        $('.menu-item[data-section="' + section + '"]').addClass('active');
                        
                        // Carregar a seção
                        loadSection(section);
                    }
                });
            }
            
            // Inicializar os eventos para a primeira carga da página
            initSectionEvents();
            
            // Verificar se o conteúdo está vazio e carregar o dashboard
            if ($('#content-container').is(':empty')) {
                loadSection('dashboard');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html> 