<div class="card shadow-lg">
    <div class="card-header text-white" style="background-color: #63499E;">
        <i class="fas fa-map-marker-alt me-2" style="font-size: 1.2rem; "></i> Rastreamento de Envio FedEx
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8 col-md-10 col-sm-12 mx-auto">
                <form id="rastreamento-form" action="{{ route('api.rastreamento.buscar') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3 shadow-sm">
                        <input type="text" class="form-control form-control-lg" id="codigo_rastreamento" name="codigo_rastreamento" placeholder="Digite o código de rastreamento FedEx" required>
                        <button class="btn" style="background-color: #63499E; color: #fff;" type="submit"><i class="fas fa-search me-2"></i>Rastrear</button>
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
        
        <div id="rastreamento-success" class="alert alert-success mt-4" style="display: none;">
            <i class="fas fa-check-circle me-2"></i> Consulta realizada com sucesso
        </div>
        
        <div id="rastreamento-error" class="alert alert-danger mt-4" style="display: none;">
            <i class="fas fa-exclamation-triangle me-2"></i> <span id="rastreamento-error-message"></span>
        </div>
        
        <div id="rastreamento-resultado" style="display: none;" class="mt-4">
            <div class="row">
                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="card mb-4 shadow-lg border-0">
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
                                    <div class="info-grupo p-3 rounded bg-light mb-2">
                                        <p class="mb-2"><strong><i class="fas fa-calendar-alt me-2 text-muted"></i>Data de Postagem:</strong> <span id="data-postagem">-</span></p>
                                        <p class="mb-2"><strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>Origem:</strong> <span id="origem-envio">-</span></p>
                                        <p class="mb-2"><strong><i class="fas fa-map-pin me-2 text-muted"></i>Destino:</strong> <span id="destino-envio">-</span></p>
                                        <p class="mb-0"><strong><i class="fas fa-clock me-2 text-muted"></i>Entrega Prevista:</strong> <span id="entrega-prevista">-</span></p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div id="status-atual-container" class="status-atual text-center p-3 rounded shadow-sm" style="background-color: rgba(99, 73, 158, 0.1);">
                                        <p class="mb-1 text-muted"><i class="fas fa-info-circle me-1"></i>Status Atual</p>
                                        <h4 class="mb-0" id="status-atual" style="color: #63499E">-</h4>
                                        <p class="mt-2 mb-0 small" id="status-atualizacao">Atualizado em: -</p>
                                    </div>
                                    
                                    <div id="status-atraso-container" class="mt-3 status-atraso text-center p-3 rounded shadow-sm" style="background-color: rgba(220, 53, 69, 0.1); display: none;">
                                        <p class="mb-1 text-muted"><i class="fas fa-exclamation-circle me-1"></i>Atraso Identificado</p>
                                        <p class="mb-0 text-danger" id="status-atraso-detalhes"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3"><i class="fas fa-history me-2"></i>Histórico de Rastreamento</h5>
                    
                    <div class="timeline-container">
                        <ul class="timeline" id="rastreamento-timeline">
                            <!-- Eventos de rastreamento serão inseridos aqui dinamicamente -->
                        </ul>
                    </div>
                    
                    <div id="rastreamento-simulado-alert" class="alert alert-warning mt-4 shadow-sm" style="display: none;">
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
                <h5 class="modal-title" id="comprovanteModalLabel"><i class="fas fa-file-signature me-2"></i>Comprovante de Entrega</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3 p-4" id="comprovante-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <div class="mt-2">Solicitando comprovante de entrega...</div>
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
                <h5 class="modal-title" id="simulacaoModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Serviço FedEx Indisponível</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="simulacao-error-message">Não foi possível conectar ao serviço de rastreamento da FedEx.</span>
                </div>
                <p>Deseja visualizar uma simulação de rastreamento em vez de dados reais?</p>
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Nota: A simulação gera dados fictícios para fins de demonstração.</small>
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

<!-- Seção para exibir logs de depuração -->
 
