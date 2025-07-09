<div class="cotacao-container">
    <!-- Header Section -->
    <div class="page-header-wrapper">
        <div class="page-header-content">
            
            <div class="header-content">
                <div class="title-section">
                    <div class="title-area">
                        <i class="fas fa-box-open me-2"></i>
                        <h1>Cotação de Envio Internacional</h1>
                    </div>
                    <p class="description">Calcule o valor do seu envio internacional de forma rápida e segura</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card main-card">
        <div class="card-body p-4">
            <form id="cotacao-form" method="POST" action="/calcular-cotacao">
                @csrf
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card feature-card origem-card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Origem
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="origem" name="origem" 
                                           placeholder="00000-000" required pattern="[0-9]{5}-[0-9]{3}">
                                    <label for="origem">CEP de Origem (Brasil)</label>
                                </div>
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP no formato: 00000-000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card feature-card destino-card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-flag-checkered me-2"></i>
                                    Destino
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="destino" name="destino" 
                                           placeholder="00000" required>
                                    <label for="destino">CEP de Destino</label>
                                </div>
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP do país de destino</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card feature-card dimensoes-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-box-open me-2"></i>
                            Dimensões do Pacote
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="altura" name="altura" required>
                                    <label for="altura">
                                        <i class="fas fa-arrows-alt-v me-1"></i> Altura (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="largura" name="largura" required>
                                    <label for="largura">
                                        <i class="fas fa-arrows-alt-h me-1"></i> Largura (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="comprimento" name="comprimento" required>
                                    <label for="comprimento">
                                        <i class="fas fa-arrows-alt me-1"></i> Comprimento (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="0.1" class="form-control" 
                                           id="peso" name="peso" required>
                                    <label for="peso">
                                        <i class="fas fa-weight-hanging me-1"></i> Peso (kg)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Dica Importante</h6>
                                    <p class="mb-0">Para cotação internacional, é importante fornecer as dimensões e peso corretos para obter um valor preciso.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="cotacao-loader" class="text-center my-4" style="display: none;">
                    <div class="loader-container">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-3">Calculando as melhores opções de envio...</p>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="calcular-cotacao">
                        <i class="fas fa-calculator me-2"></i> Calcular Cotação
                    </button>
                    <button type="button" id="limpar-form" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-broom me-2"></i>Limpar
                    </button>
                </div>
            </form>
            
            <div id="cotacao-resultado" class="mt-5" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Garantir que o loader está escondido inicialmente
    $('#cotacao-loader').hide();
    
    // Limpar formulário
    $('#limpar-form').on('click', function() {
        $('#cotacao-form')[0].reset();
        $('#cotacao-resultado').hide();
    });
    
    // Processar envio do formulário via AJAX
    $('#cotacao-form').on('submit', function(e) {
        e.preventDefault();
        
        // Mostrar o loader
        $('#cotacao-loader').show();
        
        // Esconder resultados anteriores
        $('#cotacao-resultado').hide();
        
        // Obter os dados do formulário
        var formData = $(this).serialize();
        
        // Enviar para o endpoint de cotação
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
                // Processar a resposta usando a função compartilhada
                processarResposta(response);
            },
            error: function(xhr, status, error) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
                // Usar a função compartilhada para exibir erros
                exibirErro(xhr);
            }
        });
    });
    
    // Função para processar a resposta
    function processarResposta(response) {
        if (response.success === false) {
            // Mostrar mensagem de erro
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">Serviço Indisponível</h4></div>';
            html += '<div class="card-body">';
            html += '<div class="alert alert-danger">';
            html += '<i class="fas fa-exclamation-circle me-2"></i> O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
            html += '</div>';
            html += '</div></div>';
            
            $('#cotacao-resultado').html(html).fadeIn();
            return;
        }
        
        if (response.success) {
            // Montar HTML para exibir os resultados
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-success text-white"><h4 class="mb-0">Cotação calculada com sucesso!</h4></div>';
            html += '<div class="card-body">';
            
            // Detalhes do peso
            html += '<div class="row mb-4">';
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Cúbico</h5>';
            html += '<p class="fs-4">' + response.pesoCubico + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Real</h5>';
            html += '<p class="fs-4">' + response.pesoReal + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Utilizado</h5>';
            html += '<p class="fs-4 fw-bold">' + response.pesoUtilizado + ' kg</p>';
            html += '</div></div></div>';
            html += '</div>';
            
            // Opções de Envio
            if (response.cotacoesFedEx && response.cotacoesFedEx.length > 0) {
                html += '<h4 class="mb-3">Opções de Envio</h4>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped table-hover">';
                html += '<thead><tr>';
                html += '<th>Serviço</th>';
                html += '<th>Tempo de Entrega</th>';
                html += '<th>Valor (USD)</th>';
                html += '<th>Valor (BRL)</th>';
                html += '</tr></thead><tbody>';
                
                response.cotacoesFedEx.forEach(function(cotacao) {
                    html += '<tr>';
                    html += '<td>' + cotacao.servico + '</td>';
                    html += '<td>';
                    if (cotacao.tempoEntrega) {
                        html += cotacao.tempoEntrega;
                    } else {
                        html += 'Consultar';
                    }
                    html += '</td>';
                    html += '<td>' + cotacao.valorTotal + ' ' + cotacao.moeda + '</td>';
                    html += '<td class="fw-bold text-success">R$ ' + (cotacao.valorTotalBRL || '-') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                
                // Exibir a cotação do dólar usada
                if (response.cotacaoDolar) {
                    html += '<div class="alert alert-info mt-3">';
                    html += '<i class="fas fa-dollar-sign me-2"></i> Cotação do dólar utilizada: <strong>R$ ' + 
                            response.cotacaoDolar.toFixed(2).replace('.', ',') + '</strong>';
                    html += '</div>';
                }
            } else {
                html += '<div class="alert alert-warning">Nenhuma opção de envio encontrada para os parâmetros fornecidos.</div>';
            }
            
            html += '<div class="d-flex justify-content-between align-items-center mt-4">';
            html += '<div class="text-muted">Cotação calculada em: ' + response.dataConsulta + '</div>';
            
            html += '<div class="d-flex gap-2">';
            html += '<button id="btn-realizar-envio" class="btn btn-success">';
            html += '<i class="fas fa-shipping-fast me-2"></i>Realizar Envio</button>';
            html += '<button onclick="window.print();" class="btn btn-outline-secondary">';
            html += '<i class="fas fa-print me-2"></i>Imprimir</button>';
            
            if (response.hash) {
                html += '<a href="/exportar-cotacao-pdf?hash=' + response.hash + '" ';
                html += 'class="btn btn-danger" target="_blank">';
                html += '<i class="fas fa-file-pdf me-2"></i>Baixar PDF</a>';
            }
            html += '</div></div>';
            
            html += '</div></div>';
            
            // Exibir resultados
            $('#cotacao-resultado').html(html).fadeIn();
            
            // Scroll suave até os resultados
            $('html, body').animate({
                scrollTop: $('#cotacao-resultado').offset().top - 100
            }, 500);
        }
    }
    
    // Função para exibir erro
    function exibirErro(xhr) {
        var html = '<div class="card shadow">';
        html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">Serviço Indisponível</h4></div>';
        html += '<div class="card-body">';
        html += '<div class="alert alert-danger">';
        html += '<i class="fas fa-exclamation-circle me-2"></i> O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
        html += '</div>';
        html += '</div></div>';
        
        $('#cotacao-resultado').html(html).fadeIn();
    }

    // Após exibir resultados, adicionar o evento ao botão Realizar Envio
    $(document).on('click', '#btn-realizar-envio', function() {
        if (typeof loadSection === 'function') {
            loadSection('envio');
        } else {
            window.location.href = '/sections/envio';
        }
    });
});
</script>

