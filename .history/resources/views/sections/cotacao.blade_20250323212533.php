<div class="container mt-4">
    <h2 class="mb-4 text-center">Cotação de Envio Internacional</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <form id="cotacao-form" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
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
                            <div class="card-header bg-primary text-white">
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
                    <div class="card-header bg-primary text-white">
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
                
                <div class="mb-4 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="calcular-cotacao">
                        <i class="fas fa-calculator me-2"></i> Calcular Cotação
                    </button>
                </div>
            </form>
            
            <div id="cotacao-resultado" class="mt-4" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Garantir que o loader está escondido inicialmente
    $('#cotacao-loader').hide();
    
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
            url: '/calcular-cotacao',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
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
                        html += '<th>Valor</th>';
                        html += '<th>Ação</th>';
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
                            html += '<td class="fw-bold">' + cotacao.valorTotal + ' ' + cotacao.moeda + '</td>';
                            html += '<td><a href="/envio?servico=' + cotacao.servicoTipo + '&valor=' + cotacao.valorTotal + '" class="btn btn-sm btn-primary">Escolher</a></td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                    } else {
                        html += '<div class="alert alert-warning">Nenhuma opção de envio encontrada para os parâmetros fornecidos.</div>';
                    }
                    
                    html += '<div class="text-muted mt-3">Cotação calculada em: ' + response.dataConsulta + '</div>';
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
                    html += '<p>' + (response.message || 'Ocorreu um erro ao processar sua solicitação.') + '</p>';
                    if (response.resposta) {
                        html += '<details>';
                        html += '<summary>Detalhes técnicos</summary>';
                        html += '<pre>' + JSON.stringify(response.resposta, null, 2) + '</pre>';
                        html += '</details>';
                    }
                    html += '</div>';
                    
                    $('#cotacao-resultado').html(html).show();
                }
            },
            error: function(xhr) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
                // Exibir mensagem de erro
                var html = '<div class="alert alert-danger">';
                html += '<h4>Erro na requisição</h4>';
                html += '<p>Não foi possível processar sua solicitação.</p>';
                
                if (xhr.responseJSON) {
                    html += '<details>';
                    html += '<summary>Detalhes técnicos</summary>';
                    html += '<pre>' + JSON.stringify(xhr.responseJSON, null, 2) + '</pre>';
                    html += '</details>';
                }
                
                html += '</div>';
                
                $('#cotacao-resultado').html(html).show();
                
                console.error('Erro:', xhr);
            }
        });
    });
});
</script> 