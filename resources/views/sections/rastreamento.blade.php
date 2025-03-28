<div class="card">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-map-marker-alt me-2"></i> Rastreamento de Envio FedEx
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
                <form id="rastreamento-form" action="{{ route('api.rastreamento.buscar') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" id="codigo_rastreamento" name="codigo_rastreamento" placeholder="Digite o código de rastreamento FedEx" required>
                        <button class="btn btn-primary" type="submit">Rastrear</button>
                    </div>
                    <div class="small text-muted text-center">
                        <i class="fas fa-info-circle me-1"></i> Digite o código de rastreamento fornecido no momento do envio.
                    </div>
                </form>
            </div>
        </div>
        
        <div class="d-flex justify-content-center mt-4" id="rastreamento-loader" style="display: none !important;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <div class="ms-2">Consultando informações de rastreamento...</div>
        </div>
        
        <div id="rastreamento-error" class="alert alert-danger mt-4" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i> <span id="rastreamento-error-message"></span>
        </div>
        
        <div id="rastreamento-resultado" style="display: none;" class="mt-4">
            <div class="row">
                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i> <span id="rastreamento-codigo"></span></h5>
                            <div>
                                <span class="badge bg-primary me-2" id="rastreamento-servico"></span>
                                <button id="btn-solicitar-comprovante" class="btn btn-sm btn-outline-primary ms-2" style="display: none;" title="Solicitar comprovante de entrega assinado">
                                    <i class="fas fa-file-signature"></i> Comprovante de Entrega
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-md-0 mb-3">
                                    <p><strong>Data de Postagem:</strong> <span id="data-postagem">-</span></p>
                                    <p><strong>Origem:</strong> <span id="origem-envio">-</span></p>
                                    <p><strong>Destino:</strong> <span id="destino-envio">-</span></p>
                                    <p><strong>Entrega Prevista:</strong> <span id="entrega-prevista">-</span></p>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div id="status-atual-container" class="status-atual text-center p-3 rounded" style="background-color: rgba(99, 73, 158, 0.1);">
                                        <p class="mb-1 text-muted">Status Atual</p>
                                        <h4 class="mb-0" id="status-atual" style="color: #63499E">-</h4>
                                        <p class="mt-2 mb-0 small" id="status-atualizacao">Atualizado em: -</p>
                                    </div>
                                    
                                    <div id="status-atraso-container" class="mt-3 status-atraso text-center p-3 rounded" style="background-color: rgba(220, 53, 69, 0.1); display: none;">
                                        <p class="mb-1 text-muted">Atraso Identificado</p>
                                        <p class="mb-0 text-danger" id="status-atraso-detalhes"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Histórico de Rastreamento</h5>
                    
                    <div class="timeline-container">
                        <ul class="timeline" id="rastreamento-timeline">
                            <!-- Eventos de rastreamento serão inseridos aqui dinamicamente -->
                        </ul>
                    </div>
                    
                    <div id="rastreamento-simulado-alert" class="alert alert-warning mt-4" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i> <span id="rastreamento-simulado-message"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir o comprovante de entrega -->
<div class="modal fade" id="comprovanteModal" tabindex="-1" aria-labelledby="comprovanteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="comprovanteModalLabel">Comprovante de Entrega</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="comprovante-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div>Solicitando comprovante de entrega...</div>
                </div>
                <div id="comprovante-error" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> <span id="comprovante-error-message"></span>
                </div>
                <div id="comprovante-content" style="display: none;">
                    <!-- O conteúdo do comprovante será exibido aqui -->
                    <div class="ratio ratio-16x9">
                        <iframe id="comprovante-iframe" class="embed-responsive-item" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-download-comprovante">
                    <i class="fas fa-download me-1"></i> Baixar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para perguntar sobre simulação quando a API falha -->