<style>
/* Gradientes e Cores */
:root {
    --primary-gradient: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%);
    --secondary-gradient: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    --card-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.text-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

/* Container Principal */
.cotacao-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Cards */
.main-card {
    border: none;
    border-radius: 15px;
    box-shadow: var(--card-shadow);
}

.feature-card {
    border: none;
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow);
}

.card-header {
    background: var(--primary-gradient);
    color: white;
    border-radius: 12px 12px 0 0 !important;
    padding: 0.75rem 1rem;
}

.card-header h5 {
    font-size: 1rem;
    margin: 0;
}

.card-body {
    padding: 1rem;
}

/* Formulários */
.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.75rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.form-control:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.form-floating label {
    padding: 1rem;
    font-size: 0.875rem;
}

/* Botões */
.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
}

/* Loader */
.loader-container {
    padding: 2rem;
    background: rgba(255,255,255,0.9);
    border-radius: 12px;
}

.spinner-grow {
    width: 3rem;
    height: 3rem;
}

/* Breadcrumb */
.breadcrumb {
    background: transparent;
    padding: 0.5rem 0;
}

.breadcrumb-item a {
    color: #6f42c1;
    text-decoration: none;
}

/* Helpers */
.cep-helper {
    color: #6c757d;
    font-size: 0.75rem;
}