<div id="debug-logs-section" class="mt-5 mb-3 bg-gray-100 rounded-md p-4 hidden">
    <h3 class="text-lg font-semibold mb-2 flex items-center">
        <!--<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Logs de Rastreamento
        <button id="toggle-logs" class="ml-2 text-xs text-blue-600 hover:text-blue-800">Ocultar</button>
    </h3>
    <div id="logs-container" class="bg-gray-900 text-green-400 p-4 rounded-md font-mono text-sm overflow-auto" style="max-height: 400px;">
        <div id="logs-content"></div>
    </div>-->
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
        width: 3px;
        background: linear-gradient(to bottom, rgba(0,123,255,0.3), rgba(99, 73, 158, 0.3), rgba(40, 167, 69, 0.3));
        border-radius: 3px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }
    
    .timeline-item {
        position: relative;
        padding-left: 50px;
        margin-bottom: 30px;
    }
    
    .timeline-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        text-align: center;
        line-height: 42px;
        color: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        z-index: 1;
    }
    
    .timeline-item:hover .timeline-badge {
        transform: scale(1.2);
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.25);
    }
    
    .timeline-badge i {
        font-size: 1.1rem;
        line-height: inherit;
    }
    
    .timeline-panel {
        padding: 18px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        position: relative;
    }
    
    .timeline-panel:before {
        content: "";
        position: absolute;
        top: 18px;
        left: -10px;
        width: 0;
        height: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-right: 10px solid white;
        z-index: 1;
    }
    
    .timeline-item:hover .timeline-panel {
        transform: translateY(-3px) translateX(3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Cores personalizadas por tipo de evento */
    .timeline-item .bg-success {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    
    .timeline-item .bg-primary {
        background: linear-gradient(135deg, #007bff, #1e88e5);
    }
    
    .timeline-item .bg-info {
        background: linear-gradient(135deg, #17a2b8, #00b8d4);
    }
    
    .timeline-item .bg-warning {
        background: linear-gradient(135deg, #ffc107, #ffb300);
    }
    
    .timeline-item .bg-secondary {
        background: linear-gradient(135deg, #6c757d, #546e7a);
    }
    
    /* Bordas laterais coloridas por tipo de evento */
    .timeline-item:has(.bg-success) .timeline-panel {
        border-left-color: #28a745;
    }
    
    .timeline-item:has(.bg-primary) .timeline-panel {
        border-left-color: #007bff;
    }
    
    .timeline-item:has(.bg-info) .timeline-panel {
        border-left-color: #17a2b8;
    }
    
    .timeline-item:has(.bg-warning) .timeline-panel {
        border-left-color: #ffc107;
    }
    
    .timeline-item:has(.bg-secondary) .timeline-panel {
        border-left-color: #6c757d;
    }
    
    /* Destaque especial para o último evento */
    .timeline-item:first-child .timeline-panel {
        background-color: #f8f9fa;
        border-width: 4px;
    }
    
    .timeline-item:first-child .timeline-badge {
        width: 48px;
        height: 48px;
        line-height: 48px;
        left: -3px;
    }
    
    .timeline-item:first-child .timeline-badge i {
        font-size: 1.3rem;
    }
    
    .timeline-title {
        margin-top: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }
    
    /* Melhoria no layout das datas e localidades */
    .timeline-body .text-muted {
        color: #6c757d !important;
    }
    
    .timeline-heading .text-muted {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.03);
        padding: 3px 8px;
        border-radius: 4px;
    }
    
    .timeline-heading .text-muted i {
        color: #6c757d;
        margin-right: 5px;
    }
    
    .timeline-date-time {
        display: inline-block;
    }
    
    .timeline-date-time .text-muted {
        font-weight: 500;
        border-left: 3px solid rgba(108, 117, 125, 0.3);
        transition: all 0.2s ease;
    }
    
    .timeline-item:hover .timeline-date-time .text-muted {
        background-color: rgba(0, 0, 0, 0.06);
        border-left-color: rgba(108, 117, 125, 0.6);
    }
    
    /* Destaque especial para o último evento */
    .timeline-item:first-child .timeline-date-time .text-muted {
        background-color: rgba(0, 123, 255, 0.06);
        border-left-color: rgba(0, 123, 255, 0.4);
    }
    
    .timeline-badge.pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
        50% {
            transform: scale(1.15);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }
    }
    
    /* Estilos para o card principal */
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .card-header.bg-dark {
        background: linear-gradient(135deg, #343a40, #212529) !important;
        padding: 16px;
    }
    
    .card-body {
        padding: 25px;
    }
    
    /* Estilos para o formulário */
    .form-control-lg {
        border-radius: 8px 0 0 8px;
        border: 1px solid #ced4da;
        box-shadow: none;
        transition: border-color 0.2s;
    }
    
    .form-control-lg:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }
    
    .btn-primary {
        border-radius: 0 8px 8px 0;
        padding: 12px 24px;
        font-weight: 500;
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }
    
    /* Estilos para o card de resultado */
    #rastreamento-resultado .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    #rastreamento-resultado .card-header {
        padding: 16px 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    #rastreamento-resultado h5 {
        font-weight: 600;
        color: #333;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        padding: 6px 12px;
        font-weight: 500;
    }
    
    /* Estilos para o status atual */
    .status-atual {
        border-radius: 10px;
        padding: 20px !important;
        transition: transform 0.3s;
    }
    
    .status-atual:hover {
        transform: translateY(-3px);
    }
    
    #status-atual-container {
        background-color: rgba(99, 73, 158, 0.08) !important;
        border-left: 4px solid #63499E;
    }
    
    #status-atual {
        font-size: 1.4rem;
        font-weight: 600;
    }
    
    /* Status de atraso */
    #status-atraso-container {
        background-color: rgba(220, 53, 69, 0.08) !important;
        border-left: 4px solid #dc3545;
        border-radius: 10px;
    }
    
    /* Estilo para os alertas */
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }
    
    .alert-success {
        background-color: rgba(40, 167, 69, 0.12);
        border-left: 4px solid #28a745;
        color: #155724;
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.12);
        border-left: 4px solid #dc3545;
        color: #721c24;
    }
    
    .alert-warning {
        background-color: rgba(255, 193, 7, 0.12);
        border-left: 4px solid #ffc107;
        color: #856404;
    }
    
    /* Estilo para o loader */
    #rastreamento-loader {
        padding: 20px;
        border-radius: 8px;
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }
    
    /* Botão de solicitar comprovante */
    #btn-solicitar-comprovante {
        border-radius: 6px;
        transition: all 0.3s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    #btn-solicitar-comprovante:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Melhorias para as informações do pacote */
    #rastreamento-resultado p strong {
        color: #555;
        font-weight: 600;
    }
    
    /* Título do Histórico de Rastreamento */
    #rastreamento-resultado h5.mb-3 {
        font-weight: 600;
        color: #333;
        margin-top: 30px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
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
        
        .card-body {
            padding: 20px 15px;
        }
    }
    
    /* Estilos para os modais */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: #f8f9fa;
        padding: 15px 20px;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
        color: #333;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 15px 20px;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268, #3d4246);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    #btn-usar-simulacao {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    #btn-usar-simulacao:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    
    #btn-download-comprovante {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 6px;
        transition: all 0.3s;
    }
    
    #btn-download-comprovante:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    
    /* Loader nos modais */
    #comprovante-loader {
        padding: 20px 0;
    }
    
    .spinner-border {
        width: 2rem;
        height: 2rem;
        border-width: 0.2em;
    }
    
    .ratio-16x9 {
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    /* Melhorias gerais de usabilidade */
    .small.text-muted {
        color: #6c757d !important;
    }
    
    /* Animações suaves */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #rastreamento-resultado {
        animation: fadeIn 0.5s ease-out;
    }
    
    .timeline-item {
        animation: fadeIn 0.5s ease-out;
        animation-fill-mode: both;
    }
    
    .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .timeline-item:nth-child(5) { animation-delay: 0.5s; }
    .timeline-item:nth-child(6) { animation-delay: 0.6s; }
    .timeline-item:nth-child(7) { animation-delay: 0.7s; }
    .timeline-item:nth-child(8) { animation-delay: 0.8s; }
    
    /* Estilo para os separadores de data na timeline */
    .timeline-date {
        position: relative;
        z-index: 2;
        list-style: none;
    }
    
    .timeline-date .badge {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-weight: 500;
    }
    
    .timeline-divider {
        position: relative;
        z-index: 1;
    }
    
    .timeline-divider .badge {
        opacity: 0.8;
        font-size: 0.75rem;
    }
    
    /* Destaque para status de eventos específicos */
    .timeline-item:has(.bg-success) .timeline-title {
        color: #28a745;
    }
    
    .timeline-item:has(.bg-warning) .timeline-title {
        color: #e0a800;
    }
    
    .timeline-item:has(.bg-info) .timeline-title {
        color: #138496;
    }
    
    .timeline-item:has(.bg-primary) .timeline-title {
        color: #0069d9;
    }
    
    /* Melhoria visual para hover nos eventos */
    .timeline-item {
        transition: all 0.3s ease;
    }
    
    .timeline-item:hover {
        z-index: 5;
    }
