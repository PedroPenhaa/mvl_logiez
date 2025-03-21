<div class="rastreamento-container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-map-marker-alt me-2 text-primary"></i> Rastreamento de Envio
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <h5 class="mb-3">Acompanhe seu envio</h5>
                    <p class="text-muted">
                        Digite o código de rastreamento para verificar o status atual da sua encomenda. 
                        Você pode rastrear qualquer envio realizado através da Logiez ou diretamente com as 
                        transportadoras parceiras.
                    </p>
                    
                    <form id="rastreamento-form" action="{{ route('api.rastreio.consultar') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" id="codigo_rastreio" 
                                name="codigo_rastreio" placeholder="Digite o código de rastreamento" required>
                            <button class="btn btn-primary" type="submit" id="btn-rastrear">
                                <i class="fas fa-search me-2"></i>Rastrear
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i> O código de rastreamento foi enviado para o seu e-mail e também pode ser encontrado na área "Meus Envios".
                        </div>
                    </form>
                    
                    <div class="recent-shipments d-none">
                        <h6 class="mb-3">Seus envios recentes</h6>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action" data-tracking="LZ12345678US">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">LZ12345678US</h6>
                                    <small class="text-muted">3 dias atrás</small>
                                </div>
                                <p class="mb-1">Enviado para: Nova York, EUA</p>
                                <small class="text-success">Em trânsito</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" data-tracking="LZ87654321CA">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">LZ87654321CA</h6>
                                    <small class="text-muted">7 dias atrás</small>
                                </div>
                                <p class="mb-1">Enviado para: Toronto, Canadá</p>
                                <small class="text-success">Entregue</small>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="tracking-image text-center mb-3">
                        <img src="{{ asset('img/tracking-illustration.svg') }}" alt="Rastreamento de envio" class="img-fluid" style="max-height: 200px;">
                    </div>
                    <div class="tracking-info text-center">
                        <h5>Por que rastrear com a Logiez?</h5>
                        <ul class="list-unstyled text-start mt-3">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Informações detalhadas em tempo real</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Notificações automáticas por e-mail</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Previsão precisa de entrega</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Integração com todas as transportadoras</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="resultado-rastreio" style="display: none;">
                <hr>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card border-light h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informações do Envio</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Código de Rastreio:</strong>
                                    <span id="result-tracking-code" class="ms-2">LZ12345678US</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Origem:</strong>
                                    <span id="result-origin" class="ms-2">São Paulo, Brasil</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Destino:</strong>
                                    <span id="result-destination" class="ms-2">Nova York, EUA</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Serviço:</strong>
                                    <span id="result-service" class="ms-2">DHL Express</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Data de Postagem:</strong>
                                    <span id="result-date" class="ms-2">01/09/2023</span>
                                </div>
                                <div>
                                    <strong>Previsão de Entrega:</strong>
                                    <span id="result-eta" class="ms-2">05/09/2023</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card border-light h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Status Atual</h5>
                            </div>
                            <div class="card-body">
                                <div class="current-status mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="status-icon me-3">
                                            <i class="fas fa-truck text-primary fa-2x"></i>
                                        </div>
                                        <div class="status-info">
                                            <h5 class="mb-1" id="result-status-text">Em Trânsito</h5>
                                            <p class="text-muted mb-0" id="result-status-detail">
                                                Seu pacote está em trânsito para o destino final
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-4" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" id="result-progress-bar" 
                                        style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="status-steps d-flex justify-content-between mb-3">
                                    <div class="status-step active">
                                        <div class="step-dot"></div>
                                        <div class="step-label">Postado</div>
                                    </div>
                                    <div class="status-step active">
                                        <div class="step-dot"></div>
                                        <div class="step-label">Em trânsito</div>
                                    </div>
                                    <div class="status-step">
                                        <div class="step-dot"></div>
                                        <div class="step-label">Chegada ao país</div>
                                    </div>
                                    <div class="status-step">
                                        <div class="step-dot"></div>
                                        <div class="step-label">Entregue</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card border-light">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Histórico de Movimentações</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-date mb-0">03/09/2023 - 14:30</h6>
                                            <p class="mb-0"><strong>Em trânsito</strong> - Frankfurt, Alemanha</p>
                                            <p class="text-muted">Objeto em trânsito - em rota de entrega para o destino</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-date mb-0">02/09/2023 - 22:15</h6>
                                            <p class="mb-0"><strong>Saída do centro de distribuição</strong> - São Paulo, Brasil</p>
                                            <p class="text-muted">Objeto encaminhado para transporte internacional</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-date mb-0">02/09/2023 - 10:45</h6>
                                            <p class="mb-0"><strong>Recebido no centro de distribuição</strong> - São Paulo, Brasil</p>
                                            <p class="text-muted">Objeto recebido pelos Correios do Brasil</p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-date mb-0">01/09/2023 - 15:20</h6>
                                            <p class="mb-0"><strong>Postado</strong> - São Paulo, Brasil</p>
                                            <p class="text-muted">Objeto postado pelo remetente</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button class="btn btn-outline-primary me-2" id="btn-compartilhar">
                            <i class="fas fa-share-alt me-2"></i> Compartilhar Rastreio
                        </button>
                        <button class="btn btn-primary" id="btn-notificacoes">
                            <i class="fas fa-bell me-2"></i> Ativar Notificações
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="empty-result" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Nenhum resultado encontrado</h4>
                <p class="text-muted">Não foi possível encontrar informações para o código de rastreamento informado.</p>
                <p>Verifique se o código está correto e tente novamente.</p>
            </div>
            
            <div class="loader-container text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-3">Consultando informações do rastreio...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .rastreamento-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .status-step {
        text-align: center;
        position: relative;
        width: 25%;
    }
    
    .status-step .step-dot {
        width: 16px;
        height: 16px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: block;
        margin: 0 auto 8px;
        z-index: 1;
        position: relative;
        border: 3px solid #fff;
    }
    
    .status-step.active .step-dot {
        background-color: var(--primary-color);
    }
    
    .status-step .step-label {
        font-size: 0.8rem;
        color: var(--gray);
    }
    
    .status-step.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .timeline {
        position: relative;
        padding-left: 1.5rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0.75rem;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-marker {
        position: absolute;
        left: -1.5rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: var(--primary-color);
        top: 0.3rem;
    }
    
    .timeline-date {
        font-size: 0.85rem;
        color: var(--gray);
    }
</style>

<script>
    $(document).ready(function() {
        const sampleTrackingData = {
            'LZ12345678US': {
                origin: 'São Paulo, Brasil',
                destination: 'Nova York, EUA',
                service: 'DHL Express',
                date: '01/09/2023',
                eta: '05/09/2023',
                status: 'Em Trânsito',
                statusDetail: 'Seu pacote está em trânsito para o destino final',
                progress: 65,
                currentStep: 1, // 0-based index, 0=Postado, 1=Em trânsito
                hasResult: true
            },
            'LZ87654321CA': {
                origin: 'Rio de Janeiro, Brasil',
                destination: 'Toronto, Canadá',
                service: 'DHL Economy',
                date: '28/08/2023',
                eta: '04/09/2023',
                status: 'Entregue',
                statusDetail: 'Seu pacote foi entregue com sucesso',
                progress: 100,
                currentStep: 3, // Entregue
                hasResult: true
            }
        };
        
        // Verificar se o usuário está logado para mostrar envios recentes
        if (false) { // Aqui seria uma condição real para verificar autenticação
            $('.recent-shipments').removeClass('d-none');
        }
        
        // Clique nos envios recentes
        $('.recent-shipments a').on('click', function(e) {
            e.preventDefault();
            const trackingCode = $(this).data('tracking');
            $('#codigo_rastreio').val(trackingCode);
            $('#rastreamento-form').submit();
        });
        
        // Processar o formulário de rastreamento
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            const trackingCode = $('#codigo_rastreio').val().trim();
            if (!trackingCode) {
                return;
            }
            
            // Mostrar loader
            $('.loader-container').show();
            $('#resultado-rastreio, #empty-result').hide();
            
            // Simular chamada à API (3 segundos)
            setTimeout(function() {
                $('.loader-container').hide();
                
                const trackingData = sampleTrackingData[trackingCode];
                
                if (trackingData && trackingData.hasResult) {
                    // Preencher dados do resultado
                    $('#result-tracking-code').text(trackingCode);
                    $('#result-origin').text(trackingData.origin);
                    $('#result-destination').text(trackingData.destination);
                    $('#result-service').text(trackingData.service);
                    $('#result-date').text(trackingData.date);
                    $('#result-eta').text(trackingData.eta);
                    $('#result-status-text').text(trackingData.status);
                    $('#result-status-detail').text(trackingData.statusDetail);
                    $('#result-progress-bar').css('width', trackingData.progress + '%');
                    
                    // Atualizar etapas de status
                    $('.status-step').removeClass('active');
                    $('.status-step').each(function(index) {
                        if (index <= trackingData.currentStep) {
                            $(this).addClass('active');
                        }
                    });
                    
                    // Mostrar resultado
                    $('#resultado-rastreio').show();
                    
                    // Rolar para o resultado
                    $('html, body').animate({
                        scrollTop: $('#resultado-rastreio').offset().top - 100
                    }, 500);
                } else {
                    // Mostrar mensagem de erro
                    $('#empty-result').show();
                }
            }, 3000);
        });
        
        // Botão de compartilhar
        $('#btn-compartilhar').on('click', function() {
            const trackingCode = $('#result-tracking-code').text();
            const shareUrl = window.location.origin + '/rastreio/' + trackingCode;
            
            // Verificar se a API de compartilhamento está disponível
            if (navigator.share) {
                navigator.share({
                    title: 'Rastreamento Logiez',
                    text: 'Acompanhe meu envio com a Logiez',
                    url: shareUrl
                });
            } else {
                // Clipboard fallback
                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = shareUrl;
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                alert('Link de rastreamento copiado para a área de transferência!');
            }
        });
        
        // Botão de ativar notificações
        $('#btn-notificacoes').on('click', function() {
            $(this).html('<i class="fas fa-check me-2"></i> Notificações Ativadas');
            $(this).removeClass('btn-primary').addClass('btn-success');
            $(this).prop('disabled', true);
            
            // Aqui seria implementada a lógica real para ativar notificações
        });
    });
</script> 