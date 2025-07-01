<div class="container mt-4">
    <h2 class="mb-4 text-center">Cotação de Envio Internacional</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <form id="cotacao-form" method="POST" action="/calcular-cotacao">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header text-white" style="background-color: #6f42c1;">
                                <h5 class="mb-0">Origem</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="origem" class="form-label">CEP de Origem (Brasil)</label>
                                    <input type="text" class="form-control" id="origem" name="origem" placeholder="00000-000" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header text-white" style="background-color: #6f42c1;">
                                <h5 class="mb-0">Destino</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="destino" class="form-label">CEP de Destino (EUA)</label>
                                    <input type="text" class="form-control" id="destino" name="destino" placeholder="00000" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header text-white"  style="background-color: #6f42c1;">
                        <h5 class="mb-0">Dimensões do Pacote</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="altura" class="form-label">Altura (cm)</label>
                                <input type="number" step="0.01" min="1" class="form-control" id="altura" name="altura" required>
                            </div>
                            <div class="col-md-3">
                                <label for="largura" class="form-label">Largura (cm)</label>
                                <input type="number" step="0.01" min="1" class="form-control" id="largura" name="largura" required>
                            </div>
                            <div class="col-md-3">
                                <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                <input type="number" step="0.01" min="1" class="form-control" id="comprimento" name="comprimento" required>
                            </div>
                            <div class="col-md-3">
                                <label for="peso" class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" min="0.1" class="form-control" id="peso" name="peso" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Para cotação internacional, é importante fornecer as dimensões e peso corretos para obter um valor preciso.
                        </div>
                    </div>
                </div>
                
                <div id="cotacao-loader" class="text-center my-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p>Calculando as melhores opções de envio...</p>
                </div>
                
                <div class="mb-4 text-center d-flex justify-content-center gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="calcular-cotacao" style="background-color: #6f42c1;">
                        <i class="fas fa-calculator me-2"></i> Calcular Cotação
                    </button>
                    <button type="button" id="limpar-form" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-broom me-2"></i>Limpar
                    </button>
                </div>
            </form>
            
            <div id="cotacao-resultado" class="mt-4" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- Modal para quando a API da FedEx estiver indisponível -->