</style>

<script>
    $(document).ready(function() {
        // Variáveis globais para armazenar o código de rastreamento
        let codigoRastreamento = '';
        
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
        
        // Função para mostrar os resultados do rastreamento
        function mostrarResultadoRastreamento(response) {
            // Garantir que o loader esteja oculto
            $('#rastreamento-loader').hide().css('display', 'none !important');
            
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
            
            // Fazer a mensagem de sucesso desaparecer após 3 segundos
            setTimeout(function() {
                $('#rastreamento-success').fadeOut('slow');
            }, 3000);
        }
        
        // Função para preencher a timeline
        function preencherTimeline(eventos) {
            const timeline = $('#rastreamento-timeline');
            timeline.empty();
            
            if (!eventos || eventos.length === 0) {
                timeline.append('<li class="text-center text-muted p-4">Nenhum evento de rastreamento disponível.</li>');
                return;
            }
            
            // Adicionar contador de dias aos eventos
            let ultimaData = null;
            let diaCorrente = null;
            
            // Criar elemento para cada evento
            $.each(eventos, function(index, evento) {
                // Determinar ícone e cor com base no tipo de evento
                let icone = 'box';
                let corClasse = 'bg-secondary';
                
                // Status personalizado com base no código ou descrição do evento
                if (evento.status) {
                    const status = evento.status.toLowerCase();
                    
                    if (status.includes('entreg') || status.includes('ready for recipient') || evento.codigo === 'DL') {
                        icone = 'check-circle';
                        corClasse = 'bg-success';
                    } else if (status.includes('rota') || status.includes('vehicle') || status.includes('em trânsito') || status.includes('transit')) {
                        icone = 'truck';
                        corClasse = 'bg-primary';
                    } else if (status.includes('chegada') || status.includes('chegou') || status.includes('arrived')) {
                        icone = 'plane-arrival';
                        corClasse = 'bg-info';
                    } else if (status.includes('saída') || status.includes('saiu do') || status.includes('departed')) {
                        icone = 'plane-departure';
                        corClasse = 'bg-primary';
                    } else if (status.includes('atraso') || status.includes('exceção') || status.includes('problema')) {
                        icone = 'exclamation-triangle';
                        corClasse = 'bg-warning';
                    } else if (status.includes('alfândega') || status.includes('customs')) {
                        icone = 'clipboard-check';
                        corClasse = 'bg-info';
                    } else if (status.includes('hold')) {
                        icone = 'hand-paper';
                        corClasse = 'bg-warning';
                    } else if (status.includes('picked up')) {
                        icone = 'box-open';
                        corClasse = 'bg-success';
                    } else if (status.includes('information') || status.includes('option requested')) {
                        icone = 'info-circle';
                        corClasse = 'bg-info';
                    } else if (status.includes('at local') || status.includes('facility')) {
                        icone = 'warehouse';
                        corClasse = 'bg-secondary';
                    } else if (status.includes('shipment')) {
                        icone = 'shipping-fast';
                        corClasse = 'bg-primary';
                    }
                }
                
                // Verificar se é o primeiro evento (mais recente) para adicionar classe de animação
                const pulseClass = index === 0 ? 'pulse' : '';
                
                // Formatação da data/hora para melhor legibilidade
                const dataHoraFormatada = formatarDataHora(evento.data, evento.hora);
                
                // Verificar se a data mudou para agrupar eventos por dia
                const dataEvento = evento.data;
                let mostrarSeparadorDia = false;
                
                if (dataEvento && (!diaCorrente || dataEvento !== diaCorrente)) {
                    diaCorrente = dataEvento;
                    mostrarSeparadorDia = true;
                    
                    // Calcular diferença de dias se houver uma data anterior
                    if (ultimaData) {
                        const d1 = new Date(ultimaData.split('-').join('/'));
                        const d2 = new Date(dataEvento.split('-').join('/'));
                        const diffTime = Math.abs(d2 - d1);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        
                        if (diffDays > 1) {
                            timeline.append(`
                                <li class="timeline-divider text-center my-4">
                                    <span class="badge bg-light text-dark py-1 px-3 small">
                                        <i class="fas fa-calendar-day me-1"></i> 
                                        ${diffDays} dias de diferença
                                    </span>
                                </li>
                            `);
                        }
                    }
                    
                    ultimaData = dataEvento;
                    
                    // Adicionar separador de dia
                    const dataParts = dataEvento.split('-');
                    const dataFormatada = `${dataParts[2]}/${dataParts[1]}/${dataParts[0]}`;
                    
                    timeline.append(`
                        <li class="timeline-date text-center my-3">
                            <span class="badge bg-light text-dark py-2 px-3">
                                <i class="fas fa-calendar-alt me-1"></i> 
                                ${dataFormatada}
                            </span>
                        </li>
                    `);
                }
                
                // Criar elemento de timeline com design aprimorado
                const timelineItem = $(`
                    <li class="timeline-item">
                        <div class="timeline-badge ${corClasse} ${pulseClass}"><i class="fas fa-${icone}"></i></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading d-flex justify-content-between align-items-start">
                                <div class="me-2">
                                    <h6 class="timeline-title mb-2">${evento.status || 'Evento'}</h6>
                                    <p class="mb-0 timeline-date-time"><small class="text-muted"><i class="fas fa-clock"></i> ${dataHoraFormatada}</small></p>
                                </div>
                                <span class="badge ${corClasse === 'bg-success' ? 'bg-success' : corClasse === 'bg-warning' ? 'bg-warning' : corClasse === 'bg-info' ? 'bg-info' : 'bg-primary'} rounded-pill ms-2 d-none d-md-inline-block" style="font-size: 0.7rem; font-weight: 400; padding: 0.35em 0.65em; opacity: 0.85;">${index === 0 ? 'Último evento' : ''}</span>
                            </div>
                            <div class="timeline-body mt-3">
                                ${evento.descricao ? `<p class="mb-2">${evento.descricao}</p>` : ''}
                                ${evento.local ? `<p class="mb-0 d-flex align-items-center"><i class="fas fa-map-marker-alt me-2 text-muted"></i><span class="small text-muted">${evento.local}</span></p>` : ''}
                            </div>
                        </div>
                    </li>
                `);
                
                timeline.append(timelineItem);
            });
        }
        
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
                        // Esconder loader e mostrar mensagem de sucesso
                        $('#rastreamento-loader').hide().css('display', 'none !important');
                        $('#rastreamento-success').show();
                        
                        // Preencher dados do resultado
                        mostrarResultadoRastreamento(response);
                    } else {
                        // Esconder loader e mensagem de sucesso
                        $('#rastreamento-loader').hide();
                        $('#rastreamento-success').hide();
                        
                        // Verificar se é o código especial
                        if (codigoRastreamento === '794616896420') {
                            console.log('Código de rastreamento especial detectado: ' + codigoRastreamento);
                            // Tentar forçar simulação automaticamente para esse código
                            enviarSolicitacaoRastreamento(true);
                            return;
                        }
                        
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
                    // Esconder loader e mensagem de sucesso
                    $('#rastreamento-loader').hide();
                    $('#rastreamento-success').hide();
                    
                    let errorMsg = 'Erro ao processar a solicitação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#rastreamento-error').show();
                    $('#rastreamento-error-message').text(errorMsg);
                },
                complete: function() {
                    // Esconder o loader como garantia adicional
                    $('#rastreamento-loader').hide().css('display', 'none !important');
                }
            });
        }
        
        // Função para obter parâmetros da URL
        function getParameterByName(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
        
        // Verificar se existe o parâmetro hash na URL
        const hashParam = getParameterByName('hash');
        if (hashParam) {
            console.log('Hash de rastreamento detectado na URL:', hashParam);
            // Preencher o campo com o hash e disparar a busca automaticamente
            $('#codigo_rastreamento').val(hashParam);
            codigoRastreamento = hashParam;
            
            // Mostrar loader e esconder resultados anteriores
            $('#rastreamento-error').hide();
            $('#rastreamento-success').hide();
            $('#rastreamento-loader').show();
            $('#rastreamento-resultado').hide();
            
            // Enviar solicitação AJAX para a API
            setTimeout(() => {
                enviarSolicitacaoRastreamento(false);
            }, 500); // Pequeno delay para garantir que os elementos estejam prontos
        }
        
        // Processar formulário de rastreamento via AJAX
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            // Armazenar o código de rastreamento
            codigoRastreamento = $('#codigo_rastreamento').val().trim();
            
            // Esconder mensagens de erro e sucesso anteriores
            $('#rastreamento-error').hide();
            $('#rastreamento-success').hide();
            
            // Mostrar loader e esconder resultados anteriores
            $('#rastreamento-loader').show();
            $('#rastreamento-resultado').hide();
            
            // Enviar solicitação AJAX para a API
            enviarSolicitacaoRastreamento(false);
        });
        
        // Botão para usar simulação
        $('#btn-usar-simulacao').on('click', function() {
            // Fechar o modal
            const simulacaoModal = bootstrap.Modal.getInstance(document.getElementById('simulacaoModal'));
            simulacaoModal.hide();
            
            // Esconder mensagens de erro e sucesso
            $('#rastreamento-error').hide();
            $('#rastreamento-success').hide();
            
            // Mostrar loader novamente
            $('#rastreamento-loader').show();
            
            // Enviar solicitação com flag para forçar simulação
            enviarSolicitacaoRastreamento(true);
        });
        
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
    });
