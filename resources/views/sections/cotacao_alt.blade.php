<div class="container mt-4">
    <h2>Cotação de Envio Internacional</h2>
    
    <form id="cotacao-form" method="POST">
        @csrf
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="origem" class="form-label">CEP de Origem</label>
                <input type="text" class="form-control" id="origem" name="origem" required>
            </div>
            <div class="col-md-6">
                <label for="destino" class="form-label">CEP de Destino</label>
                <input type="text" class="form-control" id="destino" name="destino" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="altura" class="form-label">Altura (cm)</label>
                <input type="number" step="0.01" class="form-control" id="altura" name="altura" required>
            </div>
            <div class="col-md-3">
                <label for="largura" class="form-label">Largura (cm)</label>
                <input type="number" step="0.01" class="form-control" id="largura" name="largura" required>
            </div>
            <div class="col-md-3">
                <label for="comprimento" class="form-label">Comprimento (cm)</label>
                <input type="number" step="0.01" class="form-control" id="comprimento" name="comprimento" required>
            </div>
            <div class="col-md-3">
                <label for="peso" class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" class="form-control" id="peso" name="peso" required>
            </div>
        </div>
        
        <div id="cotacao-loader" class="text-center my-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p>Calculando melhor cotação...</p>
        </div>
        
        <div class="mb-4">
            <button type="submit" class="btn btn-primary" id="calcular-cotacao">Calcular Cotação</button>
        </div>
    </form>
    
    <div id="cotacao-resultado" class="mt-4" style="display: none;"></div>
</div>

<script>
$(document).ready(function() {
    // Garantir que o loader está escondido inicialmente
    $('#cotacao-loader').hide();
    
    // Processar envio do formulário via AJAX
    $('#cotacao-form').on('submit', function(e) {
        e.preventDefault();
        $('#cotacao-loader').show();
        
        // Obter os dados do formulário
        var formData = $(this).serialize();
        
        // Enviar para o endpoint usando URL direta em vez da função route()
        $.ajax({
            url: '/calcular-cotacao',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#cotacao-loader').hide();
                
                if (response.success) {
                    // Exibir os resultados da cotação
                    var html = '<div class="alert alert-success">';
                    html += '<h4>Cotação calculada com sucesso!</h4>';
                    html += '<p><strong>Peso Cúbico:</strong> ' + response.pesoCubico + ' kg</p>';
                    html += '<p><strong>Peso Real:</strong> ' + response.pesoReal + ' kg</p>';
                    html += '<p><strong>Peso Utilizado:</strong> ' + response.pesoUtilizado + ' kg</p>';
                    
                    if (response.cotacoesFedEx && response.cotacoesFedEx.length > 0) {
                        html += '<h5>Opções de Envio:</h5>';
                        html += '<ul>';
                        response.cotacoesFedEx.forEach(function(cotacao) {
                            html += '<li>';
                            html += '<strong>' + cotacao.servico + ':</strong> ';
                            html += cotacao.valorTotal + ' ' + cotacao.moeda;
                            if (cotacao.tempoEntrega) {
                                html += ' (Entrega em ' + cotacao.tempoEntrega + ')';
                            }
                            html += '</li>';
                        });
                        html += '</ul>';
                    }
                    
                    html += '</div>';
                    
                    $('#cotacao-resultado').html(html).show();
                } else {
                    // Exibir mensagem de erro
                    $('#cotacao-resultado').html(
                        '<div class="alert alert-danger">' + 
                        '<h4>Erro ao calcular cotação</h4>' +
                        '<p>' + (response.message || 'Ocorreu um erro ao processar sua solicitação.') + '</p>' +
                        '</div>'
                    ).show();
                }
            },
            error: function(xhr) {
                $('#cotacao-loader').hide();
                
                // Exibir mensagem de erro
                $('#cotacao-resultado').html(
                    '<div class="alert alert-danger">' + 
                    '<h4>Erro na requisição</h4>' +
                    '<p>Não foi possível processar sua solicitação.</p>' +
                    '</div>'
                ).show();
                
                //console.error('Erro:', xhr);
            }
        });
    });
});
</script> 