/* Alert Styles */
.alert {
    font-size: 0.875rem;
}

.alert-heading {
    font-size: 1rem;
}

/* Responsividade */
@media (max-width: 768px) {
    .cotacao-container {
        padding: 1rem;
    }
    
    .row {
        margin: 0 -0.5rem;
    }
    
    .btn {
        width: 100%;
        margin: 0.25rem 0;
    }
    
    .d-flex {
        flex-direction: column;
    }
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

#cotacao-resultado {
    animation: fadeIn 0.5s ease-out;
}

/* Print Styles */
@media print {
    .card-header, form, .btn, .sidebar, .toggle-sidebar {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
    .cotacao-container {
        padding: 0;
    }
}

/* Header Styles */
.page-header-wrapper {
    background: var(--primary-gradient);
    border-radius: 15px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.page-header-content {
    width: 100%;
}

.header-content {
    width: 100%;
}

.title-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.title-area {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.title-area i {
    font-size: 1.25rem;
    color: white;
}

.title-area h1 {
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.description {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.875rem;
    margin: 0;
    text-align: right;
    max-width: 100%;
}

@media (max-width: 768px) {
    .page-header-wrapper {
        padding: 0.75rem 1rem;
    }

    .title-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .title-area {
        width: 100%;
    }

    .description {
        text-align: left;
        max-width: 100%;
        margin-top: 0.5rem;
    }
}

/* Atualização dos estilos do breadcrumb */
.breadcrumb-item {
    display: flex;
    align-items: center;
    color: rgba(255,255,255,0.9);
}

.breadcrumb-item a {
    display: flex;
    align-items: center;
    color: rgba(255,255,255,0.9);
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: white;
}

.breadcrumb-item.active {
    color: rgba(255,255,255,0.7);
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.7);
    content: "/"
}

/* Tablets */
@media (min-width: 769px) and (max-width: 1024px) {
    .title-area h1 {
        font-size: 1.125rem;
    }

    .description {
        font-size: 0.813rem;
    }

    .card-header h5 {
        font-size: 0.938rem;
    }
}

/* Desktop pequeno */
@media (min-width: 1025px) {
    .title-area h1 {
        font-size: 1.25rem;
    }

    .btn {
        min-width: 160px;
    }
}

/* Ajustes para telas muito pequenas */
@media (max-width: 320px) {
    .title-area h1 {
        font-size: 1rem;
    }

    .card-header h5 {
        font-size: 0.875rem;
    }

    .form-floating label {
        font-size: 0.813rem;
    }
}
</style> 