@extends('layouts.app')

{{-- 
    MELHORIAS IMPLEMENTADAS NA TELA DE RASTREAMENTO:
    
    1. TIMELINE MODERNA E ELEGANTE:
       - Animações suaves de entrada com delay progressivo
       - Efeitos de hover com partículas e brilho
       - Cards com gradientes e sombras dinâmicas
       - Badges coloridos com animações de pulso
       - Efeito de destaque no primeiro item (mais recente)
    
    2. LOADER MODERNO:
       - Spinner com 3 anéis concêntricos animados
       - Barra de progresso com gradiente
       - Texto com animação de pontos
       - Efeitos de brilho e shimmer
    
    3. FORMULÁRIO INTERATIVO:
       - Campo de input com efeitos de foco
       - Botão com estado de loading
       - Animações de entrada suaves
       - Efeitos de hover no header
    
    4. ANIMAÇÕES E EFEITOS:
       - Confete para entregas realizadas
       - Efeitos de shake para atrasos
       - Glow effects para status de sucesso
       - Transições suaves em todos os elementos
       - Scroll automático para resultados
    
    5. RESPONSIVIDADE:
       - Layout adaptativo para mobile
       - Tamanhos otimizados para telas pequenas
       - Animações reduzidas em dispositivos móveis
    
    6. ACESSIBILIDADE:
       - Suporte a prefers-reduced-motion
       - Contraste melhorado
       - Focus states visíveis
       - Navegação por teclado
    
    7. PERFORMANCE:
       - will-change para otimização de animações
       - Transições CSS otimizadas
       - Lazy loading de efeitos
    
    TODAS AS FUNCIONALIDADES ORIGINAIS FORAM MANTIDAS!
--}}