</script>

<!-- Script para mostrar a seção de logs e renderizar os logs JavaScript -->
<script>
// Variáveis para armazenar logs
var trackingLogs = [];

// Função para adicionar log ao array e ao console
function addLog(type, message, data) {
    const logEntry = {
        type: type,
        message: message,
        data: data,
        timestamp: new Date().toISOString()
    };
    
    trackingLogs.push(logEntry);
    
    // Log no console do navegador
    if (type === 'error') {
        console.error(message, data);
    } else if (type === 'warn') {
        console.warn(message, data);
    } else {
        console.log(message, data);
    }
    
    // Atualizar a exibição de logs
    updateLogsDisplay();
}

// Função para atualizar a exibição de logs
function updateLogsDisplay() {
    const logsContainer = document.getElementById('logs-content');
    if (!logsContainer) return;
    
    // Limpar o conteúdo anterior
    logsContainer.innerHTML = '';
    
    // Adicionar cada log ao container
    trackingLogs.forEach(log => {
        const logElement = document.createElement('div');
        logElement.className = 'mb-2 pb-2 border-b border-gray-700';
        
        const header = document.createElement('div');
        header.className = `flex items-center ${log.type === 'error' ? 'text-red-500' : (log.type === 'warn' ? 'text-yellow-500' : 'text-green-400')}`;
        
        const timestamp = document.createElement('span');
        timestamp.className = 'text-xs text-gray-500 mr-2';
        timestamp.textContent = new Date(log.timestamp).toLocaleTimeString();
        
        const typeLabel = document.createElement('span');
        typeLabel.className = 'text-xs font-bold mr-2';
        typeLabel.textContent = log.type.toUpperCase();
        
        const message = document.createElement('span');
        message.textContent = log.message;
        
        header.appendChild(timestamp);
        header.appendChild(typeLabel);
        header.appendChild(message);
        logElement.appendChild(header);
        
        if (log.data) {
            const dataContainer = document.createElement('pre');
            dataContainer.className = 'mt-1 text-xs text-white bg-gray-800 p-2 rounded overflow-auto';
            dataContainer.style.maxHeight = '100px';
            dataContainer.textContent = typeof log.data === 'object' ? JSON.stringify(log.data, null, 2) : log.data;
            logElement.appendChild(dataContainer);
        }
        
        logsContainer.appendChild(logElement);
    });
}