<div class="modal fade" id="fedexErrorModal" tabindex="-1" aria-labelledby="fedexErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="fedexErrorModalLabel">Serviço FedEx Indisponível</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>O serviço da FedEx está temporariamente indisponível para cotações em tempo real.</p>
                <p>Você gostaria de:</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="tryAgainButton">
                    <i class="fas fa-sync-alt me-2"></i>Tentar Novamente
                </button>
                <button type="button" class="btn btn-primary" id="useSimulationButton">
                    <i class="fas fa-calculator me-2"></i>Usar Cotação Simulada
                </button>
            </div>
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
        
        // DEBUG - Verificar a URL do formulário
        //console.log('Form action URL:', $(this).attr('action'));
        
        // Mostrar o loader
        $('#cotacao-loader').show();
        
        // Esconder resultados anteriores
        $('#cotacao-resultado').hide();
        
        // Obter os dados do formulário
        var formData = $(this).serialize();
        
        // Armazenar os dados do formulário para reutilização
        window.lastFormData = formData;
        
        // Log dos dados que serão enviados
        //console.log('Enviando dados para cotação:', {
        //    origem: $('#origem').val(),
        //    destino: $('#destino').val(),
        //    altura: $('#altura').val(),
        //    largura: $('#largura').val(),
        //    comprimento: $('#comprimento').val(),
        //    peso: $('#peso').val()
        //});
        
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
                
                // Exibir logs detalhados no console para depuração
                console.log('Resposta completa da API FedEx:', response);
                
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
    
    // Configurar o botão "Tentar Novamente" no modal
    $('#tryAgainButton').on('click', function() {
        // Reenviar o formulário
        if (window.lastFormData) {
            // Ocultar o modal atual
            var fedexModal = bootstrap.Modal.getInstance(document.getElementById('fedexErrorModal'));
            fedexModal.hide();
            
            // Mostrar o loader
            $('#cotacao-loader').show();
            
            $.ajax({
                url: $('#cotacao-form').attr('action'),
                type: 'POST',
                data: window.lastFormData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Esconder o loader
                    $('#cotacao-loader').hide();
                    
                    // Verificar se é o erro específico da FedEx indisponível
                    if (response.success === false && response.error_code === 'fedex_unavailable') {
                        // Mostrar o modal de erro da FedEx novamente
                        var fedexModal = new bootstrap.Modal(document.getElementById('fedexErrorModal'));
                        fedexModal.show();
                        return;
                    }
                    
                    // Se não for erro de indisponibilidade, processa a resposta normalmente
                    processarResposta(response);
                },
                error: function(xhr) {
                    $('#cotacao-loader').hide();
                    exibirErro(xhr);
                }
            });
        }
    });
    
    // Configurar o botão "Usar Cotação Simulada" no modal
    $('#useSimulationButton').on('click', function() {
        // Ocultar o modal
        var fedexModal = bootstrap.Modal.getInstance(document.getElementById('fedexErrorModal'));
        fedexModal.hide();
        
        // Mostrar loader
        $('#cotacao-loader').show();
        
        // Solicitar cotação simulada
        if (window.lastFormData) {
            // Adicionar o parâmetro forcarSimulacao=true ao formData
            var simulationData = window.lastFormData + '&forcarSimulacao=true';
            
            $.ajax({
                url: $('#cotacao-form').attr('action'),
                type: 'POST',
                data: simulationData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#cotacao-loader').hide();
                    processarResposta(response);
                },
                error: function(xhr) {
                    $('#cotacao-loader').hide();
                    exibirErro(xhr);
                }
            });
        }
    });
    
    // Função para processar a resposta
    function processarResposta(response) {
        // Verificar se é o erro específico da FedEx indisponível
        if (response.success === false) {
            // Mostrar mensagem de erro
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">Erro na Cotação</h4></div>';
            html += '<div class="card-body">';
            html += '<div class="alert alert-danger">';
            html += '<i class="fas fa-exclamation-circle me-2"></i> ' + response.message;
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
            
            // Se for simulação, mostrar aviso
            if (response.simulado) {
                html += '<div class="alert alert-info mb-4">';
                html += '<i class="fas fa-info-circle me-2"></i> ' + response.mensagem;
                html += '</div>';
            }
            
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
        } else {
            // Exibir mensagem de erro
            var html = '<div class="alert alert-danger">';
            html += '<h4>Erro ao calcular cotação</h4>';
            html += '<p>' + (response.message || 'Ocorreu um erro ao processar sua cotação. Tente novamente.') + '</p>';
            html += '</div>';
            
            $('#cotacao-resultado').html(html).fadeIn();
        }
    }
    
    // Função para exibir erro
    function exibirErro(xhr) {
        // Log do erro
        //console.error('Erro na requisição AJAX:', xhr.status, xhr.statusText);
        //console.error('Resposta:', xhr.responseText);
        
        // Exibir mensagem de erro
        var html = '<div class="alert alert-danger">';
        html += '<h4>Erro ao calcular cotação</h4>';
        
        try {
            var response = JSON.parse(xhr.responseText);
            html += '<p>' + (response.message || 'Ocorreu um erro ao processar sua cotação. Tente novamente.') + '</p>';
            
            // Se tiver detalhes adicionais
            if (response.error_details) {
                html += '<p><small>Detalhes técnicos: ' + response.error_details + '</small></p>';
            }
        } catch (e) {
            html += '<p>Ocorreu um erro ao processar sua cotação. Tente novamente.</p>';
            if (xhr.status) {
                html += '<p><small>Status do erro: ' + xhr.status + ' - ' + xhr.statusText + '</small></p>';
            }
        }
        
        html += '</div>';
        
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
@media print {
    .card-header, form, .btn, .sidebar, .toggle-sidebar {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        width: 100% !important;
    }
}
</style> 