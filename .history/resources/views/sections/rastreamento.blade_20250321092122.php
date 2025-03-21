<div class="card">
    <div class="card-header">
        <i class="fas fa-map-marker-alt me-2"></i> Rastreamento de Envio
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
                <form id="rastreamento-form" action="{{ route('api.rastreamento.buscar') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" id="codigo_rastreamento" name="codigo_rastreamento" placeholder="Digite o código de rastreamento" required>
                        <button class="btn btn-primary" type="submit">Rastrear</button>
                    </div>
                    <div class="small text-muted text-center">
                        <i class="fas fa-info-circle me-1"></i> Digite o código de rastreamento fornecido no momento do envio.
                    </div>
                </form>
            </div>
        </div>
        
        <div class="loader mt-4" id="rastreamento-loader"></div>
        
        <div id="rastreamento-resultado" style="display: none;" class="mt-4">
            <div class="row">
                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i> <span id="rastreamento-codigo">DHL123456789</span></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-md-0 mb-3">
                                    <p><strong>Data de Postagem:</strong> <span id="data-postagem">20/03/2023</span></p>
                                    <p><strong>Origem:</strong> <span id="origem-envio">São Paulo, Brasil</span></p>
                                    <p><strong>Destino:</strong> <span id="destino-envio">Nova York, EUA</span></p>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="status-atual text-center p-3 rounded" style="background-color: rgba(106, 13, 173, 0.1);">
                                        <p class="mb-1 text-muted">Status Atual</p>
                                        <h4 class="mb-0 text-primary" id="status-atual">Em Trânsito</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Histórico de Rastreamento</h5>
                    
                    <div class="timeline-container">
                        <ul class="timeline" id="rastreamento-timeline">
                            <!-- Exemplo de timeline - será substituído dinamicamente -->
                            <li class="timeline-item">
                                <div class="timeline-badge bg-success"><i class="fas fa-check"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">Entrega Realizada</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock me-1"></i> 25/03/2023 14:52</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Objeto entregue ao destinatário</p>
                                        <p class="mb-0 small text-muted">Nova York, EUA</p>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-badge bg-primary"><i class="fas fa-truck"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">Em Rota de Entrega</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock me-1"></i> 25/03/2023 09:15</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Objeto saiu para entrega ao destinatário</p>
                                        <p class="mb-0 small text-muted">Nova York, EUA</p>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-badge bg-info"><i class="fas fa-plane-arrival"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">Chegada ao Destino</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock me-1"></i> 24/03/2023 18:30</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Objeto chegou ao país de destino</p>
                                        <p class="mb-0 small text-muted">Nova York, EUA</p>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-badge bg-secondary"><i class="fas fa-plane-departure"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">Saída do País de Origem</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock me-1"></i> 22/03/2023 10:45</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Objeto em trânsito - saiu do Brasil</p>
                                        <p class="mb-0 small text-muted">São Paulo, Brasil</p>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item">
                                <div class="timeline-badge bg-secondary"><i class="fas fa-box"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h6 class="timeline-title">Postado</h6>
                                        <p><small class="text-muted"><i class="fas fa-clock me-1"></i> 20/03/2023 16:22</small></p>
                                    </div>
                                    <div class="timeline-body">
                                        <p>Objeto postado</p>
                                        <p class="mb-0 small text-muted">São Paulo, Brasil</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para responsividade da timeline */
    .timeline-container {
        position: relative;
        padding: 20px 0;
    }
    
    @media (max-width: 767px) {
        .timeline:before {
            left: 30px;
        }
        
        .timeline-badge {
            left: 30px !important;
        }
        
        .timeline-panel {
            width: calc(100% - 60px) !important;
            margin-left: 60px !important;
        }
        
        .timeline-item {
            padding-left: 0 !important;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Processar formulário de rastreamento via AJAX
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            $('#rastreamento-loader').show();
            $('#rastreamento-resultado').hide();
            
            // Simulação de AJAX (para demonstração)
            setTimeout(function() {
                $('#rastreamento-loader').hide();
                $('#rastreamento-resultado').fadeIn();
                
                // Scroll suave para os resultados
                $('html, body').animate({
                    scrollTop: $('#rastreamento-resultado').offset().top - 100
                }, 500);
            }, 1500);
            
            // Implementação real com AJAX
            /*
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#rastreamento-timeline').html(response);
                    $('#rastreamento-resultado').show();
                    
                    // Scroll para os resultados
                    $('html, body').animate({
                        scrollTop: $('#rastreamento-resultado').offset().top - 100
                    }, 500);
                },
                error: function() {
                    showAlert('Código de rastreamento não encontrado. Verifique e tente novamente.', 'danger');
                },
                complete: function() {
                    $('#rastreamento-loader').hide();
                }
            });
            */
        });
    });
</script> 