<div class="modal fade" id="simulacaoModal" tabindex="-1" aria-labelledby="simulacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simulacaoModalLabel">Serviço FedEx Indisponível</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="simulacao-error-message">Não foi possível conectar ao serviço de rastreamento da FedEx.</span>
                </div>
                <p>Deseja visualizar uma simulação de rastreamento em vez de dados reais?</p>
                <small class="text-muted">Nota: A simulação gera dados fictícios para fins de demonstração.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-usar-simulacao">
                    <i class="fas fa-check me-1"></i> Ver Simulação
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
    }
    
    .timeline:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 50px;
        margin-bottom: 25px;
    }
    
    .timeline-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        line-height: 40px;
        color: white;
    }
    
    .timeline-badge i {
        font-size: 1rem;
    }
    
    .timeline-panel {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .timeline-title {
        margin-top: 0;
        font-size: 1rem;
        font-weight: 600;
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
        // Variáveis globais para armazenar o código de rastreamento
        let codigoRastreamento = '';
        
        // Processar formulário de rastreamento via AJAX
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            // Armazenar o código de rastreamento
            codigoRastreamento = $('#codigo_rastreamento').val().trim();
            
            // Esconder mensagens de erro anteriores
            $('#rastreamento-error').hide();
            
            // Mostrar loader e esconder resultados anteriores
            $('#rastreamento-loader').show();
            $('#rastreamento-resultado').hide();
            
            // Enviar solicitação AJAX para a API
            enviarSolicitacaoRastreamento(false);
        });
        
        // Função para enviar a solicitação AJAX de rastreamento
        function enviarSolicitacaoRastreamento(forcarSimulacao) {
            $.ajax({
                url: '{{ route("api.rastreamento.buscar") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    codigo_rastreamento: codigoRastreamento,
                    forcarSimulacao: forcarSimulacao
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Preencher dados do resultado
                        mostrarResultadoRastreamento(response);
                    } else {
                        // Verificar código de erro para decidir o que mostrar
                        if (response.error_code === 'fedex_unavailable' || response.error_code === 'fedex_api_error') {
                            // Mostrar modal perguntando sobre simulação
                            $('#simulacao-error-message').text(response.message);
                            const simulacaoModal = new bootstrap.Modal(document.getElementById('simulacaoModal'));
                            simulacaoModal.show();
                        } else {
                            // Mostrar mensagem de erro padrão
                            $('#rastreamento-error').show();
                            $('#rastreamento-error-message').text(response.message || 'Não foi possível obter informações de rastreamento.');
                        }
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Erro ao processar a solicitação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#rastreamento-error').show();
                    $('#rastreamento-error-message').text(errorMsg);
                },
                complete: function() {
                    $('#rastreamento-loader').hide();
                }
            });
        }
        
        // Botão para usar simulação
        $('#btn-usar-simulacao').on('click', function() {
            // Fechar o modal
            const simulacaoModal = bootstrap.Modal.getInstance(document.getElementById('simulacaoModal'));
            simulacaoModal.hide();
            
            // Mostrar loader novamente
            $('#rastreamento-loader').show();
            
            // Enviar solicitação com flag para forçar simulação
            enviarSolicitacaoRastreamento(true);
        });
        
        // Função para mostrar os resultados do rastreamento
        function mostrarResultadoRastreamento(response) {
            // Preencher dados do resultado
            $('#rastreamento-codigo').text(response.codigo);
            $('#origem-envio').text(response.origem || '-');
            $('#destino-envio').text(response.destino || '-');
            $('#data-postagem').text(formatarData(response.dataPostagem) || '-');
            $('#entrega-prevista').text(formatarData(response.dataEntregaPrevista) || '-');
            $('#status-atual').text(response.status || '-');
            $('#rastreamento-servico').text(response.servicoDescricao || 'FedEx');
            
            // Atualizar a última atualização
            if (response.ultimaAtualizacao) {
                $('#status-atualizacao').text('Atualizado em: ' + formatarDataHora(response.ultimaAtualizacao));
            } else {
                $('#status-atualizacao').text('');
            }
            
            // Verificar se há atraso
            if (response.temAtraso) {
                $('#status-atraso-container').show();
                $('#status-atraso-detalhes').text(response.detalhesAtraso || 'Há um atraso na entrega');
            } else {
                $('#status-atraso-container').hide();
            }
            
            // Verificar se está entregue para mudar a cor do status
            if (response.entregue) {
                $('#status-atual-container').css('background-color', 'rgba(40, 167, 69, 0.1)');
                $('#status-atual').css('color', '#28a745');
                
                // Exibir botão de solicitar comprovante
                $('#btn-solicitar-comprovante').show();
            } else {
                $('#status-atual-container').css('background-color', 'rgba(99, 73, 158, 0.1)');
                $('#status-atual').css('color', '#63499E');
                $('#btn-solicitar-comprovante').hide();
            }
            
            // Limpar e preencher a timeline
            preencherTimeline(response.eventos);
            
            // Verificar se é simulado
            if (response.simulado) {
                $('#rastreamento-simulado-alert').show();
                $('#rastreamento-simulado-message').text(response.mensagem || 'Atenção: Estes dados são simulados para demonstração.');
            } else {
                $('#rastreamento-simulado-alert').hide();
            }
            
            // Exibir resultados
            $('#rastreamento-resultado').fadeIn();
            
            // Scroll suave para os resultados
            $('html, body').animate({
                scrollTop: $('#rastreamento-resultado').offset().top - 100
            }, 500);
        }
        
        // Botão para solicitar comprovante de entrega
        $('#btn-solicitar-comprovante').on('click', function() {
            const trackingNumber = $('#rastreamento-codigo').text();
            if (!trackingNumber) return;
            
            // Mostrar modal
            const comprovanteModal = new bootstrap.Modal(document.getElementById('comprovanteModal'));
            comprovanteModal.show();
            
            // Preparar modal
            $('#comprovante-loader').show();
            $('#comprovante-error').hide();
            $('#comprovante-content').hide();
            
            // Solicitar comprovante
            $.ajax({
                url: '{{ route("api.rastreamento.comprovante") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    codigo_rastreamento: trackingNumber,
                    formato: 'PDF'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Base64 para URL
                        const base64Data = response.document;
                        const documentFormat = response.documentFormat.toLowerCase();
                        
                        // Criar URL para o documento
                        const documentUrl = 'data:application/' + documentFormat + ';base64,' + base64Data;
                        
                        // Exibir no iframe
                        $('#comprovante-iframe').attr('src', documentUrl);
                        $('#comprovante-content').show();
                        
                        // Configurar botão de download
                        $('#btn-download-comprovante').off('click').on('click', function() {
                            const a = document.createElement('a');
                            a.href = documentUrl;
                            a.download = 'Comprovante_Entrega_' + trackingNumber + '.' + documentFormat;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        });
                    } else {
                        // Exibir erro
                        $('#comprovante-error').show();
                        $('#comprovante-error-message').text(response.mensagem || 'Não foi possível obter o comprovante de entrega.');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Erro ao solicitar comprovante de entrega.';
                    if (xhr.responseJSON && xhr.responseJSON.mensagem) {
                        errorMsg = xhr.responseJSON.mensagem;
                    }
                    
                    $('#comprovante-error').show();
                    $('#comprovante-error-message').text(errorMsg);
                },
                complete: function() {
                    $('#comprovante-loader').hide();
                }
            });
        });
        
        // Função para preencher a timeline
        function preencherTimeline(eventos) {
            const timeline = $('#rastreamento-timeline');
            timeline.empty();
            
            if (!eventos || eventos.length === 0) {
                timeline.append('<li class="text-center text-muted">Nenhum evento de rastreamento disponível.</li>');
                return;
            }
            
            // Criar elemento para cada evento
            $.each(eventos, function(index, evento) {
                // Determinar ícone e cor com base no tipo de evento
                let icone = 'box';
                let corClasse = 'bg-secondary';
                
                // Status personalizado com base no código ou descrição do evento
                if (evento.status) {
                    const status = evento.status.toLowerCase();
                    
                    if (status.includes('entreg') || evento.codigo === 'DL') {
                        icone = 'check';
                        corClasse = 'bg-success';
                    } else if (status.includes('rota') || status.includes('saiu para') || status.includes('em trânsito')) {
                        icone = 'truck';
                        corClasse = 'bg-primary';
                    } else if (status.includes('chegada') || status.includes('chegou')) {
                        icone = 'plane-arrival';
                        corClasse = 'bg-info';
                    } else if (status.includes('saída') || status.includes('saiu do')) {
                        icone = 'plane-departure';
                        corClasse = 'bg-primary';
                    } else if (status.includes('atraso') || status.includes('exceção') || status.includes('problema')) {
                        icone = 'exclamation-triangle';
                        corClasse = 'bg-warning';
                    } else if (status.includes('alfândega') || status.includes('customs')) {
                        icone = 'clipboard-check';
                        corClasse = 'bg-info';
                    }
                }
                
                // Criar elemento de timeline
                const timelineItem = $(`
                    <li class="timeline-item">
                        <div class="timeline-badge ${corClasse}"><i class="fas fa-${icone}"></i></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">${evento.status || 'Evento'}</h6>
                                <p><small class="text-muted"><i class="fas fa-clock me-1"></i> ${formatarDataHora(evento.data, evento.hora)}</small></p>
                            </div>
                            <div class="timeline-body">
                                <p>${evento.descricao || ''}</p>
                                <p class="mb-0 small text-muted">${evento.local || ''}</p>
                            </div>
                        </div>
                    </li>
                `);
                
                timeline.append(timelineItem);
            });
        }
        
        // Função para formatar data (YYYY-MM-DD para DD/MM/YYYY)
        function formatarData(dataString) {
            if (!dataString) return '';
            
            // Tentar converter o formato YYYY-MM-DD para DD/MM/YYYY
            try {
                const partes = dataString.split('-');
                if (partes.length === 3) {
                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                }
                return dataString;
            } catch (e) {
                return dataString;
            }
        }
        
        // Função para formatar data e hora
        function formatarDataHora(data, hora) {
            if (!data) return '';
            
            let dataFormatada = formatarData(data);
            
            if (hora) {
                // Tentar formatar a hora (HH:MM:SS para HH:MM)
                try {
                    const partesHora = hora.split(':');
                    if (partesHora.length >= 2) {
                        return `${dataFormatada} ${partesHora[0]}:${partesHora[1]}`;
                    }
                    return `${dataFormatada} ${hora}`;
                } catch (e) {
                    return `${dataFormatada} ${hora}`;
                }
            }
            
            return dataFormatada;
        }
    });
</script> 