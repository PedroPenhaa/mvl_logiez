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
            cursor: pointer;
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

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        /* Timeline para o rastreamento */
        .timeline {
            list-style-type: none;
            position: relative;
            padding-left: 30px;
        }

        .timeline:before {
            content: ' ';
            background: var(--primary-color);
            display: inline-block;
            position: absolute;
            left: 9px;
            width: 2px;
            height: 100%;
            z-index: 400;
        }

        .timeline-item {
            margin: 20px 0;
            padding-left: 20px;
            position: relative;
        }

        .timeline-item:before {
            content: ' ';
            background: white;
            border: 2px solid var(--primary-color);
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            left: -30px;
            width: 20px;
            height: 20px;
            z-index: 400;
        }

        /* Loader */
        .loader {
            display: none;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid var(--primary-color);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            <div class="menu-item" data-section="dashboard">
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
    <main>
        <div id="alert-container"></div>
        
        <div class="content-header">
            <h1 id="section-title">Dashboard</h1>
        </div>
        
        <div class="loader" id="content-loader"></div>
        
        <div id="content-container">
            <!-- Conteúdo será carregado aqui via JavaScript -->
            @include('sections.dashboard')
        </div>
    </main>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Configurar CSRF token para requisições AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Definir altura mínima do conteúdo principal
            let windowHeight = $(window).height();
            $('main').css('min-height', windowHeight);
            
            // Marcar o menu inicial como ativo
            $('.menu-item[data-section="dashboard"]').addClass('active');
            
            // Função para mostrar mensagem de alerta
            function showAlert(message, type) {
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
            
            // Carregar conteúdo inicial (dashboard)
            loadSection('dashboard');
            
            // Evento para itens do menu
            $('.menu-item').on('click', function(e) {
                e.preventDefault();
                
                // Remover classe ativa de todos os itens
                $('.menu-item').removeClass('active');
                
                // Adicionar classe ativa ao item clicado
                $(this).addClass('active');
                
                // Obter a seção a ser carregada
                const section = $(this).data('section');
                
                // Carregar a seção
                loadSection(section);
            });
            
            function loadSection(section) {
                // Mostrar indicador de carregamento
                $('#content-loader').show();
                $('#content-container').hide();
                
                // Atualizar o título da seção
                $('#section-title').text(section.charAt(0).toUpperCase() + section.slice(1));
                
                // Carregar conteúdo via AJAX
                $.ajax({
                    url: '/api/sections/' + section,
                    method: 'GET',
                    success: function(response) {
                        // Atualizar conteúdo
                        $('#content-container').html(response);
                        $('#content-container').show();
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
            
            // Inicialização para seção de cotação
            function initCotacaoSection() {
                $('#cotacao-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    $('#cotacao-loader').show();
                    $('#cotacao-resultado').hide();
                    
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.sucesso) {
                                const dados = response.dados;
                                $('#peso-cubado').text(dados.pesoCubado);
                                $('#peso-real').text(dados.pesoReal);
                                $('#peso-utilizado').text(dados.pesoUtilizado);
                                $('#preco').text(`${dados.preco} ${dados.moeda}`);
                                $('#prazo').text(`${dados.prazoEstimado} dias`);
                                
                                $('#cotacao-resultado').show();
                            } else {
                                showAlert('Ocorreu um erro ao calcular a cotação.', 'danger');
                            }
                        },
                        error: function() {
                            showAlert('Erro ao processar a cotação. Verifique os dados e tente novamente.', 'danger');
                        },
                        complete: function() {
                            $('#cotacao-loader').hide();
                        }
                    });
                });
            }
            
            // Inicialização para seção de rastreamento
            function initRastreamentoSection() {
                $('#rastreamento-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    $('#rastreamento-loader').show();
                    $('#rastreamento-resultado').hide();
                    
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#rastreamento-timeline').html(response);
                            $('#rastreamento-resultado').show();
                        },
                        error: function() {
                            showAlert('Erro ao rastrear o envio. Verifique o código e tente novamente.', 'danger');
                        },
                        complete: function() {
                            $('#rastreamento-loader').hide();
                        }
                    });
                });
            }
            
            // Inicialização para seção de perfil
            function initPerfilSection() {
                $('#editar-perfil-btn').click(function() {
                    $('#perfil-visualizacao').hide();
                    $('#perfil-edicao').show();
                });
                
                $('#cancelar-edicao-btn').click(function() {
                    $('#perfil-edicao').hide();
                    $('#perfil-visualizacao').show();
                });
                
                $('#perfil-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            showAlert('Perfil atualizado com sucesso!', 'success');
                            $('#perfil-edicao').hide();
                            $('#perfil-visualizacao').show();
                            
                            // Atualizar informações exibidas
                            for (const [key, value] of Object.entries(response.usuario)) {
                                $(`.perfil-${key}`).text(value);
                            }
                        },
                        error: function() {
                            showAlert('Erro ao atualizar o perfil. Verifique os dados e tente novamente.', 'danger');
                        }
                    });
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html> 