@section('content')
<div class="card shadow-lg">
    <div class="card-header text-white" style="background-color: #63499E; padding: 20px 40px;">
        <i class="fas fa-map-marker-alt me-2" style="font-size: 1.2rem; opacity: 1; transform: translateX(-20px);"></i> Rastreamento de Envio FedEx
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
        
        <div class="d-flex justify-content-center mt-4" id="rastreamento-loader" style="display: none;">
            <div class="loading-container">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <div class="loading-text">
                    <span class="loading-dots">Consultando informações de rastreamento</span>
                    <div class="loading-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>
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
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }
    
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
        margin: 0;
    }
    
    .timeline:before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 25px;
        width: 4px;
        background: linear-gradient(to bottom, 
            rgba(0,123,255,0.4), 
            rgba(99, 73, 158, 0.4), 
            rgba(40, 167, 69, 0.4),
            rgba(255, 193, 7, 0.4),
            rgba(23, 162, 184, 0.4));
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        animation: timelineGlow 3s ease-in-out infinite alternate;
    }
    
    @keyframes timelineGlow {
        0% { box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        100% { box-shadow: 0 0 20px rgba(99, 73, 158, 0.3); }
    }
    
    .timeline-item {
        position: relative;
        padding-left: 60px;
        margin-bottom: 35px;
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.6s ease-out forwards;
    }
    
    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .timeline-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        text-align: center;
        line-height: 50px;
        color: white;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 2;
        border: 3px solid white;
    }
    
    .timeline-item:hover .timeline-badge {
        transform: scale(1.15) rotate(5deg);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
    }
    
    .timeline-badge i {
        font-size: 1.2rem;
        line-height: inherit;
        transition: all 0.3s ease;
    }
    
    .timeline-item:hover .timeline-badge i {
        transform: scale(1.1);
    }
    
    .timeline-panel {
        padding: 25px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-left: 5px solid transparent;
        position: relative;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .timeline-panel:before {
        content: "";
        position: absolute;
        top: 25px;
        left: -12px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 12px solid #ffffff;
        z-index: 1;
        filter: drop-shadow(-2px 0 3px rgba(0, 0, 0, 0.1));
    }
    
    .timeline-item:hover .timeline-panel {
        transform: translateY(-8px) translateX(5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);
    }
    
    /* Cores personalizadas por tipo de evento com gradientes */
    .timeline-item .bg-success {
        background: linear-gradient(135deg, #28a745, #20c997, #17a2b8);
        animation: successPulse 2s ease-in-out infinite;
    }
    
    .timeline-item .bg-primary {
        background: linear-gradient(135deg, #007bff, #1e88e5, #3f51b5);
    }
    
    .timeline-item .bg-info {
        background: linear-gradient(135deg, #17a2b8, #00b8d4, #00acc1);
    }
    
    .timeline-item .bg-warning {
        background: linear-gradient(135deg, #ffc107, #ffb300, #ff8f00);
    }
    
    .timeline-item .bg-secondary {
        background: linear-gradient(135deg, #6c757d, #546e7a, #455a64);
    }
    
    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    /* Bordas laterais coloridas por tipo de evento */
    .timeline-item:has(.bg-success) .timeline-panel {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #ffffff 0%, rgba(40, 167, 69, 0.05) 100%);
    }
    
    .timeline-item:has(.bg-primary) .timeline-panel {
        border-left-color: #007bff;
        background: linear-gradient(135deg, #ffffff 0%, rgba(0, 123, 255, 0.05) 100%);
    }
    
    .timeline-item:has(.bg-info) .timeline-panel {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #ffffff 0%, rgba(23, 162, 184, 0.05) 100%);
    }
    
    .timeline-item:has(.bg-warning) .timeline-panel {
        border-left-color: #ffc107;
        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 193, 7, 0.05) 100%);
    }
    
    .timeline-item:has(.bg-secondary) .timeline-panel {
        border-left-color: #6c757d;
        background: linear-gradient(135deg, #ffffff 0%, rgba(108, 117, 125, 0.05) 100%);
    }
    
    /* Destaque especial para o último evento */
    .timeline-item:first-child .timeline-panel {
        background: linear-gradient(135deg, #ffffff 0%, rgba(99, 73, 158, 0.08) 100%);
        border-width: 5px;
        box-shadow: 0 12px 35px rgba(99, 73, 158, 0.15);
        position: relative;
        overflow: hidden;
    }
    
    .timeline-item:first-child .timeline-panel::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, transparent 0%, rgba(99, 73, 158, 0.1) 100%);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }
    
    .timeline-item:first-child .timeline-badge {
        width: 60px;
        height: 60px;
        line-height: 60px;
        left: -5px;
        box-shadow: 0 12px 35px rgba(99, 73, 158, 0.3);
        animation: firstItemPulse 3s ease-in-out infinite;
    }
    
    @keyframes firstItemPulse {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 12px 35px rgba(99, 73, 158, 0.3);
        }
        50% { 
            transform: scale(1.1);
            box-shadow: 0 15px 40px rgba(99, 73, 158, 0.4);
        }
    }
    
    .timeline-item:first-child .timeline-badge i {
        font-size: 1.5rem;
    }
    
    .timeline-title {
        margin-top: 0;
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        line-height: 1.3;
    }
    
    /* Melhoria no layout das datas e localidades */
    .timeline-body .text-muted {
        color: #6c757d !important;
    }
    
    .timeline-heading .text-muted {
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.02) 100%);
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .timeline-heading .text-muted i {
        color: #6c757d;
        margin-right: 6px;
        font-size: 0.8rem;
    }
    
    .timeline-date-time {
        display: inline-block;
    }
    
    .timeline-date-time .text-muted {
        font-weight: 600;
        border-left: 3px solid rgba(108, 117, 125, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .timeline-date-time .text-muted::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .timeline-item:hover .timeline-date-time .text-muted::before {
        left: 100%;
    }
    
    .timeline-item:hover .timeline-date-time .text-muted {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0.04) 100%);
        border-left-color: rgba(108, 117, 125, 0.6);
        transform: translateX(3px);
    }
    
    /* Destaque especial para o último evento */
    .timeline-item:first-child .timeline-date-time .text-muted {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%);
        border-left-color: rgba(0, 123, 255, 0.5);
        color: #007bff !important;
    }
    
    .timeline-badge.pulse {
        animation: enhancedPulse 2s infinite;
    }
    
    @keyframes enhancedPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
    }
    
    /* Estilos para o card principal */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .card-header.bg-dark {
        background: linear-gradient(135deg, #343a40, #212529) !important;
        padding: 20px;
    }
    
    .card-body {
        padding: 30px;
    }
    
    /* Estilos para o formulário */
    .form-control-lg {
        border-radius: 12px 0 0 12px;
        border: 2px solid #e9ecef;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        font-size: 1.1rem;
        padding: 15px 20px;
    }
    
    .form-control-lg:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.3rem rgba(0, 123, 255, 0.15);
        transform: translateY(-2px);
    }
    
    .input-focused .form-control-lg {
        border-color: #63499E;
        box-shadow: 0 0 0 0.3rem rgba(99, 73, 158, 0.15);
        transform: translateY(-3px);
    }
    
    .has-content {
        background: linear-gradient(135deg, #ffffff 0%, rgba(99, 73, 158, 0.02) 100%);
    }
    
    .input-group {
        transition: all 0.3s ease;
    }
    
    .input-focused {
        transform: scale(1.02);
    }
    
    .btn-primary {
        border-radius: 0 12px 12px 0;
        padding: 15px 30px;
        font-weight: 600;
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    }
    
    .loading-btn {
        background: linear-gradient(135deg, #6c757d, #495057) !important;
        transform: scale(0.95);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        cursor: not-allowed;
    }
    
    .loading-btn:hover {
        transform: scale(0.95) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Estilos para o card de resultado */
    #rastreamento-resultado .card {
        border-radius: 15px;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    #rastreamento-resultado .card-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    #rastreamento-resultado h5 {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.3rem;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
        padding: 8px 16px;
        font-weight: 600;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    /* Estilos para o status atual */
    .status-atual {
        border-radius: 15px;
        padding: 25px !important;
        transition: all 0.4s ease;
        background: linear-gradient(135deg, rgba(99, 73, 158, 0.1) 0%, rgba(99, 73, 158, 0.05) 100%);
        border: 1px solid rgba(99, 73, 158, 0.1);
    }
    
    .status-atual:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(99, 73, 158, 0.15);
    }
    
    #status-atual-container {
        background: linear-gradient(135deg, rgba(99, 73, 158, 0.1) 0%, rgba(99, 73, 158, 0.05) 100%) !important;
        border-left: 5px solid #63499E;
        position: relative;
        overflow: hidden;
    }
    
    #status-atual-container::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, transparent 0%, rgba(99, 73, 158, 0.1) 100%);
        border-radius: 50%;
        transform: translate(20px, -20px);
    }
    
    #status-atual {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(135deg, #63499E, #8B5CF6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Status de atraso */
    #status-atraso-container {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%) !important;
        border-left: 5px solid #dc3545;
        border-radius: 15px;
        animation: warningShake 0.5s ease-in-out;
    }
    
    @keyframes warningShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    /* Estilo para os alertas */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
        font-weight: 500;
    }
    
    .alert-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.08) 100%);
        border-left: 5px solid #28a745;
        color: #155724;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.08) 100%);
        border-left: 5px solid #dc3545;
        color: #721c24;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0.08) 100%);
        border-left: 5px solid #ffc107;
        color: #856404;
    }
    
    /* Estilo para o loader */
    #rastreamento-loader {
        padding: 30px;
        border-radius: 15px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.8) 100%);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    #rastreamento-loader.hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
    
    /* Novo loader moderno */
    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    
    .loading-spinner {
        position: relative;
        width: 80px;
        height: 80px;
    }
    
    .spinner-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border: 3px solid transparent;
        border-radius: 50%;
        animation: spin 2s linear infinite;
    }
    
    .spinner-ring:nth-child(1) {
        border-top-color: #63499E;
        animation-delay: 0s;
    }
    
    .spinner-ring:nth-child(2) {
        border-right-color: #007bff;
        animation-delay: 0.3s;
        width: 60px;
        height: 60px;
        top: 10px;
        left: 10px;
    }
    
    .spinner-ring:nth-child(3) {
        border-bottom-color: #28a745;
        animation-delay: 0.6s;
        width: 40px;
        height: 40px;
        top: 20px;
        left: 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        text-align: center;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .loading-dots {
        display: inline-block;
        position: relative;
    }
    
    .loading-dots::after {
        content: '';
        animation: dots 1.5s infinite;
    }
    
    @keyframes dots {
        0%, 20% { content: ''; }
        40% { content: '.'; }
        60% { content: '..'; }
        80%, 100% { content: '...'; }
    }
    
    .loading-progress {
        width: 200px;
        height: 4px;
        background: rgba(99, 73, 158, 0.1);
        border-radius: 2px;
        margin-top: 15px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #63499E, #007bff, #28a745);
        border-radius: 2px;
        animation: progress 2s ease-in-out infinite;
        position: relative;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes progress {
        0% { width: 0%; }
        50% { width: 70%; }
        100% { width: 100%; }
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Botão de solicitar comprovante */
    #btn-solicitar-comprovante {
        border-radius: 25px;
        transition: all 0.4s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        padding: 10px 20px;
        font-weight: 600;
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        color: white;
    }
    
    #btn-solicitar-comprovante:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        background: linear-gradient(135deg, #0069d9, #004494);
    }
    
    /* Melhorias para as informações do pacote */
    #rastreamento-resultado p strong {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .info-grupo {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .info-grupo:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Título do Histórico de Rastreamento */
    #rastreamento-resultado h5.mb-3 {
        font-weight: 700;
        color: #2c3e50;
        margin-top: 40px;
        padding-bottom: 15px;
        border-bottom: 3px solid rgba(99, 73, 158, 0.2);
        position: relative;
        font-size: 1.4rem;
    }
    
    #rastreamento-resultado h5.mb-3::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(135deg, #63499E, #8B5CF6);
        border-radius: 2px;
    }
    
    @media (max-width: 767px) {
        .timeline:before {
            left: 35px;
        }
        
        .timeline-badge {
            left: 35px !important;
        }
        
        .timeline-panel {
            width: calc(100% - 70px) !important;
            margin-left: 70px !important;
        }
        
        .timeline-item {
            padding-left: 0 !important;
        }
        
        .card-body {
            padding: 25px 20px;
        }
        
        .timeline-panel {
            padding: 20px;
        }
    }
    
    /* Estilos para os modais */
    .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px 25px;
    }
    
    .modal-header .modal-title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.3rem;
    }
    
    .modal-body {
        padding: 30px;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px 25px;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: 600;
        padding: 10px 25px;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268, #3d4246);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    #btn-usar-simulacao {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: 600;
        padding: 12px 30px;
    }
    
    #btn-usar-simulacao:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    }
    
    #btn-download-comprovante {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 25px;
        transition: all 0.3s ease;
        font-weight: 600;
        padding: 12px 30px;
    }
    
    #btn-download-comprovante:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    }
    
    /* Loader nos modais */
    #comprovante-loader {
        padding: 30px 0;
    }
    
    .spinner-border {
        width: 2.5rem;
        height: 2.5rem;
        border-width: 0.25em;
    }
    
    .ratio-16x9 {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    /* Melhorias gerais de usabilidade */
    .small.text-muted {
        color: #6c757d !important;
    }
    
    /* Animações suaves aprimoradas */
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }
    
    #rastreamento-resultado {
        animation: fadeInUp 0.8s ease-out;
    }
    
    .timeline-item {
        animation: fadeInUp 0.6s ease-out;
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
        margin: 30px 0;
    }
    
    .timeline-date .badge {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        font-weight: 600;
        padding: 12px 20px;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    
    .timeline-date .badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .timeline-divider {
        position: relative;
        z-index: 1;
        margin: 25px 0;
    }
    
    .timeline-divider .badge {
        opacity: 0.8;
        font-size: 0.8rem;
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.1), rgba(108, 117, 125, 0.05));
        border: 1px solid rgba(108, 117, 125, 0.1);
    }
    
    /* Destaque para status de eventos específicos */
    .timeline-item:has(.bg-success) .timeline-title {
        color: #28a745;
        text-shadow: 0 1px 2px rgba(40, 167, 69, 0.1);
    }
    
    .timeline-item:has(.bg-warning) .timeline-title {
        color: #e0a800;
        text-shadow: 0 1px 2px rgba(255, 193, 7, 0.1);
    }
    
    .timeline-item:has(.bg-info) .timeline-title {
        color: #138496;
        text-shadow: 0 1px 2px rgba(23, 162, 184, 0.1);
    }
    
    .timeline-item:has(.bg-primary) .timeline-title {
        color: #0069d9;
        text-shadow: 0 1px 2px rgba(0, 123, 255, 0.1);
    }
    
    /* Melhoria visual para hover nos eventos */
    .timeline-item {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .timeline-item:hover {
        z-index: 10;
    }
    
    /* Efeito de brilho nos cards */
    .timeline-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
        z-index: 1;
        pointer-events: none;
    }
    
    .timeline-item:hover .timeline-panel::before {
        left: 100%;
    }
    
    /* Melhorias na responsividade */
    @media (max-width: 576px) {
        .timeline-panel {
            padding: 15px;
        }
        
        .timeline-title {
            font-size: 1.1rem;
        }
        
        .timeline-badge {
            width: 45px;
            height: 45px;
            line-height: 45px;
        }
        
        .timeline-badge i {
            font-size: 1rem;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
        }
        
        .spinner-ring:nth-child(2) {
            width: 45px;
            height: 45px;
            top: 7.5px;
            left: 7.5px;
        }
        
        .spinner-ring:nth-child(3) {
            width: 30px;
            height: 30px;
            top: 15px;
            left: 15px;
        }
        
        .loading-progress {
            width: 150px;
        }
    }
    
    .timeline-item:first-child:hover .timeline-panel::after {
        opacity: 1;
    }
    
    .first-item-pulse {
        animation: firstItemPulse 3s ease-in-out infinite;
    }
    
    @keyframes firstItemPulse {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 12px 35px rgba(99, 73, 158, 0.3);
        }
        50% { 
            transform: scale(1.1);
            box-shadow: 0 15px 40px rgba(99, 73, 158, 0.4);
        }
    }
    
    /* Melhorias nas animações de easing */
    .timeline-item {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .timeline-panel {
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .timeline-badge {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    /* Efeito de destaque para o item ativo */
    .timeline-item.active .timeline-panel {
        background: linear-gradient(135deg, #ffffff 0%, rgba(99, 73, 158, 0.1) 100%);
        border-left-color: #63499E;
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(99, 73, 158, 0.15);
    }
    
    .timeline-item.active .timeline-badge {
        transform: scale(1.1);
        box-shadow: 0 15px 40px rgba(99, 73, 158, 0.3);
    }
    
    /* Melhorias na responsividade */
    @media (max-width: 576px) {
        .timeline-panel {
            padding: 15px;
        }
        
        .timeline-title {
            font-size: 1.1rem;
        }
        
        .timeline-badge {
            width: 45px;
            height: 45px;
            line-height: 45px;
        }
        
        .timeline-badge i {
            font-size: 1rem;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
        }
        
        .spinner-ring:nth-child(2) {
            width: 45px;
            height: 45px;
            top: 7.5px;
            left: 7.5px;
        }
        
        .spinner-ring:nth-child(3) {
            width: 30px;
            height: 30px;
            top: 15px;
            left: 15px;
        }
        
        .loading-progress {
            width: 150px;
        }
    }
    
    .card-header {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .card-header:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }
    
    .card-header i {
        transition: all 0.3s ease;
    }
    
    .card-header:hover i {
        transform: scale(1.2) rotate(5deg);
    }
    
    /* Efeito de destaque para o título */
    .card-header h5 {
        position: relative;
        overflow: hidden;
    }
    
    .card-header h5::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ffffff, transparent);
        transition: left 0.6s ease;
    }
    
    .card-header:hover h5::after {
        left: 100%;
    }
    
    /* Melhorias finais na responsividade */
    @media (max-width: 768px) {
        .card-header {
            padding: 15px 20px;
        }
        
        .card-header h5 {
            font-size: 1.1rem;
        }
        
        .card-header i {
            font-size: 1rem;
        }
        
        .form-control-lg {
            font-size: 1rem;
            padding: 12px 15px;
        }
        
        .btn-primary {
            padding: 12px 20px;
            font-size: 1rem;
        }
        
        .timeline-container {
            margin-top: 15px;
            padding: 15px 0;
        }
        
        .timeline-panel {
            padding: 15px;
        }
        
        .timeline-title {
            font-size: 1rem;
        }
        
        .info-grupo {
            padding: 15px !important;
        }
        
        .status-atual {
            padding: 20px !important;
        }
        
        #status-atual {
            font-size: 1.3rem;
        }
    }
    
    /* Efeitos de acessibilidade */
    .timeline-item:focus {
        outline: 2px solid #63499E;
        outline-offset: 2px;
    }
    
    .btn:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
    
    .form-control:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
    
    /* Melhorias na performance */
    .timeline-item {
        will-change: transform, opacity;
    }
    
    .timeline-panel {
        will-change: transform, box-shadow;
    }
    
    .timeline-badge {
        will-change: transform, box-shadow;
    }
    
    /* Efeitos de loading mais suaves */
    .loading-container {
        will-change: opacity;
    }
    
    .spinner-ring {
        will-change: transform;
    }
    
    /* Melhorias na tipografia */
    .timeline-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 0.5px;
    }
    
    .card-header h5 {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 0.8px;
        font-weight: 700;
    }
    
    #status-atual {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 1px;
        font-weight: 800;
    }
    
    /* Efeitos de profundidade */
    .timeline-panel {
        position: relative;
    }
    
    .timeline-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%, rgba(0, 0, 0, 0.02) 100%);
        border-radius: 15px;
        pointer-events: none;
        z-index: 1;
    }
    
    .timeline-panel > * {
        position: relative;
        z-index: 2;
    }
    
    /* Efeitos de hover mais suaves */
    .timeline-item {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .timeline-item:hover {
        transform: translateY(-5px);
    }
    
    .timeline-item:hover .timeline-panel {
        transform: translateY(-8px) translateX(5px);
    }
    
    /* Melhorias no contraste */
    .timeline-title {
        color: #1a1a1a;
    }
    
    .timeline-body {
        color: #333333;
    }
    
    .text-muted {
        color: #666666 !important;
    }
    
    /* Efeitos de loading mais elegantes */
    .loading-dots {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .loading-progress {
        background: rgba(99, 73, 158, 0.1);
        border: 1px solid rgba(99, 73, 158, 0.2);
    }
    
    /* Melhorias finais na experiência do usuário */
    .timeline-item {
        cursor: pointer;
        user-select: none;
    }
    
    .timeline-item:active {
        transform: scale(0.98);
    }
    
    .btn:active {
        transform: scale(0.95);
    }
    
    /* Efeitos de transição mais suaves */
    * {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    /* Melhorias na acessibilidade de cores */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
    
    /* Efeitos de destaque para elementos importantes */
    .timeline-item:first-child .timeline-badge {
        box-shadow: 0 0 20px rgba(99, 73, 158, 0.4);
    }
    
    .timeline-item:first-child .timeline-panel {
        border: 2px solid rgba(99, 73, 158, 0.1);
    }
    
    /* Melhorias na legibilidade */
    .timeline-body p {
        line-height: 1.6;
        margin-bottom: 0.5rem;
    }
    
    .timeline-body p:last-child {
        margin-bottom: 0;
    }
    
    /* Efeitos de loading mais informativos */
    .loading-text {
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    /* Melhorias na responsividade do loader */
    @media (max-width: 576px) {
        .loading-text {
            font-size: 1rem;
        }
        
        .loading-dots {
            font-size: 0.9rem;
        }
    }
</style>

<script>
// Verificar se jQuery está disponível
if (typeof jQuery === 'undefined') {
    // Aguardar o carregamento do jQuery
    window.addEventListener('load', function() {
        if (typeof jQuery !== 'undefined') {
            //console.log('jQuery carregado, inicializando script...');
            initializeRastreamentoScript();
        } else {
            //console.error('jQuery ainda não está disponível após carregamento da página');
        }
    });
} else {
    //console.log('jQuery já está disponível, inicializando script...');
    initializeRastreamentoScript();
}

function initializeRastreamentoScript() {
    $(document).ready(function() {
        // GARANTIR QUE O LOADER ESTÁ OCULTO DESDE O INÍCIO
        ocultarLoader();
        
        // Timeout adicional para garantir que o loader esteja oculto
        setTimeout(function() {
            ocultarLoader();
        }, 100);
        
        // Variáveis globais para armazenar o código de rastreamento
        let codigoRastreamento = '';
        
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
                //console.error(message, data);
            } else if (type === 'warn') {
                //console.warn(message, data);
            } else {
                //console.log(message, data);
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

        // Função para mostrar/ocultar a seção de logs - apenas se o elemento existir
        const toggleLogsElement = document.getElementById('toggle-logs');
        if (toggleLogsElement) {
            toggleLogsElement.addEventListener('click', function() {
                const logsContainer = document.getElementById('logs-container');
                if (logsContainer) {
                    const isHidden = logsContainer.classList.contains('hidden');
                    
                    if (isHidden) {
                        logsContainer.classList.remove('hidden');
                        this.textContent = 'Ocultar';
                    } else {
                        logsContainer.classList.add('hidden');
                        this.textContent = 'Mostrar';
                    }
                }
            });
        }

        // Script para verificar ambiente e perfil do usuário
        var appEnvironment = "{{ app()->environment() }}";
        var isUserAdmin = "{{ auth()->check() && auth()->user()->is_admin ? 'true' : 'false' }}";

        // Verificar se estamos em ambiente de desenvolvimento ou se o usuário é admin
        if (appEnvironment === "local" || isUserAdmin === "true") {
            const debugSection = document.getElementById('debug-logs-section');
            if (debugSection) {
                debugSection.classList.remove('hidden');
                addLog('info', 'Modo de depuração ativado', { 
                    ambiente: appEnvironment, 
                    admin: isUserAdmin 
                });
            }
        }
        
        // Função para ocultar o loader de forma robusta
        function ocultarLoader() {
            const loader = document.getElementById('rastreamento-loader');
            if (loader) {
                loader.style.display = 'none';
                loader.style.visibility = 'hidden';
                loader.style.opacity = '0';
                loader.classList.add('hidden');
                $(loader).hide();
            }
        }
        
        // Função para mostrar o loader
        function mostrarLoader() {
            const loader = document.getElementById('rastreamento-loader');
            if (loader) {
                loader.style.display = 'flex';
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
                loader.classList.remove('hidden');
                $(loader).show();
            }
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
        
        // Função para mostrar os resultados do rastreamento
        function mostrarResultadoRastreamento(response) {
            
            // Preencher dados do resultado com animações
            $('#rastreamento-codigo').text(response.codigo).hide().fadeIn(800);
            $('#origem-envio').text(response.origem || '-').hide().fadeIn(800);
            $('#destino-envio').text(response.destino || '-').hide().fadeIn(800);
            $('#data-postagem').text(formatarData(response.dataPostagem) || '-').hide().fadeIn(800);
            $('#entrega-prevista').text(formatarData(response.dataEntregaPrevista) || '-').hide().fadeIn(800);
            $('#status-atual').text(response.status || '-').hide().fadeIn(800);
            $('#rastreamento-servico').text(response.servicoDescricao || 'FedEx').hide().fadeIn(800);
            
            // Atualizar a última atualização
            if (response.ultimaAtualizacao) {
                $('#status-atualizacao').text('Atualizado em: ' + formatarDataHora(response.ultimaAtualizacao)).hide().fadeIn(800);
            } else {
                $('#status-atualizacao').text('');
            }
            
            // Verificar se há atraso com animação
            if (response.temAtraso) {
                $('#status-atraso-container').hide().fadeIn(1000).addClass('shake-animation');
                $('#status-atraso-detalhes').text(response.detalhesAtraso || 'Há um atraso na entrega');
                
                // Remover classe de animação após 2 segundos
                setTimeout(() => {
                    $('#status-atraso-container').removeClass('shake-animation');
                }, 2000);
            } else {
                $('#status-atraso-container').hide();
            }
            
            // Verificar se está entregue para mudar a cor do status com animação
            if (response.entregue) {
                $('#status-atual-container')
                    .css('background-color', 'rgba(40, 167, 69, 0.1)')
                    .addClass('success-glow');
                $('#status-atual')
                    .css('color', '#28a745')
                    .addClass('success-text-glow');
                
                // Exibir botão de solicitar comprovante com animação
                $('#btn-solicitar-comprovante').hide().fadeIn(1000).addClass('bounce-in');
                
                // Adicionar confete para entrega
                createConfettiEffect();
            } else {
                $('#status-atual-container')
                    .css('background-color', 'rgba(99, 73, 158, 0.1)')
                    .removeClass('success-glow');
                $('#status-atual')
                    .css('color', '#63499E')
                    .removeClass('success-text-glow');
                $('#btn-solicitar-comprovante').hide();
            }
            
            // Limpar e preencher a timeline
            preencherTimeline(response.eventos);
            
            // Verificar se é simulado
            if (response.simulado) {
                $('#rastreamento-simulado-alert').hide().fadeIn(800);
                $('#rastreamento-simulado-message').text(response.mensagem || 'Atenção: Estes dados são simulados para demonstração.');
            } else {
                $('#rastreamento-simulado-alert').hide();
            }
            
            // Exibir resultados com animação
            $('#rastreamento-resultado').hide().fadeIn(1000);
            
            // Scroll suave para os resultados
            $('html, body').animate({
                scrollTop: $('#rastreamento-resultado').offset().top - 100
            }, 800, 'swing');
            
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
                    <li class="timeline-item" data-index="${index}">
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
                
                // Adicionar efeitos interativos após inserir o elemento
                setTimeout(function() {
                    const item = timeline.find(`[data-index="${index}"]`);
                    
                    // Efeito de entrada com delay progressivo e animação mais suave
                    item.css({
                        'opacity': '0',
                        'transform': 'translateX(-50px) scale(0.8)'
                    }).animate({
                        'opacity': '1',
                        'transform': 'translateX(0) scale(1)'
                    }, {
                        duration: 800,
                        easing: 'swing',
                        delay: index * 150
                    });
                    
                    // Adicionar efeito de hover com som
                    item.on('mouseenter', function() {
                        $(this).find('.timeline-badge').addClass('hover-effect');
                        $(this).find('.timeline-panel').addClass('hover-glow');
                        
                        // Efeito de partículas para o primeiro item
                        if (index === 0) {
                            createParticleEffect($(this));
                        }
                        
                        // Adicionar efeito de sombra dinâmica
                        $(this).find('.timeline-panel').css({
                            'box-shadow': '0 20px 50px rgba(99, 73, 158, 0.2)'
                        });
                    }).on('mouseleave', function() {
                        $(this).find('.timeline-badge').removeClass('hover-effect');
                        $(this).find('.timeline-panel').removeClass('hover-glow');
                        
                        // Remover efeito de sombra dinâmica
                        $(this).find('.timeline-panel').css({
                            'box-shadow': ''
                        });
                    });
                    
                    // Efeito de clique para expandir detalhes
                    item.on('click', function() {
                        const panel = $(this).find('.timeline-panel');
                        
                        // Remover destaque de todos os outros itens
                        timeline.find('.timeline-item').removeClass('active');
                        
                        // Adicionar destaque ao item clicado
                        $(this).addClass('active');
                        
                        // Scroll suave para centralizar o item
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - 150
                        }, 600, 'swing');
                        
                        panel.toggleClass('expanded');
                        
                        if (panel.hasClass('expanded')) {
                            panel.find('.timeline-body').slideDown(400);
                            panel.css({
                                'transform': 'scale(1.03) translateY(-5px)',
                                'box-shadow': '0 25px 60px rgba(0, 0, 0, 0.2)'
                            });
                        } else {
                            panel.find('.timeline-body').slideUp(400);
                            panel.css({
                                'transform': 'scale(1) translateY(0)',
                                'box-shadow': ''
                            });
                            
                            // Remover destaque se não estiver expandido
                            setTimeout(() => {
                                if (!panel.hasClass('expanded')) {
                                    $(this).removeClass('active');
                                }
                            }, 400);
                        }
                    });
                    
                    // Adicionar efeito de pulso para o primeiro item
                    if (index === 0) {
                        item.find('.timeline-badge').addClass('first-item-pulse');
                    }
                    
                }, 100);
            });
            
            // Adicionar efeito de scroll suave para a timeline
            $('html, body').animate({
                scrollTop: timeline.offset().top - 100
            }, 800, 'swing');
            
            // Destacar automaticamente o primeiro item após 1 segundo
            setTimeout(() => {
                const firstItem = timeline.find('.timeline-item').first();
                if (firstItem.length) {
                    firstItem.addClass('active');
                    
                    // Remover destaque após 3 segundos
                    setTimeout(() => {
                        firstItem.removeClass('active');
                    }, 3000);
                }
            }, 1000);
        }
        
        // Função para criar efeito de partículas
        function createParticleEffect(element) {
            const badge = element.find('.timeline-badge');
            const badgeOffset = badge.offset();
            
            for (let i = 0; i < 5; i++) {
                const particle = $('<div class="particle"></div>');
                particle.css({
                    position: 'absolute',
                    width: '4px',
                    height: '4px',
                    background: 'radial-gradient(circle, #fff 0%, transparent 70%)',
                    borderRadius: '50%',
                    pointerEvents: 'none',
                    zIndex: 1000,
                    left: badgeOffset.left + badge.width() / 2,
                    top: badgeOffset.top + badge.height() / 2,
                    animation: `particleFloat ${1 + Math.random()}s ease-out forwards`
                });
                
                $('body').append(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 1000 + Math.random() * 500);
            }
        }
        
        // Função para criar efeito de confete
        function createConfettiEffect() {
            const colors = ['#28a745', '#007bff', '#ffc107', '#17a2b8', '#6f42c1'];
            const container = $('#rastreamento-resultado');
            const containerOffset = container.offset();
            
            for (let i = 0; i < 50; i++) {
                const confetti = $('<div class="confetti-piece"></div>');
                const color = colors[Math.floor(Math.random() * colors.length)];
                const size = Math.random() * 10 + 5;
                const left = Math.random() * container.width();
                const animationDuration = Math.random() * 3 + 2;
                
                confetti.css({
                    position: 'absolute',
                    width: size + 'px',
                    height: size + 'px',
                    background: color,
                    left: containerOffset.left + left + 'px',
                    top: containerOffset.top - 20 + 'px',
                    borderRadius: Math.random() > 0.5 ? '50%' : '0',
                    zIndex: 9999,
                    pointerEvents: 'none',
                    animation: `confettiFall ${animationDuration}s ease-in forwards`
                });
                
                $('body').append(confetti);
                
                setTimeout(() => {
                    confetti.remove();
                }, animationDuration * 1000);
            }
        }
        
        // Adicionar CSS para animações adicionais
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                @keyframes confettiFall {
                    0% {
                        transform: translateY(-20px) rotate(0deg);
                        opacity: 1;
                    }
                    100% {
                        transform: translateY(100vh) rotate(720deg);
                        opacity: 0;
                    }
                }
                
                .success-glow {
                    animation: successGlow 2s ease-in-out infinite alternate;
                }
                
                @keyframes successGlow {
                    0% { box-shadow: 0 0 20px rgba(40, 167, 69, 0.3); }
                    100% { box-shadow: 0 0 40px rgba(40, 167, 69, 0.6); }
                }
                
                .success-text-glow {
                    animation: textGlow 2s ease-in-out infinite alternate;
                }
                
                @keyframes textGlow {
                    0% { text-shadow: 0 0 5px rgba(40, 167, 69, 0.5); }
                    100% { text-shadow: 0 0 15px rgba(40, 167, 69, 0.8); }
                }
                
                .shake-animation {
                    animation: shake 0.5s ease-in-out;
                }
                
                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-5px); }
                    75% { transform: translateX(5px); }
                }
                
                .bounce-in {
                    animation: bounceIn 0.8s ease-out;
                }
                
                @keyframes bounceIn {
                    0% {
                        transform: scale(0.3);
                        opacity: 0;
                    }
                    50% {
                        transform: scale(1.05);
                    }
                    70% {
                        transform: scale(0.9);
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
                
                .info-grupo {
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                
                .info-grupo:hover {
                    transform: translateY(-5px) scale(1.02);
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
                }
                
                .info-grupo p {
                    transition: all 0.3s ease;
                }
                
                .info-grupo:hover p {
                    transform: translateX(5px);
                }
                
                .info-grupo:hover strong {
                    color: #63499E !important;
                }
                
                .timeline-container {
                    position: relative;
                    overflow: hidden;
                }
                
                .timeline-container::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
                    animation: shimmer 3s infinite;
                    z-index: 1;
                    pointer-events: none;
                }
                
                @keyframes shimmer {
                    0% { left: -100%; }
                    100% { left: 100%; }
                }
                
                .timeline-item {
                    position: relative;
                    z-index: 2;
                }
                
                .timeline-badge {
                    position: relative;
                    overflow: hidden;
                }
                
                .timeline-badge::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                    transform: rotate(45deg);
                    transition: all 0.6s ease;
                    opacity: 0;
                }
                
                .timeline-item:hover .timeline-badge::before {
                    opacity: 1;
                    animation: shine 0.6s ease;
                }
                
                @keyframes shine {
                    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
                }
                
                .form-control-lg:focus {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.2);
                }
                
                .btn-primary:active {
                    transform: translateY(-1px);
                }
                
                .card {
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                
                .card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
                }
                
                .alert {
                    transition: all 0.4s ease;
                }
                
                .alert:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                }
                
                .spinner-border {
                    animation: spinnerGlow 2s ease-in-out infinite alternate;
                }
                
                @keyframes spinnerGlow {
                    0% { box-shadow: 0 0 10px rgba(0, 123, 255, 0.3); }
                    100% { box-shadow: 0 0 20px rgba(0, 123, 255, 0.6); }
                }
                
                .timeline-title {
                    position: relative;
                    overflow: hidden;
                }
                
                .timeline-title::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: -100%;
                    width: 100%;
                    height: 2px;
                    background: linear-gradient(90deg, transparent, currentColor, transparent);
                    transition: left 0.5s ease;
                }
                
                .timeline-item:hover .timeline-title::after {
                    left: 100%;
                }
                
                .timeline-date-time .text-muted {
                    position: relative;
                    overflow: hidden;
                }
                
                .timeline-date-time .text-muted::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                    transition: left 0.6s ease;
                }
                
                .timeline-item:hover .timeline-date-time .text-muted::after {
                    left: 100%;
                }
                
                .timeline-panel {
                    position: relative;
                    overflow: hidden;
                }
                
                .timeline-panel::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 0;
                    height: 0;
                    border-style: solid;
                    border-width: 0 20px 20px 0;
                    border-color: transparent rgba(99, 73, 158, 0.1) transparent transparent;
                    transition: all 0.3s ease;
                    opacity: 0;
                }
                
                .timeline-item:hover .timeline-panel::after {
                    opacity: 1;
                }
                
                .timeline-item:first-child .timeline-panel::after {
                    border-color: transparent rgba(40, 167, 69, 0.2) transparent transparent;
                }
                
                .timeline-item:first-child:hover .timeline-panel::after {
                    opacity: 1;
                }
            `)
            .appendTo('head');

        // Função para enviar a solicitação AJAX de rastreamento
        function enviarSolicitacaoRastreamento(forcarSimulacao) {
            
            // Timeout de segurança para ocultar loader após 30 segundos
            setTimeout(function() {
                ocultarLoader();
            }, 30000);
            
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
                    
                    // OCULTAR LOADER IMEDIATAMENTE
                    ocultarLoader();
                    
                    // Restaurar botão
                    const submitBtn = $('#rastreamento-form button[type="submit"]');
                    submitBtn.html('<i class="fas fa-search me-2"></i>Rastrear')
                        .prop('disabled', false)
                        .removeClass('loading-btn');
                    
                    if (response.success) {
                        // Mostrar mensagem de sucesso
                        $('#rastreamento-success').show();
                        
                        // Preencher dados do resultado
                        mostrarResultadoRastreamento(response);
                    } else {
                        // Verificar se é o código especial
                        if (codigoRastreamento === '794616896420') {
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
                    // OCULTAR LOADER IMEDIATAMENTE
                    ocultarLoader();
                    
                    // Restaurar botão
                    const submitBtn = $('#rastreamento-form button[type="submit"]');
                    submitBtn.html('<i class="fas fa-search me-2"></i>Rastrear')
                        .prop('disabled', false)
                        .removeClass('loading-btn');
                    
                    let errorMsg = 'Erro ao processar a solicitação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#rastreamento-error').show();
                    $('#rastreamento-error-message').text(errorMsg);
                },
                complete: function() {
                    // Garantia adicional - ocultar loader novamente
                    ocultarLoader();
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
            // Preencher o campo com o hash apenas
            $('#codigo_rastreamento').val(hashParam);
            codigoRastreamento = hashParam;
            // NÃO MOSTRAR LOADER AUTOMATICAMENTE
        }
        
        // Processar formulário de rastreamento via AJAX
        $('#rastreamento-form').on('submit', function(e) {
            e.preventDefault();
            
            // Armazenar o código de rastreamento
            codigoRastreamento = $('#codigo_rastreamento').val().trim();
            
            // Adicionar efeito de loading no botão
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Rastreando...')
                .prop('disabled', true)
                .addClass('loading-btn');
            
            // LIMPAR TUDO E MOSTRAR LOADER
            $('#rastreamento-resultado').hide();
            $('#rastreamento-error').hide();
            $('#rastreamento-success').hide();
            $('#rastreamento-simulado-alert').hide();
            mostrarLoader();
            
            // Enviar solicitação AJAX para a API
            enviarSolicitacaoRastreamento(false);
            
            // Restaurar botão após 5 segundos (timeout de segurança)
            setTimeout(() => {
                submitBtn.html(originalText)
                    .prop('disabled', false)
                    .removeClass('loading-btn');
            }, 5000);
        });
        
        // Botão para usar simulação
        $('#btn-usar-simulacao').on('click', function() {
            // Fechar o modal
            const simulacaoModal = bootstrap.Modal.getInstance(document.getElementById('simulacaoModal'));
            simulacaoModal.hide();
            
            // Esconder mensagens de erro e sucesso
            // limparResultadosAnteriores(); // REMOVIDO
            
            // Mostrar loader novamente
            mostrarLoader();
            
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
        
        // Configurar menu ativo
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="rastreamento"]').addClass('active');
        $('#content-container').show();
        
        // Adicionar efeito de entrada para o card principal
        $('.card').css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        }).animate({
            'opacity': '1',
            'transform': 'translateY(0)'
        }, {
            duration: 800,
            easing: 'swing'
        });
        
        // Adicionar efeito de destaque no título
        $('.card-header h5, .card-header i').css({
            'opacity': '0',
            'transform': 'translateX(-20px)'
        }).animate({
            'opacity': '1',
            'transform': 'translateX(0)'
        }, {
            duration: 1000,
            easing: 'swing',
            delay: 300
        });
        
        // Adicionar efeitos ao campo de input
        $('#codigo_rastreamento').on('focus', function() {
            $(this).parent().addClass('input-focused');
        }).on('blur', function() {
            $(this).parent().removeClass('input-focused');
        });
        
        // Adicionar efeito de digitação
        $('#codigo_rastreamento').on('input', function() {
            const value = $(this).val();
            if (value.length > 0) {
                $(this).addClass('has-content');
            } else {
                $(this).removeClass('has-content');
            }
        });
        
        // Adicionar efeito de hover no card header
        $('.card-header').on('mouseenter', function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #63499E, #8B5CF6) !important'
            });
        }).on('mouseleave', function() {
            $(this).css({
                'background': 'linear-gradient(135deg, #343a40, #212529) !important'
            });
        });
    });
}
</script>
@endsection 