// Função para mostrar/ocultar a seção de logs
document.getElementById('toggle-logs').addEventListener('click', function() {
    const logsContainer = document.getElementById('logs-container');
    const isHidden = logsContainer.classList.contains('hidden');
    
    if (isHidden) {
        logsContainer.classList.remove('hidden');
        this.textContent = 'Ocultar';
    } else {
        logsContainer.classList.add('hidden');
        this.textContent = 'Mostrar';
    }
});

// Sobrescrever a função original de enviar solicitação AJAX para adicionar logs
const originalEnviarSolicitacao = enviarSolicitacaoRastreamento;
enviarSolicitacaoRastreamento = function(forcarSimulacao) {
    addLog('info', 'Iniciando solicitação de rastreamento', { 
        codigo: codigoRastreamento,
        forcarSimulacao: forcarSimulacao 
    });
    
    // Sobrescrever método Ajax para logar
    const originalAjax = $.ajax;
    $.ajax = function(settings) {
        addLog('info', 'Enviando requisição AJAX', {
            url: settings.url,
            method: settings.type,
            data: settings.data
        });
        
        // Armazenar os callbacks originais
        const originalSuccess = settings.success;
        const originalError = settings.error;
        
        // Sobrescrever callbacks
        settings.success = function(response) {
            addLog('info', 'Resposta AJAX recebida com sucesso', response);
            if (originalSuccess) originalSuccess(response);
        };
        
        settings.error = function(xhr, status, error) {
            addLog('error', 'Erro na requisição AJAX', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            if (originalError) originalError(xhr, status, error);
        };
        
        return originalAjax.apply($, arguments);
    };
    
    // Chamar função original
    const result = originalEnviarSolicitacao(forcarSimulacao);
    
    // Restaurar método Ajax original
    $.ajax = originalAjax;
    
    return result;
};

// Script para verificar ambiente e perfil do usuário
var appEnvironment = "{{ app()->environment() }}";
var isUserAdmin = {{ auth()->check() && auth()->user()->is_admin ? 'true' : 'false' }};

// Verificar se estamos em ambiente de desenvolvimento ou se o usuário é admin
if (appEnvironment === "local" || isUserAdmin) {
    document.getElementById('debug-logs-section').classList.remove('hidden');
    addLog('info', 'Modo de depuração ativado', { 
        ambiente: appEnvironment, 
        admin: isUserAdmin 
    });
}
</script> 