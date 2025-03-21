<div class="card">
    <div class="card-header">
        <i class="fas fa-map-marker-alt me-2"></i> Rastreamento de Envio
    </div>
    <div class="card-body">
        <form id="rastreamento-form" action="{{ route('api.rastreamento.rastrear') }}" method="POST">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group mb-4">
                        <input type="text" class="form-control form-control-lg" id="codigo_rastreio" name="codigo_rastreio" placeholder="Digite o código de rastreio" required>
                        <button class="btn btn-primary" type="submit">Rastrear</button>
                    </div>
                    <div class="text-center text-muted mb-4">
                        <small>O código de rastreio encontra-se na etiqueta de envio ou no e-mail de confirmação.</small>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="loader mt-4" id="rastreamento-loader"></div>
        
        <div id="rastreamento-resultado" style="display:none">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Informações do Envio</strong>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-primary" id="status-atual">Em Trânsito</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Código de Rastreio:</strong> <span id="codigo-display">DHL12345678</span>
                            </div>
                            <div class="mb-2">
                                <strong>Data de Postagem:</strong> <span id="data-postagem">15/10/2023</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>Origem:</strong> <span id="origem-display">São Paulo, Brasil</span>
                            </div>
                            <div class="mb-2">
                                <strong>Destino:</strong> <span id="destino-display">Miami, EUA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Histórico de Rastreamento</h5>
            
            <div id="rastreamento-timeline">
                <!-- O conteúdo da timeline será carregado aqui via AJAX -->
                <ul class="timeline">
                    <li class="timeline-item">
                        <div class="mb-1"><strong>19/10/2023 09:30</strong></div>
                        <div class="mb-1"><strong>Chegada ao Destino</strong></div>
                        <div class="text-muted mb-1">Envio chegou ao país de destino</div>
                        <div class="text-muted small">Miami, FL, USA</div>
                    </li>
                    <li class="timeline-item">
                        <div class="mb-1"><strong>17/10/2023 16:45</strong></div>
                        <div class="mb-1"><strong>Em Trânsito</strong></div>
                        <div class="text-muted mb-1">Envio em trânsito para o destino</div>
                        <div class="text-muted small">Aeroporto Internacional de Guarulhos, SP</div>
                    </li>
                    <li class="timeline-item">
                        <div class="mb-1"><strong>16/10/2023 10:15</strong></div>
                        <div class="mb-1"><strong>Em Processamento</strong></div>
                        <div class="text-muted mb-1">Envio em processamento no centro de distribuição</div>
                        <div class="text-muted small">São Paulo, SP</div>
                    </li>
                    <li class="timeline-item">
                        <div class="mb-1"><strong>15/10/2023 08:30</strong></div>
                        <div class="mb-1"><strong>Registrado</strong></div>
                        <div class="text-muted mb-1">Envio registrado no sistema</div>
                        <div class="text-muted small">São Paulo, SP</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Exemplo para demonstração - mostrar o resultado
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            $('#rastreamento-loader').show();
            
            // Simular uma chamada AJAX com atraso para efeito visual
            setTimeout(function() {
                $('#rastreamento-loader').hide();
                
                // Preencher informações de exemplo
                $('#codigo-display').text($('#codigo_rastreio').val() || 'DHL12345678');
                $('#data-postagem').text('15/10/2023');
                $('#origem-display').text('São Paulo, Brasil');
                $('#destino-display').text('Miami, EUA');
                $('#status-atual').text('Em Trânsito');
                
                // Mostrar o resultado
                $('#rastreamento-resultado').show();
            }, 1000);
        });
    });
</script> 