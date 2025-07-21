@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cotacao.css') }}">
<style>
    /* Padrão de roxo para toda a interface */
    :root {
        --roxo-principal: #764ba2;
        --roxo-secundario: #6f42c1;
        --roxo-claro: #a084e8;
        --roxo-escuro: #4b2c6f;
        --roxo-bg: #f5f3fa;
        --roxo-badge: #8f5fd6;
    }
    body, .page-header-wrapper {
        background: var(--roxo-bg) !important;
    }
    
    /* Header Styles - Aplicando o mesmo padrão da cotação */
    .page-header-wrapper {
        background: var(--primary-gradient) !important;
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
    
    .bg-gradient-primary, .btn-primary, .card-header.bg-gradient-primary, .progress-bar.bg-primary {
        background: var(--roxo-principal) !important;
        background-color: var(--roxo-principal) !important;
        color: #fff !important;
        border: none;
    }
    .bg-gradient-secondary, .btn-secondary, .card-header.bg-gradient-secondary {
        background: var(--roxo-secundario) !important;
        background-color: var(--roxo-secundario) !important;
        color: #fff !important;
        border: none;
    }
    .btn-primary:hover, .btn-secondary:hover {
        background: var(--roxo-escuro) !important;
        color: #fff !important;
    }
    .btn-success, .btn-success:hover {
        background: var(--roxo-claro) !important;
        color: #fff !important;
        border: none;
    }
    .btn-outline-primary {
        color: var(--roxo-principal) !important;
        border-color: var(--roxo-principal) !important;
        background: #fff !important;
    }
    .btn-outline-primary:hover {
        background: var(--roxo-principal) !important;
        color: #fff !important;
    }
    .btn-outline-secondary {
        color: var(--roxo-secundario) !important;
        border-color: var(--roxo-secundario) !important;
        background: #fff !important;
    }
    .btn-outline-secondary:hover {
        background: var(--roxo-secundario) !important;
        color: #fff !important;
    }
    .badge.bg-success, .badge.bg-info, .badge.bg-secondary {
        background: var(--roxo-badge) !important;
        color: #fff !important;
    }
    .badge.bg-primary {
        background: var(--roxo-principal) !important;
        color: #fff !important;
    }
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .card {
        background: #fff;
        border: 1px solid #e9e3f7;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(120, 90, 180, 0.06);
    }
    .input-group-text {
        border-color: #e9e3f7;
        background-color: #f5f3fa;
        color: var(--roxo-principal);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--roxo-principal);
        box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.15);
    }
    .alert-info, .alert-success, .alert-warning, .alert-danger {
        background: var(--roxo-bg) !important;
        color: var(--roxo-principal) !important;
        border: 1px solid var(--roxo-claro) !important;
    }
    .alert .fa-info-circle, .alert .fa-check-circle, .alert .fa-exclamation-triangle, .alert .fa-times-circle {
        color: var(--roxo-principal) !important;
    }
    .select2-container--default .select2-selection--single {
        border-color: #e9e3f7;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: var(--roxo-principal);
        box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.15);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--roxo-principal);
    }
    /* Responsividade melhorada */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem !important;
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .d-flex.gap-3 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        .badge.fs-6 {
            font-size: 0.875rem !important;
        }
        
        /* Responsividade do header */
        .page-header-wrapper {
            padding: 0.75rem 1rem;
        }
        
        .title-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .description {
            text-align: left;
        }
    }
    @media (max-width: 576px) {
        .col-sm-12 {
            margin-bottom: 1rem;
        }
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    }
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(120, 90, 180, 0.10) !important;
    }
    
    /* Estilos para o card informativo colapsável */
    #info-card {
        transition: all 0.3s ease;
    }
    
    #info-card-header {
        transition: all 0.3s ease;
    }
    
    #info-card-header:hover {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 0.5rem;
        margin: -0.5rem;
    }
    
    #info-card-content {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    #info-card.collapsed #info-card-content {
        display: none;
    }
    
    #info-card.collapsed #info-card-icon {
        transform: rotate(-90deg);
    }
    
    #info-card.expanded #info-card-icon {
        transform: rotate(0deg);
    }
    
    /* Animação suave para o conteúdo */
    .info-content-enter {
        max-height: 0;
        opacity: 0;
    }
    
    .info-content-enter-active {
        max-height: 500px;
        opacity: 1;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }
    
    .info-content-exit {
        max-height: 500px;
        opacity: 1;
    }
    
    .info-content-exit-active {
        max-height: 0;
        opacity: 0;
        transition: max-height 0.3s ease, opacity 0.3s ease;
    }
    
    /* Melhorias para o card colapsado */
    #info-card.collapsed {
        padding: 0.75rem 1rem;
    }
    
    #info-card.collapsed #info-card-header {
        margin: 0;
        padding: 0;
    }
    
    #info-card.expanded {
        padding: 1rem 1.5rem;
    }
    
    /* Efeito hover mais suave */
    #info-card.collapsed #info-card-header:hover {
        padding: 0.5rem;
        margin: -0.5rem;
    }
    
    /* Estilos específicos para cards de produtos */
    #produtos-cards .card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.06);
    }
    
    #produtos-cards .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    #produtos-cards .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        color: white !important;
        border: none !important;
    }
    
    #produtos-cards .btn-group {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    #produtos-cards .btn-group .btn {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    #produtos-cards .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }
    
    #produtos-cards .btn-outline-primary {
        color: var(--roxo-principal) !important;
        border-color: var(--roxo-principal) !important;
        background: white !important;
    }
    
    #produtos-cards .btn-outline-primary:hover {
        background: var(--roxo-principal) !important;
        color: white !important;
        border-color: var(--roxo-principal) !important;
    }
    
    #produtos-cards .btn-primary {
        background: var(--roxo-principal) !important;
        border-color: var(--roxo-principal) !important;
        color: white !important;
    }
    
    #produtos-cards .btn-outline-danger {
        border-color: #dc3545 !important;
        color: #dc3545 !important;
        background: white !important;
    }
    
    #produtos-cards .btn-outline-danger:hover {
        background: #dc3545 !important;
        color: white !important;
        border-color: #dc3545 !important;
    }
    
    /* Garantir que os botões de quantidade fiquem sempre dentro do card */
    #produtos-cards .btn-group {
        max-width: 120px !important;
        width: 100% !important;
    }
    
    #produtos-cards .btn-group .btn {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    #produtos-cards .btn-group .btn:not(.disabled) {
        min-width: 40px;
    }
    
    #produtos-cards .btn-group .btn.disabled {
        min-width: 50px;
        max-width: 60px;
    }
    
    /* Estilos para cards de serviço de entrega */
    .servico-option {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
    }
    
    .servico-option:hover {
        border-color: var(--roxo-principal);
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.15);
        transform: translateY(-2px);
    }
    
    .servico-option.selected {
        border-color: var(--roxo-principal);
        background-color: rgba(111, 66, 193, 0.05);
        box-shadow: 0 4px 12px rgba(111, 66, 193, 0.2);
    }
    
    .servico-option .price-main {
        font-size: 1.4rem;
        color: var(--roxo-principal);
        font-weight: 600;
    }
    
    .servico-option .card-title {
        color: var(--roxo-principal);
        font-weight: 600;
    }
    
    .servico-option .text-muted {
        font-size: 0.875rem;
    }
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="page-header-wrapper">
    <div class="page-header-content">
        <div class="header-content">
            <div class="title-section">
                <div class="title-area">
                    <i class="fas fa-shipping-fast me-2"></i>
                    <h1>Envio Internacional</h1>
                </div>
                <p class="description">Processe seu envio internacional de forma rápida e segura</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
  
    <div class="card-body">
        <form id="envio-form" action="{{ route('api.envio.processar') }}" method="POST">
            @csrf

            <!-- Wizard Progress Bar -->
            <div id="etapas-container" class="mb-4">
                <div class="progress" style="height: 30px;">
                    <div id="wizard-progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 16.6%;" aria-valuenow="1" aria-valuemin="1" aria-valuemax="6">
                        <span id="wizard-progress-label">Etapa 1 de 6</span>
                    </div>
                </div>
            </div>
            
            <!-- Balãozinho Informativo -->
            <div class="alert alert-info border-0 mb-4" role="alert" id="info-card" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-left: 4px solid var(--roxo-principal) !important;">
                <!-- Cabeçalho Colapsável -->
                <div class="d-flex align-items-center justify-content-between" id="info-card-header" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning me-2" style="font-size: 1.25rem;"></i>
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Como funciona o processo de envio
                        </h6>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2" id="info-card-badge">Etapa 1 de 6</span>
                        <i class="fas fa-chevron-down text-primary" id="info-card-icon" style="transition: transform 0.3s ease;"></i>
                    </div>
                </div>
                
                <!-- Conteúdo Colapsável -->
                <div id="info-card-content" class="mt-3">
                    <p class="mb-2 text-dark">
                        Para facilitar o processo de envio internacional, dividimos as informações em <strong>6 etapas simples</strong>:
                    </p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i><strong>Etapa 1:</strong> Tipo de envio e pessoa</li>
                                <li class="mb-1"><i class="fas fa-box text-primary me-2"></i><strong>Etapa 2:</strong> Produtos e embalagens</li>
                                <li class="mb-1"><i class="fas fa-map-marker-alt text-info me-2"></i><strong>Etapa 3:</strong> Endereços de origem e destino</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-1"><i class="fas fa-eye text-warning me-2"></i><strong>Etapa 4:</strong> Revisão dos dados</li>
                                <li class="mb-1"><i class="fas fa-shipping-fast text-secondary me-2"></i><strong>Etapa 5:</strong> Serviço de entrega</li>
                                <li class="mb-1"><i class="fas fa-credit-card text-success me-2"></i><strong>Etapa 6:</strong> Pagamento</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-white rounded border">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            <strong>Dica:</strong> Você pode navegar entre as etapas usando os botões "Continuar" e "Voltar". 
                            Todas as informações são salvas automaticamente durante o processo.
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Etapas do Wizard -->
            <div id="wizard-steps">
                <!-- Etapa 1: Tipo de Envio -->
                <div id="step-1" data-step="1">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="card border-light shadow-sm">
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-6 mb-3">
                                            <label for="tipo_envio" class="form-label required"><i class="fas fa-box me-1"></i> Tipo de Envio</label>
                                            <select class="form-select" id="tipo_envio" name="tipo_envio" required style="color: #B0B0B0 !important;">
                                                <option value="">Selecione o tipo de envio</option>
                                                <option value="venda">Venda</option>
                                                <option value="amostra">Envio de Amostras</option>
                                                <option value="pessoal">Envio Pessoal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="tipo_pessoa" class="form-label required"><i class="fas fa-user me-1"></i> Tipo de Pessoa</label>
                                            <select class="form-select" id="tipo_pessoa" name="tipo_pessoa" required style="color: #B0B0B0 !important;">
                                                <option value="">Selecione o tipo de pessoa</option>
                                                <option value="pf">Pessoa Física</option>
                                                <option value="pj">Pessoa Jurídica</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary" id="btn-step-1-next">Continuar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Etapa 2: Produtos e Caixas -->
                <div id="step-2" data-step="2" class="d-none">
                    <!-- Seção de Produtos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-primary text-white py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box-open me-3 fs-4"></i>
                                        <div>
                                            <h5 class="mb-0 fw-bold">Produtos do Envio</h5>
                                            <small class="opacity-75">Adicione os produtos que serão enviados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Busca de Produtos -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-lg-5 col-md-6">
                                            <label for="busca-descricao" class="form-label fw-semibold">
                                                <i class="fas fa-search me-1 text-primary"></i>Descrição do Produto
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="fas fa-tag text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0" id="busca-descricao" 
                                                       placeholder="Ex: Havaianas, eletrônicos, roupas...">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-3">
                                            <label for="busca-codigo" class="form-label fw-semibold">
                                                <i class="fas fa-barcode me-1 text-primary"></i>NCM
                                            </label>
                                            <input type="text" class="form-control text-center" id="busca-codigo" 
                                                   placeholder="NCM" maxlength="10">
                                        </div>
                                       <!--  <div class="col-lg-3 col-md-3">
                                            <label for="produto-select" class="form-label fw-semibold">
                                                <i class="fas fa-list me-1 text-primary"></i>Selecionar Produto
                                            </label>
                                            <select class="form-select" id="produto-select" style="width: 100%"></select>
                                        </div> -->
                                        <div class="col-lg-2 col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-secondary w-100" id="limpar-busca">
                                                <i class="fas fa-eraser me-1"></i> Limpar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Status da Busca -->
                                    <div class="mb-3" id="select-status">
                                        <div class="alert alert-info border-0 bg-light">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>
                                            Digite o nome de um produto para buscar
                                        </div>
                                    </div>

                                    <!-- Descrição do Produto (Gemini) -->
                                    <div class="mb-3 d-none" id="descricao-gemini-container">
                                        <div class="alert alert-success border-0 bg-light">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-robot me-2 text-success mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <strong>Descrição do Produto (IA):</strong>
                                                    <div id="descricao-gemini-text" class="mt-1 text-dark"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detalhes do Produto -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="produto-quantidade" class="form-label fw-semibold">
                                                <i class="fas fa-sort-numeric-up me-1 text-primary"></i>Quantidade
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">Qtd</span>
                                                <input type="number" class="form-control" id="produto-quantidade" min="1" value="1">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label for="produto-valor" class="form-label fw-semibold">
                                                <i class="fas fa-dollar-sign me-1 text-primary"></i>Valor Unitário
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">R$</span>
                                                <input type="number" class="form-control" id="produto-valor" min="0" step="0.01" value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="produto-unidade" class="form-label fw-semibold">
                                                <i class="fas fa-balance-scale me-1 text-primary"></i>Unidade
                                            </label>
                                            <select class="form-select" id="produto-unidade">
                                                <option value="">Selecione</option>
                                                <option value="UN">UN - Unidade</option>
                                                <option value="KG">KG - Quilograma</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-2 col-sm-6 d-flex align-items-end">
                                            <button type="button" class="btn btn-success w-100" id="adicionar-produto">
                                                <i class="fas fa-plus-circle me-2"></i>Adicionar Produto
                                            </button>
                                        </div>
                                        <div class="col-lg-2 col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-primary w-100 d-none" id="reload-produtos">
                                                <i class="fas fa-sync-alt me-1"></i> Recarregar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Alertas -->
                                    <div class="alert alert-warning border-0 d-none" id="sem-produtos-alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Nenhum produto adicionado. Adicione pelo menos um produto para continuar.
                                    </div>

                                    <!-- Resumo dos Produtos -->
                                    <div id="resumo-produtos" class="mb-4 d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-bold text-primary">
                                                <i class="fas fa-boxes me-2"></i>Produtos Adicionados
                                            </h6>
                                            <div class="d-flex gap-3">
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    Total: R$ <span id="valor-total">0.00</span>
                                                </span>
                                                <span class="badge bg-info fs-6">
                                                    <i class="fas fa-weight-hanging me-1"></i>
                                                    Peso: <span id="peso-total">0.00</span> kg
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row g-3" id="produtos-cards"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Caixas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-secondary text-white py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cube me-3 fs-4"></i>
                                        <div>
                                            <h5 class="mb-0 fw-bold">Caixas e Embalagem</h5>
                                            <small class="opacity-75">Defina as dimensões e peso das caixas</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Formulário de Caixa -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="altura" class="form-label fw-semibold">
                                                <i class="fas fa-arrows-alt-v me-1 text-secondary"></i>Altura
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="altura" min="1" value="10">
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="largura" class="form-label fw-semibold">
                                                <i class="fas fa-arrows-alt-h me-1 text-secondary"></i>Largura
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="largura" min="1" value="20">
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="comprimento" class="form-label fw-semibold">
                                                <i class="fas fa-arrows-alt-h me-1 text-secondary"></i>Comprimento
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="comprimento" min="1" value="30">
                                                <span class="input-group-text bg-light">cm</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="peso_caixa" class="form-label fw-semibold">
                                                <i class="fas fa-weight-hanging me-1 text-secondary"></i>Peso
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="peso_caixa" min="0.01" step="0.01" value="0.5">
                                                <span class="input-group-text bg-light">kg</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-secondary w-100" id="adicionar-caixa">
                                                <i class="fas fa-plus-circle me-2"></i>Adicionar Caixa
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Alertas -->
                                    <div class="alert alert-warning border-0 d-none" id="sem-caixas-alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Nenhuma caixa adicionada. Adicione pelo menos uma caixa para continuar.
                                    </div>

                                    <!-- Resumo das Caixas -->
                                    <div id="resumo-caixas" class="d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-bold text-secondary">
                                                <i class="fas fa-cubes me-2"></i>Caixas Adicionadas
                                            </h6>
                                            <span class="badge bg-secondary fs-6">
                                                <i class="fas fa-cube me-1"></i>
                                                <span id="total-caixas">0</span> caixa(s)
                                            </span>
                                        </div>
                                        <div class="row g-3" id="caixas-cards"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos Ocultos -->
                    <input type="hidden" id="produtos-json" name="produtos_json">
                    <input type="hidden" id="valor-total-input" name="valor_total">
                    <input type="hidden" id="peso-total-input" name="peso_total">
                    <input type="hidden" id="caixas-json" name="caixas_json">
                    <input type="hidden" id="altura-hidden" name="altura">
                    <input type="hidden" id="largura-hidden" name="largura">
                    <input type="hidden" id="comprimento-hidden" name="comprimento">
                    <input type="hidden" id="peso-caixa-hidden" name="peso_caixa">
                    <input type="hidden" id="servico_entrega" name="servico_entrega">

                    <!-- Botão de Continuar -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-lg px-4" id="btn-step-2-next">
                                    <i class="fas fa-arrow-right me-2"></i>Continuar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Etapa 3: Endereço de Origem e Destino -->
                <div id="step-3" data-step="3" class="d-none">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <!-- Bloco de origem -->
                            <div class="card border-light shadow-sm">
                                <div class="card-header bg-light" style="background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%) !important; color: #fff;">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <h5 class="mb-0" style="color: white; display: inline;">Endereço de Origem</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="origem_nome" class="form-label">Nome/Razão Social</label>
                                        <input type="text" class="form-control" id="origem_nome" name="origem_nome" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="origem_endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="origem_endereco" name="origem_endereco" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <label for="origem_cep" class="form-label">CEP</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="origem_cep" name="origem_cep" maxlength="9" required>
                                                <button type="button" class="btn btn-outline-secondary" id="origem_buscar_cep">Buscar CEP</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="origem_pais" class="form-label">País</label>
                                            <select class="form-select pais-select" id="origem_pais" name="origem_pais" required>
                                                <option value="">Selecione o país</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="origem_estado" class="form-label">Estado</label>
                                            <select class="form-select estado-select" id="origem_estado" name="origem_estado" required>
                                                <option value="">Selecione o estado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3" id="origem_cidade_container">
                                        <label for="origem_cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="origem_cidade" name="origem_cidade" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label for="origem_telefone" class="form-label">Telefone</label>
                                            <input type="text" class="form-control" id="origem_telefone" name="origem_telefone" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="origem_email" class="form-label">E-mail</label>
                                            <input type="email" class="form-control" id="origem_email" name="origem_email" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <!-- Bloco de destino -->
                            <div class="card border-light shadow-sm">
                                <div class="card-header bg-light" style="background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%) !important; color: #fff;">
                                    <i class="fas fa-map-marker me-2"></i>
                                    <h5 class="mb-0" style="color: white; display: inline;">Endereço de Destino</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="destino_nome" class="form-label">Nome/Razão Social</label>
                                        <input type="text" class="form-control" id="destino_nome" name="destino_nome" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="destino_endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="destino_endereco" name="destino_endereco" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-4">
                                            <label for="destino_cep" class="form-label">CEP</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="destino_cep" name="destino_cep" maxlength="9" required>
                                                <button type="button" class="btn btn-outline-secondary" id="destino_buscar_cep">Buscar CEP</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="destino_pais" class="form-label">País</label>
                                            <select class="form-select pais-select" id="destino_pais" name="destino_pais" required>
                                                <option value="">Selecione o país</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="destino_estado" class="form-label">Estado</label>
                                            <select class="form-select estado-select" id="destino_estado" name="destino_estado" required>
                                                <option value="">Selecione o estado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3" id="destino_cidade_container">
                                        <label for="destino_cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="destino_cidade" name="destino_cidade" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label for="destino_telefone" class="form-label">Telefone</label>
                                            <input type="text" class="form-control" id="destino_telefone" name="destino_telefone" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="destino_email" class="form-label">E-mail</label>
                                            <input type="email" class="form-control" id="destino_email" name="destino_email" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="btn-step-3-next">Continuar</button>
                    </div>
                </div>
                <!-- Etapa 4: Revisão Final (modal será aberto via JS) -->
                <!-- Etapa 5: Serviços -->
                <div id="step-5" data-step="5" class="d-none">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-light shadow-sm">
                                <div class="card-header bg-light" style="background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%) !important; color: #fff;">
                                    <i class="fas fa-shipping-fast me-2"></i>
                                    <h5 class="mb-0" style="color: white;">Serviço de Entrega FedEx</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p class="mt-3 text-muted">Carregando opções de serviço...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Etapa 6: Pagamento -->
                <div id="step-6" data-step="6" class="d-none">
                    <div class="row mb-4" id="pagamento-section">
                        <div class="col-12">
                            <div class="card border-light shadow-sm">
                                <div class="card-header bg-light" style="background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%) !important; color: #fff;">
                                    <i class="fas fa-credit-card me-2"></i>
                                    <h5 class="mb-0">Método de Pagamento</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Bloco de pagamento (copiado do original) -->
                                    <!-- ... cole aqui o HTML do bloco de pagamento ... -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de Revisão Final (Etapa 4) -->
            <div class="modal fade" id="modal-revisao-final" tabindex="-1" aria-labelledby="modalRevisaoFinalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalRevisaoFinalLabel">Revisão Final</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Aqui você pode montar o resumo dos dados preenchidos -->
                            <div id="resumo-revisao-final"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" id="btn-editar-etapas">Editar</button>
                            <button type="button" class="btn btn-success" id="btn-confirmar-revisao">Confirmar e Continuar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Adicionar o script do Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Verificar se o jQuery está disponível
    if (typeof jQuery === 'undefined') {
        //console.error('jQuery não está carregado. Carregando...');

        // Adicionar jQuery se não estiver disponível
        var script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.4.min.js';
        script.integrity = 'sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=';
        script.crossOrigin = 'anonymous';
        script.onload = function() {
            //console.log('jQuery carregado com sucesso. Inicializando...');
            inicializarApp();
        };
        document.head.appendChild(script);
    } else {
        // jQuery já está carregado, inicializar normalmente
        $(document).ready(function() {
            inicializarApp();
        });
    }

    // Função para inicializar a aplicação
    function inicializarApp() {
        //console.log("Documento pronto, iniciando script");

        // Função para mostrar alertas
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show border-0" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'danger' ? 'times-circle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            // Verificar se já existe um container de alerta
            if ($('#alert-container').length === 0) {
                // Criar container de alerta antes do formulário
                $('.card-body').prepend('<div id="alert-container"></div>');
            }

            // Adicionar o alerta e rolar até ele
            $('#alert-container').html(alertHtml);
            $('html, body').animate({
                scrollTop: $('#alert-container').offset().top - 100
            }, 500);

            // Auto-fechamento após 5 segundos para alertas de sucesso
            if (type === 'success') {
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        }

        // Verificar se o Select2 está disponível
        if (typeof $.fn.select2 === 'undefined') {
            //console.error("Select2 não está carregado! Tentando carregar novamente...");
            // Tentar carregar o Select2 novamente
            $.getScript("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js")
                .done(function() {
                    //console.log("Select2 carregado com sucesso");
                    inicializarSelect2();
                })
                .fail(function(jqxhr, settings, exception) {
                    //console.error("Erro ao carregar Select2:", exception);
                    alert("Erro ao carregar o componente de seleção de produtos. Por favor, recarregue a página.");
                });
        } else {
            //console.log("Select2 já está carregado, inicializando...");
            inicializarSelect2();
        }

        // Variável para armazenar a última resposta do Gemini
        let ultimaDescricaoGemini = '';

        // Array para armazenar os produtos adicionados
        let produtos = [];
        let valorTotal = 0;
        let pesoTotal = 0;

        // Array para armazenar as caixas adicionadas
        let caixas = [];

        // Variável para armazenar produto em confirmação
        let produtoEmConfirmacao = null;

        // Variáveis para controle de paginação e busca
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;

        // Dados de países, estados e cidades
        const paises = [{
                id: "BR",
                nome: "Brasil"
            },
            {
                id: "US",
                nome: "Estados Unidos"
            },
            {
                id: "PT",
                nome: "Portugal"
            },
            {
                id: "ES",
                nome: "Espanha"
            },
            {
                id: "FR",
                nome: "França"
            },
            {
                id: "IT",
                nome: "Itália"
            },
            {
                id: "DE",
                nome: "Alemanha"
            },
            {
                id: "GB",
                nome: "Reino Unido"
            },
            {
                id: "JP",
                nome: "Japão"
            },
            {
                id: "CN",
                nome: "China"
            },
            {
                id: "AR",
                nome: "Argentina"
            },
            {
                id: "UY",
                nome: "Uruguai"
            },
            {
                id: "CL",
                nome: "Chile"
            },
            {
                id: "MX",
                nome: "México"
            },
            {
                id: "CA",
                nome: "Canadá"
            },
            {
                id: "AU",
                nome: "Austrália"
            }
        ];

        const estados = {
            "BR": [{
                    id: "AC",
                    nome: "Acre"
                },
                {
                    id: "AL",
                    nome: "Alagoas"
                },
                {
                    id: "AM",
                    nome: "Amazonas"
                },
                {
                    id: "AP",
                    nome: "Amapá"
                },
                {
                    id: "BA",
                    nome: "Bahia"
                },
                {
                    id: "CE",
                    nome: "Ceará"
                },
                {
                    id: "DF",
                    nome: "Distrito Federal"
                },
                {
                    id: "ES",
                    nome: "Espírito Santo"
                },
                {
                    id: "GO",
                    nome: "Goiás"
                },
                {
                    id: "MA",
                    nome: "Maranhão"
                },
                {
                    id: "MG",
                    nome: "Minas Gerais"
                },
                {
                    id: "MS",
                    nome: "Mato Grosso do Sul"
                },
                {
                    id: "MT",
                    nome: "Mato Grosso"
                },
                {
                    id: "PA",
                    nome: "Pará"
                },
                {
                    id: "PB",
                    nome: "Paraíba"
                },
                {
                    id: "PE",
                    nome: "Pernambuco"
                },
                {
                    id: "PI",
                    nome: "Piauí"
                },
                {
                    id: "PR",
                    nome: "Paraná"
                },
                {
                    id: "RJ",
                    nome: "Rio de Janeiro"
                },
                {
                    id: "RN",
                    nome: "Rio Grande do Norte"
                },
                {
                    id: "RO",
                    nome: "Rondônia"
                },
                {
                    id: "RR",
                    nome: "Roraima"
                },
                {
                    id: "RS",
                    nome: "Rio Grande do Sul"
                },
                {
                    id: "SC",
                    nome: "Santa Catarina"
                },
                {
                    id: "SE",
                    nome: "Sergipe"
                },
                {
                    id: "SP",
                    nome: "São Paulo"
                },
                {
                    id: "TO",
                    nome: "Tocantins"
                }
            ],
            "US": [{
                    id: "AL",
                    nome: "Alabama"
                },
                {
                    id: "AK",
                    nome: "Alaska"
                },
                {
                    id: "AZ",
                    nome: "Arizona"
                },
                {
                    id: "AR",
                    nome: "Arkansas"
                },
                {
                    id: "CA",
                    nome: "California"
                },
                {
                    id: "CO",
                    nome: "Colorado"
                },
                {
                    id: "CT",
                    nome: "Connecticut"
                },
                {
                    id: "DE",
                    nome: "Delaware"
                },
                {
                    id: "FL",
                    nome: "Florida"
                },
                {
                    id: "GA",
                    nome: "Georgia"
                },
                {
                    id: "HI",
                    nome: "Hawaii"
                },
                {
                    id: "ID",
                    nome: "Idaho"
                },
                {
                    id: "IL",
                    nome: "Illinois"
                },
                {
                    id: "IN",
                    nome: "Indiana"
                },
                {
                    id: "IA",
                    nome: "Iowa"
                },
                {
                    id: "KS",
                    nome: "Kansas"
                },
                {
                    id: "KY",
                    nome: "Kentucky"
                },
                {
                    id: "LA",
                    nome: "Louisiana"
                },
                {
                    id: "ME",
                    nome: "Maine"
                },
                {
                    id: "MD",
                    nome: "Maryland"
                },
                {
                    id: "MA",
                    nome: "Massachusetts"
                },
                {
                    id: "MI",
                    nome: "Michigan"
                },
                {
                    id: "MN",
                    nome: "Minnesota"
                },
                {
                    id: "MS",
                    nome: "Mississippi"
                },
                {
                    id: "MO",
                    nome: "Missouri"
                },
                {
                    id: "MT",
                    nome: "Montana"
                },
                {
                    id: "NE",
                    nome: "Nebraska"
                },
                {
                    id: "NV",
                    nome: "Nevada"
                },
                {
                    id: "NH",
                    nome: "New Hampshire"
                },
                {
                    id: "NJ",
                    nome: "New Jersey"
                },
                {
                    id: "NM",
                    nome: "New Mexico"
                },
                {
                    id: "NY",
                    nome: "New York"
                },
                {
                    id: "NC",
                    nome: "North Carolina"
                },
                {
                    id: "ND",
                    nome: "North Dakota"
                },
                {
                    id: "OH",
                    nome: "Ohio"
                },
                {
                    id: "OK",
                    nome: "Oklahoma"
                },
                {
                    id: "OR",
                    nome: "Oregon"
                },
                {
                    id: "PA",
                    nome: "Pennsylvania"
                },
                {
                    id: "RI",
                    nome: "Rhode Island"
                },
                {
                    id: "SC",
                    nome: "South Carolina"
                },
                {
                    id: "SD",
                    nome: "South Dakota"
                },
                {
                    id: "TN",
                    nome: "Tennessee"
                },
                {
                    id: "TX",
                    nome: "Texas"
                },
                {
                    id: "UT",
                    nome: "Utah"
                },
                {
                    id: "VT",
                    nome: "Vermont"
                },
                {
                    id: "VA",
                    nome: "Virginia"
                },
                {
                    id: "WA",
                    nome: "Washington"
                },
                {
                    id: "WV",
                    nome: "West Virginia"
                },
                {
                    id: "WI",
                    nome: "Wisconsin"
                },
                {
                    id: "WY",
                    nome: "Wyoming"
                }
            ],
            // Adicionar alguns estados básicos para outros países
            "PT": [{
                    id: "LI",
                    nome: "Lisboa"
                },
                {
                    id: "PO",
                    nome: "Porto"
                },
                {
                    id: "FA",
                    nome: "Faro"
                },
                {
                    id: "CO",
                    nome: "Coimbra"
                }
            ]
            // Demais países podem ser adicionados conforme necessário
        };

        const cidades = {
            "SP": [{
                    id: "SAO",
                    nome: "São Paulo"
                },
                {
                    id: "CAM",
                    nome: "Campinas"
                },
                {
                    id: "RIB",
                    nome: "Ribeirão Preto"
                },
                {
                    id: "SJC",
                    nome: "São José dos Campos"
                },
                {
                    id: "SAN",
                    nome: "Santos"
                }
            ],
            "RJ": [{
                    id: "RIO",
                    nome: "Rio de Janeiro"
                },
                {
                    id: "NIT",
                    nome: "Niterói"
                },
                {
                    id: "PET",
                    nome: "Petrópolis"
                },
                {
                    id: "MAC",
                    nome: "Macaé"
                }
            ],
            "MG": [{
                    id: "BHZ",
                    nome: "Belo Horizonte"
                },
                {
                    id: "UBE",
                    nome: "Uberlândia"
                },
                {
                    id: "CON",
                    nome: "Contagem"
                },
                {
                    id: "JDF",
                    nome: "Juiz de Fora"
                },
                {
                    id: "MOC",
                    nome: "Montes Claros"
                },
                {
                    id: "IPA",
                    nome: "Ipatinga"
                },
                {
                    id: "DIV",
                    nome: "Divinópolis"
                },
                {
                    id: "POC",
                    nome: "Poços de Caldas"
                },
                {
                    id: "VAR",
                    nome: "Varginha"
                },
                {
                    id: "UBA",
                    nome: "Uberaba"
                },
                {
                    id: "GVR",
                    nome: "Governador Valadares"
                },
                {
                    id: "PSS",
                    nome: "Pouso Alegre"
                },
                {
                    id: "SJR",
                    nome: "São João del-Rei"
                },
                {
                    id: "ITA",
                    nome: "Itajubá"
                },
                {
                    id: "LAV",
                    nome: "Lavras"
                },
                {
                    id: "BAR",
                    nome: "Barbacena"
                },
                {
                    id: "ARA",
                    nome: "Araxá"
                },
                {
                    id: "ITU",
                    nome: "Ituiutaba"
                },
                {
                    id: "FOR",
                    nome: "Formiga"
                },
                {
                    id: "CAT",
                    nome: "Cataguases"
                },
                {
                    id: "TEO",
                    nome: "Teófilo Otoni"
                },
                {
                    id: "PSO",
                    nome: "Passos"
                },
                {
                    id: "MUR",
                    nome: "Muriaé"
                },
                {
                    id: "PAT",
                    nome: "Patos de Minas"
                },
                {
                    id: "IBI",
                    nome: "Ibirité"
                },
                {
                    id: "SAB",
                    nome: "Sabará"
                },
                {
                    id: "NLA",
                    nome: "Nova Lima"
                },
                {
                    id: "LFO",
                    nome: "Lafaiete"
                },
                {
                    id: "BTC",
                    nome: "Betim"
                },
                {
                    id: "SCL",
                    nome: "Santa Luzia"
                },
                {
                    id: "ITC",
                    nome: "Itaúna"
                },
                {
                    id: "COG",
                    nome: "Congonhas"
                },
                {
                    id: "AXE",
                    nome: "Araguari"
                },
                {
                    id: "PAR",
                    nome: "Paracatu"
                },
                {
                    id: "TPI",
                    nome: "Três Pontas"
                },
                {
                    id: "OPA",
                    nome: "Ouro Preto"
                }
            ],
            "CA": [{
                    id: "LA",
                    nome: "Los Angeles"
                },
                {
                    id: "SF",
                    nome: "San Francisco"
                },
                {
                    id: "SD",
                    nome: "San Diego"
                },
                {
                    id: "SJ",
                    nome: "San Jose"
                }
            ],
            "NY": [{
                    id: "NYC",
                    nome: "New York City"
                },
                {
                    id: "BUF",
                    nome: "Buffalo"
                },
                {
                    id: "ROC",
                    nome: "Rochester"
                },
                {
                    id: "SYR",
                    nome: "Syracuse"
                }
            ],
            "TX": [{
                    id: "HOU",
                    nome: "Houston"
                },
                {
                    id: "DAL",
                    nome: "Dallas"
                },
                {
                    id: "AUS",
                    nome: "Austin"
                },
                {
                    id: "SAT",
                    nome: "San Antonio"
                }
            ],
            "LI": [{
                    id: "LIS",
                    nome: "Lisboa"
                },
                {
                    id: "CAS",
                    nome: "Cascais"
                },
                {
                    id: "SIN",
                    nome: "Sintra"
                },
                {
                    id: "OEI",
                    nome: "Oeiras"
                }
            ]
            // Outras cidades podem ser adicionadas conforme necessário
        };

        // Variável para controlar se o Select2 está sendo inicializado
        let inicializandoSelect2 = false;

        // Função para inicializar o Select2
        function inicializarSelect2() {
            // Evitar inicialização múltipla
            if (inicializandoSelect2) {
                //console.log("Select2 já está sendo inicializado. Aguardando...");
                return;
            }

            inicializandoSelect2 = true;
            //console.log("Destruindo instância anterior de Select2 caso exista");

            // Fechar qualquer dropdown aberto
            $('.select2-container').remove();

            // Destruir instância anterior caso exista
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }

            // Limpar lista de produtos e garantir que tenha a opção padrão
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));

            //console.log("Inicializando novo Select2");
            $('#produto-select').select2({
                placeholder: 'Selecione ou busque um produto',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#produto-select').parent(),
                language: {
                    noResults: function() {
                        return "Nenhum produto encontrado";
                    },
                    searching: function() {
                        return "Buscando...";
                    },
                    inputTooShort: function() {
                        return "Digite pelo menos 3 caracteres para buscar";
                    }
                },
                minimumInputLength: 0 // Permitir carregar todos os produtos sem digitar
            });

            // Evento ao selecionar um produto
            $('#produto-select').on('select2:select', function(e) {
                const produtoSelecionado = e.params.data;
                //console.log("Produto selecionado:", produtoSelecionado);

                // Garantir que temos as propriedades código e NCM
                const ncm = produtoSelecionado.codigo || produtoSelecionado.id;
                const descricao = produtoSelecionado.text.split(' (NCM:')[0]; // Extrair apenas a descrição

                // Atualizar o campo NCM para mostrar o código do produto selecionado
                $('#busca-codigo').val(ncm);

                // Mostrar uma mensagem informativa sobre o produto selecionado
                $('#select-status').html('<strong>Produto selecionado:</strong> ' + descricao + ' <span class="badge bg-info">NCM: ' + ncm + '</span>');

                // Sugerir valor inicial (pode ser editado pelo usuário)
                const valorSugerido = produtoSelecionado.valor || 10.00;
                $('#produto-valor').val(valorSugerido.toFixed(2));
                $('#produto-valor').select(); // Seleciona o texto para fácil edição

                // Se já temos uma unidade no campo, não sobrescrever
                const unidadeAtual = $('#produto-unidade').val();

                if (!unidadeAtual) {
                    //console.log("Unidade não preenchida. Buscando unidade para o NCM:", ncm);

                    // Buscar a unidade tributária com base no NCM extraído
                    const ncmFormatado = formatarNCMParaBusca(ncm);
                    //console.log("NCM formatado para busca de unidade:", ncmFormatado);

                    buscarUnidadeTributaria(ncm)
                        .done(function(response) {
                            //console.log("Resposta da busca de unidade:", response);
                            if (response.success && response.unidade) {
                                // Validar que a unidade é UN ou KG, caso contrário, usar UN como padrão
                                const unidadeNormalizada = (response.unidade === 'KG' || response.unidade === 'UN') ?
                                    response.unidade :
                                    'UN';

                                $('#produto-unidade').val(unidadeNormalizada);
                                //console.log("Unidade tributária encontrada e normalizada:", unidadeNormalizada);
                            } else {
                                //console.warn("Unidade não encontrada para o NCM:", ncm);
                                // Verificar se o nome do produto tem relação com produtos que geralmente são KG
                                const textoLowerCase = descricao.toLowerCase();
                                const produtosEmKG = [
                                    'café', 'cafe', 'açúcar', 'acucar', 'arroz', 'feijão', 'feijao', 'farinha',
                                    'grão', 'grao', 'grãos', 'graos', 'semente', 'sementes', 'cereal', 'cereais',
                                    'frutas', 'fruta', 'legume', 'legumes', 'verdura', 'verduras', 'carne',
                                    'pó', 'po', 'chá', 'cha', 'erva', 'tempero', 'especiaria', 'chocolate',
                                    'cacau', 'sal', 'açúcar', 'granel', 'peso', 'quilograma', 'quilo'
                                ];

                                let ehKG = false;
                                for (const produto of produtosEmKG) {
                                    if (textoLowerCase.includes(produto)) {
                                        ehKG = true;
                                        break;
                                    }
                                }

                                // Definir a unidade com base na detecção
                                $('#produto-unidade').val(ehKG ? 'KG' : 'UN');
                                //console.log(`Unidade inferida pelo nome do produto: ${ehKG ? 'KG' : 'UN'}`);
                            }
                        })
                        .fail(function(error) {
                            //console.error("Erro ao buscar unidade tributária:", error);
                            $('#produto-unidade').val('UN'); // Valor padrão em caso de erro
                        });
                } else {
                    //console.log("Mantendo unidade já preenchida:", unidadeAtual);
                }
            });

            inicializandoSelect2 = false;

            // Não realizamos a busca automática aqui - a busca será feita por quem chamou a função
        }

        // Função para realizar a busca baseada nos campos de busca
        function realizarBusca() {
            $('#select-status').html(`
                <div class="alert alert-info border-0 bg-light">
                    <i class="fas fa-spinner fa-spin me-2 text-primary"></i>
                    Buscando produtos...
                </div>
            `);

            const buscaDescricao = $('#busca-descricao').val();
            const buscaNCM = $('#busca-codigo').val();

            // Limpar a descrição anterior do Gemini
            ultimaDescricaoGemini = '';
            
            // Ocultar descrição do Gemini
            $('#descricao-gemini-container').addClass('d-none');

            // Destruir a instância do Select2 para garantir que seja completamente reinicializado
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }

            // Limpar completamente o select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));

            // Se tiver uma descrição de produto e não tiver um NCM, consultar o Gemini
            if (buscaDescricao && !buscaNCM) {
                $('#select-status').html(`
                    <div class="alert alert-info border-0 bg-light">
                        <i class="fas fa-robot me-2 text-primary"></i>
                        Consultando IA para identificar NCM e unidade...
                    </div>
                `);

                // Mostrar indicador de carregamento
                $('#busca-descricao').addClass('loading');

                // Chama o endpoint para consultar o Gemini
                $.ajax({
                    url: 'http://localhost:8080/gemini-consulta.php',
                    method: 'POST',
                    data: JSON.stringify({
                        produto: buscaDescricao
                    }),
                    contentType: 'application/json',
                    dataType: 'json',
                    beforeSend: function() {
                        //console.log("Enviando consulta para o Gemini para o produto:", buscaDescricao);
                    },
                    success: function(response) {
                        // Ocultar indicador de carregamento
                        $('#busca-descricao').removeClass('loading');

                        if (response.success) {
                            // Preencher os campos diretamente com o que veio do backend
                            $('#busca-codigo').val(response.ncm);
                            $('#produto-unidade').val(response.unidade);
                            $('#busca-descricao').val(response.descricao);
                            
                            // Definir a variável global com a resposta do Gemini
                            ultimaDescricaoGemini = response.raw_response || response.descricao;
                            
                            // Mostrar a descrição do Gemini
                            if (ultimaDescricaoGemini) {
                                $('#descricao-gemini-text').html(ultimaDescricaoGemini.replace(/\n/g, '<br>'));
                                $('#descricao-gemini-container').removeClass('d-none');
                            }
                            
                            $('#select-status').html(`
                                <div class="alert alert-success border-0 bg-light">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    NCM identificado: <strong>${response.ncm}</strong>. Unidade: <strong>${response.unidade}</strong>. Buscando produtos...
                                </div>
                            `);

                            // Buscar produtos pelo NCM retornado
                            buscarProdutos({
                                codigo: response.ncm,
                                descricao: response.descricao
                            });
                        } else {
                            // Ocultar descrição do Gemini em caso de erro
                            $('#descricao-gemini-container').addClass('d-none');
                            
                            $('#select-status').html(`
                                <div class="alert alert-warning border-0 bg-light">
                                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                    Erro na consulta da IA: ${response.error}. Tentando busca direta...
                                </div>
                            `);
                            // Continuar com a busca normal
                            buscarProdutos({
                                descricao: buscaDescricao
                            });
                        }
                    },
                    error: function(error) {
                        // Ocultar indicador de carregamento
                        $('#busca-descricao').removeClass('loading');

                        $('#select-status').html(`
                            <div class="alert alert-warning border-0 bg-light">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                Erro na consulta da IA. Tentando busca direta...
                            </div>
                        `);
                        //console.error("Erro ao consultar a IA:", error);
                        // Continuar com a busca normal
                        buscarProdutos({
                            descricao: buscaDescricao
                        });
                    }
                });
            } else {
                // Criar o objeto de busca normal
                const searchParams = {};
                if (buscaDescricao) searchParams.descricao = buscaDescricao;
                if (buscaNCM) searchParams.codigo = buscaNCM;

                // Executar a busca direta
                buscarProdutos(searchParams);
            }
        }

        // Função para extrair o NCM da resposta do Gemini
        function extrairNCM(texto) {
            //console.log("Extraindo NCM do texto:", texto);

            // Caso específico para Havaianas
            if (texto.toLowerCase().includes('havaianas') && texto.includes('6402.20.00')) {
                //console.log("Caso especial: Havaianas encontrado com NCM 6402.20.00");
                return '6402.20.00';
            }

            // Primeiro tenta encontrar o NCM entre asteriscos, uma convenção comum
            const boldMatch = texto.match(/\*\*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})\*\*/);
            if (boldMatch && boldMatch[1]) {
                //console.log("NCM encontrado em destaque:", boldMatch[1]);
                return formatarNCM(boldMatch[1]);
            }

            // Padrões para encontrar o NCM na resposta
            const padroes = [
                /NCM[:\s]*(?:é|e|do produto)?[:\s]*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/i, // "NCM é: 6402.20.00" ou "NCM do produto: 6402.20.00"
                /código NCM[:\s]*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/i, // "código NCM: 6402.20.00"
                /([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/, // Formato numérico simples com ou sem pontos
                /NCM[:\s]+([0-9]{4}\.?[0-9]{2})/i, // "NCM: 6402.20" (formato parcial)
            ];

            for (const padrao of padroes) {
                const match = texto.match(padrao);
                if (match && match[1]) {
                    //console.log("NCM encontrado com padrão:", padrao.toString(), match[1]);
                    return formatarNCM(match[1]);
                }
            }

            //console.log("Nenhum NCM encontrado no texto");
            return null;
        }

        // Função auxiliar para formatar o NCM encontrado
        function formatarNCM(ncm) {
            // Remover pontos se houver
            let ncmLimpo = ncm.replace(/\./g, '');

            // Padronizar para o formato que está no JSON (com pontos)
            if (ncmLimpo.length >= 8) {
                // Formatar como XXXX.XX.XX
                return ncmLimpo.slice(0, 4) + '.' + ncmLimpo.slice(4, 6) + '.' + ncmLimpo.slice(6, 8);
            } else if (ncmLimpo.length >= 6) {
                // Formatar como XXXX.XX
                return ncmLimpo.slice(0, 4) + '.' + ncmLimpo.slice(4, 6);
            } else if (ncmLimpo.length >= 4) {
                // Formatar como XXXX
                return ncmLimpo.slice(0, 4);
            }

            return ncm; // Retorna como está se não conseguir formatar
        }

        // Nova função para extrair a unidade da resposta do Gemini
        function extrairUnidade(texto) {
            //console.log("Extraindo unidade do texto:", texto);

            // Lista de produtos comumente vendidos em KG
            const produtosEmKG = [
                'café', 'cafe', 'açúcar', 'acucar', 'arroz', 'feijão', 'feijao', 'farinha',
                'grão', 'grao', 'grãos', 'graos', 'semente', 'sementes', 'cereal', 'cereais',
                'frutas', 'fruta', 'legume', 'legumes', 'verdura', 'verduras', 'carne',
                'pó', 'po', 'chá', 'cha', 'erva', 'tempero', 'especiaria', 'chocolate',
                'cacau', 'sal', 'açúcar', 'granel', 'peso', 'quilograma', 'quilo',
                'soja', 'milho', 'trigo', 'aveia', 'cevada', 'centeio'
            ];

            // Verificar se o texto menciona explicitamente algum produto que é vendido por KG
            const textoLowerCase = texto.toLowerCase();
            for (const produto of produtosEmKG) {
                if (textoLowerCase.includes(produto)) {
                    //console.log(`Produto "${produto}" encontrado no texto, atribuindo unidade KG`);
                    return "KG";
                }
            }

            // Padrões para encontrar a unidade na resposta (UN ou KG)
            const padroes = [
                /unidade[:\s]*(?:é|e|do produto)?[:\s]*([UNKGunkg]{2})/i, // "unidade é: UN" ou "unidade: KG"
                /unidade[:\s]*(?:de|de medida|tributária)?[:\s]*([UNKGunkg]{2})/i, // "unidade de medida: UN"
                /produto[^.]*?medido em\s+([UNKGunkg]{2})/i, // "produto é medido em KG"
                /vendido\s+(?:em|por)\s+([UNKGunkg]{2})/i, // "vendido em UN" ou "vendido por KG"
                /([UNKGunkg]{2})\s+[-–]\s+[UNKGunkg]/i, // "UN - Unidade" ou "KG - Quilograma"
                /comercializado\s+(?:em|por)\s+([UNKGunkg]{2})/i, // "comercializado em KG"
                /(?:peso|massa)[^.]*?(?:em|por)\s+([UNKGunkg]{2})/i, // "peso em KG"
            ];

            for (const padrao of padroes) {
                const match = texto.match(padrao);
                if (match && match[1]) {
                    const unidade = match[1].toUpperCase();
                    //console.log("Unidade encontrada com padrão:", padrao.toString(), unidade);
                    return unidade === "UN" || unidade === "KG" ? unidade : "UN"; // Padrão para UN se não for KG
                }
            }

            // Se não encontrou nenhum padrão específico, verificar menções gerais
            if (textoLowerCase.includes('quilograma') ||
                textoLowerCase.includes('quilo') ||
                textoLowerCase.includes('kg') ||
                textoLowerCase.includes('kilo') ||
                textoLowerCase.includes('kilogramas') ||
                textoLowerCase.includes('quilos') ||
                textoLowerCase.includes('peso') ||
                textoLowerCase.includes('pesado') ||
                textoLowerCase.includes('pesar') ||
                textoLowerCase.includes('gramas') ||
                textoLowerCase.includes('granel') ||
                textoLowerCase.includes('a peso')) {
                //console.log("Unidade KG inferida pelo contexto");
                return "KG";
            }

            // Verificar se há menções a unidades ou contagem
            if (textoLowerCase.includes('unidade') ||
                textoLowerCase.includes('unidades') ||
                textoLowerCase.includes('peça') ||
                textoLowerCase.includes('peças') ||
                textoLowerCase.includes('unitário') ||
                textoLowerCase.includes('por peça') ||
                textoLowerCase.includes('por unidade') ||
                textoLowerCase.includes('cada um') ||
                textoLowerCase.includes('individuais')) {
                //console.log("Unidade UN inferida pelo contexto");
                return "UN";
            }

            // Se não encontrou nenhuma menção a peso, assume que é unidade
            //console.log("Unidade padrão UN assumida");
            return "UN";
        }

        // Função para formatar o NCM para busca no arquivo Unidade_trib.csv
        function formatarNCMParaBusca(ncm) {
            // Remover pontos se houver
            let ncmLimpo = ncm.replace(/\./g, '');

            // Remover zeros à esquerda (mas manter os da direita)
            ncmLimpo = ncmLimpo.replace(/^0+/, '');

            //console.log("Formatando NCM para busca:", ncm, "->", ncmLimpo);
            return ncmLimpo;
        }

        // Função para buscar a unidade tributária correspondente ao NCM
        function buscarUnidadeTributaria(ncm) {
            if (!ncm) return $.Deferred().reject('NCM não informado').promise();

            // Formatar NCM para busca: sem pontos e sem zeros à esquerda
            const ncmFormatado = formatarNCMParaBusca(ncm);
            //console.log("NCM formatado para busca:", ncmFormatado);

            // Fazer requisição AJAX para buscar a unidade no arquivo CSV
            return $.ajax({
                url: '{{ route("api.unidade-tributaria") }}',
                method: 'GET',
                data: {
                    ncm: ncmFormatado
                },
                dataType: 'json'
            });
        }

        // Função para buscar produtos por NCM
        function buscarProdutosPorNCM(ncm) {
            //console.log("Buscando produtos com NCM:", ncm);

            // Adicionar o NCM também no campo de busca por código para visualização
            $('#busca-codigo').val(ncm);

            // Manter o valor atual do campo de descrição
            const descricaoAtual = $('#busca-descricao').val();

            // Executar a busca usando o NCM e mantendo a descrição
            if (descricaoAtual) {
                buscarProdutos({
                    codigo: ncm,
                    descricao: descricaoAtual
                });
            } else {
                buscarProdutos({
                    codigo: ncm
                });
            }
        }

        // Função para buscar produtos (extraída da busca original)
        function buscarProdutos(searchParams) {
            // Garantir que a interface limpe completamente os resultados anteriores
            $('#select-status').html(`
                <div class="alert alert-info border-0 bg-light">
                    <i class="fas fa-spinner fa-spin me-2 text-primary"></i>
                    Buscando produtos...
                </div>
            `);

            // Destruir completamente a instância atual do Select2
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }

            // Limpar completamente o select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));

            // Inicializar o Select2 novamente
            inicializarSelect2();

            // Agora fazer a busca
            $.ajax({
                url: '{{ route("api.produtos.get") }}',
                data: {
                    page: 1,
                    limit: 100,
                    search: JSON.stringify(searchParams)
                },
                dataType: 'json',
                success: function(data) {
                    //console.log("Resultados da busca:", data);

                    // Limpar o select novamente para garantir
                    $('#produto-select').empty();
                    $('#produto-select').append(new Option('Selecione um produto', '', true, true));

                    if (data && data.produtos && data.produtos.length) {
                        // Criar e adicionar as opções
                        data.produtos.forEach(function(produto) {
                            // Adicionar o NCM na descrição do produto
                            const descricaoFormatada = produto.descricao + ' (NCM: ' + produto.codigo + ')';

                            const option = new Option(descricaoFormatada, produto.codigo, false, false);
                            // Armazenar dados adicionais para uso posterior
                            $(option).data('codigo', produto.codigo);
                            $(option).data('descricao', produto.descricao);
                            $(option).data('peso', 0.5); // Valor padrão
                            $(option).data('valor', 10); // Valor padrão

                            $('#produto-select').append(option);
                        });

                        // Acionar o change para atualizar o Select2
                        $('#produto-select').trigger('change');

                        //console.log("Opções carregadas:", data.produtos.length);

                        // Mostrar quantos produtos foram encontrados e o NCM identificado
                        if (searchParams.codigo) {
                            $('#select-status').html(`
                                <div class="alert alert-success border-0 bg-light">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <strong>${data.produtos.length} produtos encontrados</strong> com NCM: <strong>${searchParams.codigo}</strong>
                                </div>
                            `);

                            // Selecionar automaticamente o primeiro produto da lista se houver apenas um
                            if (data.produtos.length === 1) {
                                $('#produto-select').val(data.produtos[0].codigo).trigger('change');
                                //console.log("Produto único selecionado automaticamente:", data.produtos[0]);
                            }
                        } else {
                            $('#select-status').html(`
                                <div class="alert alert-success border-0 bg-light">
                                    <i class="fas fa-check-circle me-2 text-success"></i>
                                    <strong>${data.produtos.length} produtos encontrados</strong>
                                </div>
                            `);
                        }

                        // Esconder o botão de reload, pois os produtos foram carregados com sucesso
                        $('#reload-produtos').hide();
                    } else {
                        if (searchParams.codigo) {
                            // Se não encontrou produtos, mas temos uma descrição do Gemini, exibi-la
                            if (ultimaDescricaoGemini) {
                                // Extrair a descrição relevante do resultado do Gemini
                                let descricaoLimpa = '';

                                // Extrair o texto de resposta útil
                                let respostaUtil = '';

                                // Se tiver o marcador de resultado da consulta, usar apenas o que vem depois
                                if (ultimaDescricaoGemini.includes('Resultado da consulta:')) {
                                    respostaUtil = ultimaDescricaoGemini.split('Resultado da consulta:')[1];
                                } else if (ultimaDescricaoGemini.includes('Resposta recebida:')) {
                                    // Se tiver JSON na resposta, ignorá-lo
                                    const partes = ultimaDescricaoGemini.split('Resposta recebida:');
                                    if (partes.length > 1) {
                                        // Tenta encontrar onde termina o JSON e começa o texto real
                                        const textoRestante = partes[1];
                                        if (textoRestante.includes('Resultado da consulta:')) {
                                            respostaUtil = textoRestante.split('Resultado da consulta:')[1];
                                        } else {
                                            // Se não encontrar o marcador, usar tudo que vem depois do início do JSON
                                            const jsonFim = textoRestante.indexOf('}') + 1;
                                            if (jsonFim > 0) {
                                                respostaUtil = textoRestante.substring(jsonFim);
                                            } else {
                                                respostaUtil = textoRestante;
                                            }
                                        }
                                    }
                                } else {
                                    // Usar a resposta completa
                                    respostaUtil = ultimaDescricaoGemini;
                                }

                                // Limpar a resposta
                                respostaUtil = respostaUtil.replace(/---+/g, '').trim();

                                // Tentar encontrar a parte da descrição após o NCM
                                const linhasGemini = respostaUtil.split('\n');
                                for (const linha of linhasGemini) {
                                    // Procurar por uma linha que contenha o NCM e uma descrição
                                    if (linha.includes(searchParams.codigo)) {
                                        // Extrair a descrição após o NCM
                                        const partes = linha.split(' - ');
                                        if (partes.length > 1) {
                                            descricaoLimpa = partes[1].replace(/\*\*/g, '').trim();
                                            break;
                                        } else if (linha.includes(':')) {
                                            // Tentar com dois pontos
                                            const partesDoisPontos = linha.split(':');
                                            if (partesDoisPontos.length > 1) {
                                                descricaoLimpa = partesDoisPontos[1].replace(/\*\*/g, '').trim();
                                                break;
                                            }
                                        }
                                    }
                                }

                                // Se não conseguir extrair a descrição específica, tentar encontrar 
                                // qualquer descrição útil no texto
                                if (!descricaoLimpa) {
                                    // Limpar asteriscos e outros marcadores
                                    respostaUtil = respostaUtil.replace(/\*\*/g, '').trim();

                                    // Procurar por descrições comuns em produtos
                                    const termos = ['produto', 'artigo', 'mercadoria', 'item', 'bem'];
                                    for (const termo of termos) {
                                        if (respostaUtil.toLowerCase().includes(termo)) {
                                            // Pegar a sentença ou o parágrafo que contém o termo
                                            const sentencas = respostaUtil.split(/[.!?]\s+/);
                                            for (const sentenca of sentencas) {
                                                if (sentenca.toLowerCase().includes(termo)) {
                                                    descricaoLimpa = sentenca.trim();
                                                    break;
                                                }
                                            }
                                            if (descricaoLimpa) break;
                                        }
                                    }

                                    if (!descricaoLimpa) {
                                        descricaoLimpa = respostaUtil;
                                    }
                                }

                                // Garantir que a descrição não seja muito longa
                                if (descricaoLimpa.length > 100) {
                                    descricaoLimpa = descricaoLimpa.substring(0, 97) + '...';
                                }

                                // Adicionar uma opção manual baseada na descrição do Gemini
                                const descricaoFormatada = descricaoLimpa + ' (NCM: ' + searchParams.codigo + ')';
                                const option = new Option(descricaoFormatada, searchParams.codigo, false, false);

                                // Armazenar dados adicionais para uso posterior
                                $(option).data('codigo', searchParams.codigo);
                                $(option).data('descricao', descricaoLimpa);
                                $(option).data('peso', 0.5); // Valor padrão
                                $(option).data('valor', 10); // Valor padrão

                                $('#produto-select').append(option);
                                $('#produto-select').val(searchParams.codigo).trigger('change');

                                // Mostrar mensagem informativa
                                $('#select-status').html(`
                                    <div class="alert alert-info border-0 bg-light">
                                        <i class="fas fa-info-circle me-2 text-info"></i>
                                        <strong>Produto criado com descrição do Gemini</strong> - NCM: <strong>${searchParams.codigo}</strong>
                                    </div>
                                `);
                            } else {
                                $('#select-status').html(`
                                    <div class="alert alert-warning border-0 bg-light">
                                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                        <strong>Nenhum produto encontrado</strong> com NCM: <strong>${searchParams.codigo}</strong>
                                    </div>
                                `);
                            }
                        } else {
                            $('#select-status').html(`
                                <div class="alert alert-warning border-0 bg-light">
                                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                    <strong>Nenhum produto encontrado</strong>
                                </div>
                            `);
                        }
                        // Mostrar o botão de reload, pois não há produtos
                        $('#reload-produtos').show();
                    }
                },
                error: function(error) {
                    console.error("Erro ao buscar produtos:", error);
                    $('#select-status').html(`
                        <div class="alert alert-danger border-0 bg-light">
                            <i class="fas fa-times-circle me-2 text-danger"></i>
                            <strong>Erro ao buscar produtos</strong>
                        </div>
                    `);
                    // Mostrar o botão de reload em caso de erro
                    $('#reload-produtos').show();
                }
            });
        }

        // Eventos para os campos de busca com debounce
        let timer;
        $('#busca-descricao, #busca-codigo').on('input', function() {
            clearTimeout(timer);

            // Se o campo de busca por descrição estiver vazio, limpar os resultados e o campo NCM
            if ($('#busca-descricao').val() === '') {
                //console.log("Campo de busca vazio, limpando resultados e NCM");
                $('#busca-codigo').val(''); // Limpar também o campo de NCM
                $('#select-status').text('Digite um produto para buscar');
                $('#produto-unidade').val(''); // Limpar também a unidade

                // Limpar a lista de produtos no select
                const defaultOption = $('#produto-select option[value=""]').clone();
                $('#produto-select').empty().append(defaultOption).trigger('change');

                // Esconder o resumo se existir
                if ($('#resumo-produtos').length) {
                    $('#resumo-produtos').addClass('d-none');
                }

                // Mostrar mensagem informativa
                $('#sem-produtos-alert').removeClass('d-none');

                return; // Não realizar busca se o campo estiver vazio
            }

            timer = setTimeout(realizarBusca, 500); // Debounce de 500ms
        });

        // Evento do botão de limpar busca
        $('#limpar-busca').on('click', function() {
            //  console.log("Limpando campo de busca e NCM");

            // Limpar os campos de entrada
            $('#busca-descricao').val('').focus();
            $('#busca-codigo').val(''); // Limpar o campo de NCM
            $('#produto-unidade').val(''); // Limpar também a unidade
            $('#select-status').html(`
                <div class="alert alert-info border-0 bg-light">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Digite o nome de um produto para buscar
                </div>
            `);

            // Limpar a descrição do Gemini
            ultimaDescricaoGemini = '';

            // Destruir instância anterior de Select2
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }

            // Limpar a lista de produtos no select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));

            // Inicializar o Select2 novamente
            inicializarSelect2();

            // Mostrar mensagem informativa
            $('#sem-produtos-alert').removeClass('d-none');

            // Também limpar os valores dos campos relacionados
            $('#produto-valor').val('0.00');
            $('#produto-quantidade').val('1');
        });

        // Evento do botão de recarregar produtos
        $('#reload-produtos').on('click', function() {
            $('#select-status').html(`
                <div class="alert alert-info border-0 bg-light">
                    <i class="fas fa-spinner fa-spin me-2 text-primary"></i>
                    Recarregando produtos...
                </div>
            `);
            $(this).prop('disabled', true).addClass('disabled');

            // Limpar os campos de busca
            $('#busca-descricao').val('');
            $('#busca-codigo').val(''); // Campo para busca de NCM

            inicializarSelect2();

            setTimeout(() => {
                $(this).prop('disabled', false).removeClass('disabled');
            }, 2000);
        });

        // Função para atualizar o resumo de produtos
        function atualizarResumo() {
            valorTotal = 0;
            pesoTotal = 0;

            // Garantir que cada produto tenha valor_unitario
            produtos.forEach(function(produto) {
                // Adicionar valor_unitario se não existir
                if (!produto.valor_unitario && produto.valor !== undefined) {
                    produto.valor_unitario = produto.valor;
                }

                valorTotal += produto.valor * produto.quantidade;
                pesoTotal += produto.peso * produto.quantidade;
            });

            // Adicionar o peso das caixas
            caixas.forEach(function(caixa) {
                pesoTotal += parseFloat(caixa.peso);
            });

            $('#valor-total').text(valorTotal.toFixed(2));
            $('#peso-total').text(pesoTotal.toFixed(2));

            // Atualizando os campos ocultos para envio
            $('#produtos-json').val(JSON.stringify(produtos));
            $('#caixas-json').val(JSON.stringify(caixas));
            $('#valor-total-input').val(valorTotal.toFixed(2));
            $('#peso-total-input').val(pesoTotal.toFixed(2));

            // Atualizar os campos ocultos de dimensões com a primeira caixa (se existir)
            if (caixas.length > 0) {
                $('#altura-hidden').val(caixas[0].altura);
                $('#largura-hidden').val(caixas[0].largura);
                $('#comprimento-hidden').val(caixas[0].comprimento);
                $('#peso-caixa-hidden').val(caixas[0].peso);
            }

            // Mostrar ou esconder o resumo de produtos
            if (produtos.length > 0) {
                $('#resumo-produtos').removeClass('d-none');
                $('#sem-produtos-alert').addClass('d-none');
            } else {
                $('#resumo-produtos').addClass('d-none');
                $('#sem-produtos-alert').removeClass('d-none');
            }

            // Mostrar ou esconder o resumo de caixas
            if (caixas.length > 0) {
                $('#resumo-caixas').removeClass('d-none');
                $('#sem-caixas-alert').addClass('d-none');
            } else {
                $('#resumo-caixas').addClass('d-none');
                $('#sem-caixas-alert').removeClass('d-none');
            }
        }

        // Função para renderizar os cards de produtos
        function renderizarProdutos() {
            const container = $('#produtos-cards');
            container.empty();

            produtos.forEach(function(produto, index) {
                const card = `
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                        <div class="card h-100 border-0 shadow-lg hover-shadow" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-white">
                                        <i class="fas fa-box me-2"></i>${produto.nome.length > 30 ? produto.nome.substring(0, 30) + '...' : produto.nome}
                                    </h6>
                                    <span class="badge bg-white text-primary fw-bold">#${index + 1}</span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-semibold">
                                            <i class="fas fa-barcode me-1"></i>NCM
                                        </small>
                                        <strong class="text-dark fs-6">${produto.codigo || 'N/A'}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-semibold">
                                            <i class="fas fa-balance-scale me-1"></i>Unidade
                                        </small>
                                        <strong class="text-dark fs-6">${produto.unidade || 'UN'}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-semibold">
                                            <i class="fas fa-weight-hanging me-1"></i>Peso Unit.
                                        </small>
                                        <strong class="text-dark fs-6">${produto.peso} kg</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block fw-semibold">
                                            <i class="fas fa-dollar-sign me-1"></i>Valor Unit.
                                        </small>
                                        <strong class="text-success fs-6">R$ ${produto.valor.toFixed(2)}</strong>
                                    </div>
                                </div>
                                
                                <div class="bg-light rounded-3 p-3 mb-3" style="border: 1px solid #e9ecef;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-semibold">Quantidade:</span>
                                        <div class="btn-group btn-group-sm" role="group" style="max-width: 120px;">
                                            <button type="button" class="btn btn-outline-primary btn-diminuir" data-index="${index}" style="border-radius: 6px 0 0 6px;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="btn btn-primary disabled px-3 fw-bold" style="border-radius: 0; min-width: 50px;">${produto.quantidade}</span>
                                            <button type="button" class="btn btn-outline-primary btn-aumentar" data-index="${index}" style="border-radius: 0 6px 6px 0;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-end">
                                        <small class="text-muted d-block fw-semibold">Subtotal</small>
                                        <strong class="text-success fs-5">R$ ${(produto.valor * produto.quantidade).toFixed(2)}</strong>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover" data-index="${index}" style="border-radius: 8px;">
                                        <i class="fas fa-trash me-1"></i>Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });

            // Adicionar eventos após renderizar
            $('.btn-diminuir').on('click', function() {
                const index = $(this).data('index');
                if (produtos[index].quantidade > 1) {
                    produtos[index].quantidade--;
                    renderizarProdutos();
                    atualizarResumo();
                }
            });

            $('.btn-aumentar').on('click', function() {
                const index = $(this).data('index');
                produtos[index].quantidade++;
                renderizarProdutos();
                atualizarResumo();
            });

            $('.btn-remover').on('click', function() {
                const index = $(this).data('index');
                produtos.splice(index, 1);
                renderizarProdutos();
                atualizarResumo();
            });
        }

        // Evento de adicionar produto
        $('#adicionar-produto').on('click', function() {
            // Verificar se o Select2 está inicializado e se há dados
            let produtoSelecionado = null;
            
            try {
                // Verificar se o elemento existe e se o Select2 está inicializado
                if ($('#produto-select').length && $('#produto-select').hasClass('select2-hidden-accessible')) {
                    const select2Data = $('#produto-select').select2('data');
                    if (select2Data && select2Data.length > 0) {
                        produtoSelecionado = select2Data[0];
                    }
                }
            } catch (error) {
                console.error('Erro ao acessar dados do Select2:', error);
            }

            // Se não conseguiu pegar do Select2, tentar pegar dos campos de busca
            if (!produtoSelecionado) {
                const descricao = $('#busca-descricao').val();
                const codigo = $('#busca-codigo').val();
                
                if (descricao && codigo) {
                    produtoSelecionado = {
                        id: codigo,
                        codigo: codigo,
                        text: descricao,
                        nome: descricao
                    };
                }
            }

            if (produtoSelecionado && produtoSelecionado.id) {
                //console.log("Produto selecionado:", produtoSelecionado);

                const id = produtoSelecionado.id;
                const codigo = produtoSelecionado.codigo || id;
                const nome = produtoSelecionado.text || produtoSelecionado.nome;
                const peso = produtoSelecionado.peso || 0.5;
                const valorInformado = parseFloat($('#produto-valor').val()) || 0;
                const unidade = $('#produto-unidade').val();
                const quantidade = parseInt($('#produto-quantidade').val());

                // Validar valor
                if (isNaN(valorInformado) || valorInformado < 0) {
                    alert('Por favor, informe um valor válido para o produto.');
                    $('#produto-valor').focus();
                    return;
                }

                // Armazenar o produto em uma variável global para uso após a confirmação
                produtoEmConfirmacao = {
                    id: id,
                    codigo: codigo,
                    nome: nome,
                    peso: peso,
                    valor: valorInformado,
                    quantidade: quantidade,
                    unidade: unidade
                };

                //console.log("Produto a ser confirmado:", produtoEmConfirmacao);

                // Preencher as informações no modal
                $('#modal-produto-nome').text(nome.split(' (NCM:')[0]); // Remover a parte do NCM do nome
                $('#modal-produto-ncm').text(codigo);
                $('#modal-produto-valor').text('R$ ' + valorInformado.toFixed(2));
                $('#modal-produto-unidade').text(unidade || 'Não especificada');
                $('#modal-produto-quantidade').text(quantidade);
                $('#modal-produto-total').text('R$ ' + (valorInformado * quantidade).toFixed(2));

                // Exibir o modal
                const modal = new bootstrap.Modal(document.getElementById('confirmarProdutoModal'));
                modal.show();
            } else {
                // Se não houver produto selecionado
                alert('Por favor, selecione um produto antes de adicionar.');
            }
        });

        // Evento para confirmar a adição do produto
        $('#confirmarProdutoBtn').on('click', function() {
            if (produtoEmConfirmacao) {
                // Verificar se o produto já existe
                const existingIndex = produtos.findIndex(p => p.id === produtoEmConfirmacao.id);

                if (existingIndex !== -1) {
                    // Se existir, atualiza a quantidade
                    produtos[existingIndex].quantidade += produtoEmConfirmacao.quantidade;
                    //console.log("Atualizada quantidade do produto existente:", produtos[existingIndex]);
                } else {
                    // Se não existir, adiciona
                    produtos.push(produtoEmConfirmacao);
                    //  console.log("Novo produto adicionado:", produtoEmConfirmacao);
                }

                // Limpar completamente todos os campos de produto
                limparCamposProduto();

                // Renderizar produtos e atualizar resumo
                renderizarProdutos();
                atualizarResumo();

                // Fechar o modal
                bootstrap.Modal.getInstance(document.getElementById('confirmarProdutoModal')).hide();

                // Limpar o produto em confirmação
                produtoEmConfirmacao = null;
            }
        });

        // Evento para editar o produto
        $('#editarProdutoBtn').on('click', function() {
            // Apenas fechar o modal para edição
            bootstrap.Modal.getInstance(document.getElementById('confirmarProdutoModal')).hide();
            // Os dados permanecem nos campos para edição
        });

        // Evento para cancelar a adição do produto
        $('#cancelarProdutoBtn').on('click', function() {
            // Limpar todos os campos de produto
            limparCamposProduto();

            // Limpar o produto em confirmação
            produtoEmConfirmacao = null;
        });

        // Função para limpar completamente todos os campos de produto
        function limparCamposProduto() {
            // Limpar a seleção do produto
            $('#produto-select').val(null).trigger('change');

            // Limpar busca por descrição e código
            $('#busca-descricao').val('');
            $('#busca-codigo').val(''); // Garantir que o NCM seja limpo

            // Limpar campos de quantidade, valor e unidade
            $('#produto-quantidade').val(1);
            $('#produto-valor').val(0.00);
            $('#produto-unidade').val('');

            // Limpar descrição do Gemini
            ultimaDescricaoGemini = '';
            $('#descricao-gemini-container').addClass('d-none');

            // Limpar mensagem de status
            $('#select-status').text('Digite um produto para buscar');
        }

        // Renderizar as caixas adicionadas
        function renderizarCaixas() {
            const container = $('#caixas-cards');
            container.empty();

            caixas.forEach(function(caixa, index) {
                const volume = (caixa.altura * caixa.largura * caixa.comprimento / 1000).toFixed(2);
                const card = `
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-header bg-secondary bg-opacity-10 border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-secondary">
                                        <i class="fas fa-cube me-2"></i>Caixa #${index + 1}
                                    </h6>
                                    <span class="badge bg-secondary">${index + 1}</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-arrows-alt-v me-1"></i>Altura
                                        </small>
                                        <strong class="text-dark">${caixa.altura} cm</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-arrows-alt-h me-1"></i>Largura
                                        </small>
                                        <strong class="text-dark">${caixa.largura} cm</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-arrows-alt-h me-1"></i>Comprimento
                                        </small>
                                        <strong class="text-dark">${caixa.comprimento} cm</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-weight-hanging me-1"></i>Peso
                                        </small>
                                        <strong class="text-dark">${caixa.peso} kg</strong>
                                    </div>
                                </div>
                                
                                <div class="bg-light rounded p-2 mb-3">
                                    <div class="text-center">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calculator me-1"></i>Volume
                                        </small>
                                        <strong class="text-info fs-6">${volume} litros</strong>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover-caixa" data-index="${index}">
                                        <i class="fas fa-trash me-1"></i>Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });

            // Atualizar contador de caixas
            $('#total-caixas').text(caixas.length);

            // Adicionar eventos após renderizar
            $('.btn-remover-caixa').on('click', function() {
                const index = $(this).data('index');
                caixas.splice(index, 1);
                renderizarCaixas();
                atualizarResumo();

                // Atualizar visualização de alertas
                if (caixas.length === 0) {
                    $('#sem-caixas-alert').removeClass('d-none');
                    $('#resumo-caixas').addClass('d-none');
                }
            });
        }

        // Evento de adicionar caixa
        $('#adicionar-caixa').on('click', function() {
            const altura = parseFloat($('#altura').val());
            const largura = parseFloat($('#largura').val());
            const comprimento = parseFloat($('#comprimento').val());
            const peso = parseFloat($('#peso_caixa').val());

            // Validação básica
            if (isNaN(altura) || isNaN(largura) || isNaN(comprimento) || isNaN(peso) ||
                altura <= 0 || largura <= 0 || comprimento <= 0 || peso <= 0) {
                showAlert('Por favor, preencha todas as dimensões da caixa com valores válidos.', 'warning');
                return;
            }

            // Adicionar a caixa
            const caixa = {
                altura: altura,
                largura: largura,
                comprimento: comprimento,
                peso: peso
            };

            caixas.push(caixa);

            // Atualizar também os campos ocultos com os valores da primeira caixa
            if (caixas.length === 1) {
                $('#altura-hidden').val(altura);
                $('#largura-hidden').val(largura);
                $('#comprimento-hidden').val(comprimento);
                $('#peso-caixa-hidden').val(peso);
            }

            // Resetar os valores para adicionar nova caixa
            $('#altura').val(10);
            $('#largura').val(20);
            $('#comprimento').val(30);
            $('#peso_caixa').val(0.5);

            // Renderizar as caixas e atualizar o resumo
            renderizarCaixas();
            atualizarResumo();
        });

        // Função para preencher o select de países
        function carregarPaises() {
            $('.pais-select').each(function() {
                const select = $(this);
                select.find('option:not(:first)').remove();

                paises.forEach(function(pais) {
                    select.append($('<option>', {
                        value: pais.id,
                        text: pais.nome
                    }));
                });
            });
        }

        // Função para preencher o select de estados com base no país selecionado
        function carregarEstados(paisId, estadoSelect) {
            const paisEstados = estados[paisId] || [];
            estadoSelect.find('option:not(:first)').remove();
            estadoSelect.prop('disabled', paisEstados.length === 0);

            if (paisEstados.length === 0) {
                estadoSelect.find('option:first').text('Nenhum estado disponível para este país');
                return;
            }

            estadoSelect.find('option:first').text('Selecione um estado');

            paisEstados.forEach(function(estado) {
                estadoSelect.append($('<option>', {
                    value: estado.id,
                    text: estado.nome
                }));
            });
        }

        // Eventos para os selects de país e estado
        $('.pais-select').on('change', function() {
            const paisId = $(this).val();
            const formGroup = $(this).closest('.card-body');
            const estadoSelect = formGroup.find('.estado-select');
            const prefixo = $(this).attr('id').split('_')[0]; // Obter prefixo (origem ou destino)

            // Limpar campo de cidade
            $(`#${prefixo}_cidade`).val('');

            carregarEstados(paisId, estadoSelect);
        });

        $('.estado-select').on('change', function() {
            const estadoId = $(this).val();
            const prefixo = $(this).attr('id').split('_')[0]; // Obter prefixo (origem ou destino)

            // Limpar campo de cidade quando estado mudar
            $(`#${prefixo}_cidade`).val('');
        });

        // Função para buscar CEP via API ViaCEP (Brasil)
        function buscarCEP(cep, prefixo) {
            if (cep.length < 8) {
                alert('CEP inválido. Por favor, digite um CEP válido com 8 dígitos.');
                return;
            }

            // Remove caracteres não numéricos
            cep = cep.replace(/\D/g, '');

            // Mostrar indicador de carregamento
            $(`#${prefixo}_endereco`).val('Buscando...');

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/?callback=?`, function(data) {
                if (!data.erro) {
                    // Preencher o endereço
                    $(`#${prefixo}_endereco`).val(data.logradouro + (data.complemento ? ', ' + data.complemento : '') + ' - ' + data.bairro);

                    // Encontrar o país (Brasil)
                    const paisBrasil = paises.find(pais => pais.id === 'BR');
                    if (paisBrasil) {
                        // Selecionar Brasil como país
                        $(`#${prefixo}_pais`).val(paisBrasil.id).trigger('change');

                        // Aguardar o carregamento dos estados
                        setTimeout(function() {
                            // Encontrar o estado pelo código UF
                            const estado = estados['BR'].find(estado =>
                                estado.id === data.uf
                            );

                            if (estado) {
                                // Selecionar o estado
                                $(`#${prefixo}_estado`).val(estado.id).trigger('change');

                                // Aguardar o carregamento das cidades
                                setTimeout(function() {
                                    $(`#${prefixo}_cidade`).val(data.localidade);
                                }, 800);
                            }
                        }, 300);
                    }
                } else {
                    alert('CEP não encontrado. Por favor, digite o endereço manualmente.');
                    $(`#${prefixo}_endereco`).val('');
                }
            }).fail(function(jqxhr, textStatus, error) {
                //console.error("Erro ao buscar CEP:", error);
                alert('Erro ao buscar o CEP. Por favor, digite o endereço manualmente.');
                $(`#${prefixo}_endereco`).val('');
            });

            // Função auxiliar para preencher o campo de cidade, independente do tipo
            function preencherCidade(prefixo, nomeCidade) {
                // Verificar se existe um campo de seleção ou um campo de texto
                const selectCidade = $(`#${prefixo}_cidade`);
                const inputCidade = $(`#${prefixo}_cidade_texto`);

                // Se temos um campo de entrada de texto, é simples
                if (inputCidade.length > 0) {
                    inputCidade.val(nomeCidade);
                    return;
                }

                // Se temos um select, precisamos verificar se a cidade está na lista
                if (selectCidade.length > 0) {
                    let cidadeEncontrada = false;

                    // Verificar se o select já tem opções carregadas
                    if (selectCidade.find('option').length > 1) {
                        // Tentar encontrar a cidade pelo nome
                        selectCidade.find('option').each(function() {
                            if ($(this).text().toLowerCase() === nomeCidade.toLowerCase()) {
                                selectCidade.val($(this).val()).change();
                                cidadeEncontrada = true;
                                return false; // Break
                            }
                        });

                        // Se não encontrou, adicionar a cidade como opção
                        if (!cidadeEncontrada) {
                            const novaOpcao = $('<option>', {
                                value: 'custom_' + nomeCidade.replace(/\s/g, '_').toLowerCase(),
                                text: nomeCidade
                            });

                            selectCidade.append(novaOpcao);
                            selectCidade.val(novaOpcao.val()).change();
                        }
                    } else {
                        // O select ainda não tem opções, esperar mais ou transformar em input
                        //console.log('Select de cidade ainda não carregou, esperando...');

                        // Verificar novamente após um curto período
                        setTimeout(function() {
                            // Se ainda não carregou, tentar uma última vez
                            if (selectCidade.find('option').length <= 1) {
                                // Verificar se o container existe
                                const container = $(`#${prefixo}_cidade_container`);
                                if (container.length > 0) {
                                    // Transformar em input
                                    container.empty().html(`
                                        <input type="text" class="form-control" id="${prefixo}_cidade_texto" 
                                               name="${prefixo}_cidade" value="${nomeCidade}" required>
                                    `);
                                    container.after(`<small class="text-muted">Campo convertido para entrada de texto.</small>`);
                                }
                            } else {
                                // Tentamos novamente com o select agora preenchido
                                $(`#${prefixo}_cidade`).val(nomeCidade);
                            }
                        }, 500);
                    }
                }
            }
        }

        // Eventos para buscar endereço pelo CEP
        $('#origem_buscar_cep').on('click', function() {
            const cep = $('#origem_cep').val();
            buscarCEP(cep, 'origem');
        });

        $('#destino_buscar_cep').on('click', function() {
            const cep = $('#destino_cep').val();
            buscarCEP(cep, 'destino');
        });

        // Máscara para CEP
        $('#origem_cep, #destino_cep').on('input', function() {
            const value = $(this).val().replace(/\D/g, '');
            if (value.length <= 5) {
                $(this).val(value);
            } else {
                $(this).val(value.substring(0, 5) + '-' + value.substring(5, 8));
            }
        });

        // Inicializar os selects de países
        carregarPaises();

        // Evento para consultar serviços de entrega
        $('#consultar-servicos').on('click', function() {
            // Validar se há produtos
            if (produtos.length === 0) {
                showAlert('Por favor, adicione pelo menos um produto para o envio.', 'warning');
                return false;
            }

            // Validar se há caixas
            if (caixas.length === 0) {
                showAlert('Por favor, adicione pelo menos uma caixa para o envio.', 'warning');
                return false;
            }

            // Validar tipo de envio
            if (!$('#tipo_envio').val()) {
                showAlert('Por favor, selecione o tipo de envio.', 'warning');
                return false;
            }

            // Validar tipo de pessoa
            if (!$('#tipo_pessoa').val()) {
                showAlert('Por favor, selecione o tipo de pessoa.', 'warning');
                return false;
            }

            // Validar campos de origem e destino
            if (!$('#origem_nome').val() || !$('#origem_endereco').val() || !$('#origem_cidade').val() ||
                !$('#origem_estado').val() || !$('#origem_cep').val() || !$('#origem_pais').val() ||
                !$('#origem_telefone').val() || !$('#origem_email').val()) {
                showAlert('Por favor, preencha todos os campos de origem.', 'warning');
                return false;
            }

            if (!$('#destino_nome').val() || !$('#destino_endereco').val() || !$('#destino_cidade').val() ||
                !$('#destino_estado').val() || !$('#destino_cep').val() || !$('#destino_pais').val() ||
                !$('#destino_telefone').val() || !$('#destino_email').val()) {
                showAlert('Por favor, preencha todos os campos de destino.', 'warning');
                return false;
            }

            // Mostrar o loader e esconder os resultados anteriores
            $('#cotacao-loader').show();
            $('#servicos-lista').hide();
            $('#servicos-info').hide();

            // Preparar dados para a cotação (usar as mesmas informações que serão usadas no envio)
            const dadosCotacao = {
                origem: $('#origem_cep').val(),
                destino: $('#destino_cep').val(),
                altura: caixas[0].altura,
                largura: caixas[0].largura,
                comprimento: caixas[0].comprimento,
                peso: pesoTotal,
                _token: $('input[name="_token"]').val()
            };

            //console.log('Consultando serviços com os dados:', dadosCotacao);

            // Fazer requisição para a API de cotação
            $.ajax({
                url: '/calcular-cotacao',
                type: 'POST',
                data: dadosCotacao,
                success: function(response) {
                    // Esconder o loader
                    $('#cotacao-loader').hide();

                    //console.log('Resposta da cotação:', response);

                    if (response.success) {
                        // Exibir as opções de serviço
                        exibirServicos(response);
                    } else {
                        // Mostrar erro
                        showAlert('Erro ao consultar serviços: ' + (response.message || 'Tente novamente mais tarde.'), 'danger');
                        $('#servicos-info').show();
                    }
                },
                error: function(xhr) {
                    $('#cotacao-loader').hide();

                    //console.error('Erro na requisição AJAX:', xhr);

                    // Tentar extrair mensagem de erro
                    let errorMessage = 'Erro ao consultar serviços. Tente novamente mais tarde.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        //console.error('Erro ao parsear resposta:', e);
                    }

                    showAlert(errorMessage, 'danger');
                    $('#servicos-info').show();
                }
            });
        });

        // Função para exibir os serviços disponíveis
        function exibirServicos(response) {
            const servicos = response.cotacoesFedEx;

            // Verificar se existem serviços para exibir
            if (!servicos || servicos.length === 0) {
                $('#servicos-lista').html('<div class="alert alert-warning">Nenhum serviço disponível para as informações fornecidas.</div>');
                $('#servicos-lista').show();
                return;
            }

            // Montar o HTML para mostrar os serviços disponíveis
            let html = '<h4 class="mb-3">Opções de Serviço</h4>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-striped table-hover">';
            html += '<thead><tr>';
            html += '<th>Serviço</th>';
            html += '<th>Tempo de Entrega</th>';
            html += '<th>Valor (USD)</th>';
            html += '<th>Valor (BRL)</th>';
            html += '<th>Selecionar</th>';
            html += '</tr></thead><tbody>';

            servicos.forEach(function(servico) {
                // Processar o valor em BRL
                const valorBRL = servico.valorTotalBRL || 'N/A';

                html += '<tr>';
                html += '<td>' + servico.servico + '</td>';
                html += '<td>' + (servico.tempoEntrega || 'Consultar') + '</td>';
                html += '<td>' + servico.valorTotal + ' ' + servico.moeda + '</td>';
                html += '<td>R$ ' + valorBRL + '</td>';
                html += '<td><button type="button" class="btn btn-sm btn-primary selecionar-servico" ' +
                    'data-servico="' + servico.servicoTipo + '" ' +
                    'data-nome="' + servico.servico + '" ' +
                    'data-valor-usd="' + servico.valorTotal + '" ' +
                    'data-valor-brl="' + valorBRL + '" ' +
                    'data-moeda="' + servico.moeda + '">Selecionar</button></td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';

            // Adicionar uma mensagem se for uma simulação
            if (response.simulado) {
                html += '<div class="alert alert-info mt-3">';
                html += '<i class="fas fa-info-circle me-2"></i> ' + (response.mensagem || 'Cotação simulada para obter valores aproximados.');
                html += '</div>';
            }

            // Exibir os serviços
            $('#servicos-lista').html(html).show();

            // Evento para quando um serviço é selecionado
            $('.selecionar-servico').on('click', function() {
                const servicoTipo = $(this).data('servico');
                const servicoNome = $(this).data('nome');
                const valorUSD = $(this).data('valor-usd');
                const valorBRL = $(this).data('valor-brl');
                const moeda = $(this).data('moeda');

                // Destacar o serviço selecionado
                $('.selecionar-servico').removeClass('btn-success').addClass('btn-primary').text('Selecionar');
                $(this).removeClass('btn-primary').addClass('btn-success').text('Selecionado');

                // Criar um campo oculto para armazenar o serviço selecionado
                if ($('#servico_entrega').length) {
                    $('#servico_entrega').val(servicoTipo);
                } else {
                    // Se não existir, criar o campo
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'servico_entrega',
                        name: 'servico_entrega',
                        value: servicoTipo
                    }).appendTo('#envio-form');
                }

                // Mostrar mensagem de confirmação
                $('#servicos-lista').append(
                    '<div class="alert alert-success mt-3">' +
                    '<i class="fas fa-check-circle me-2"></i> Serviço <strong>' + servicoNome + '</strong> selecionado. Continue para selecionar o método de pagamento.' +
                    '</div>'
                );

                // Mostrar a seção de métodos de pagamento
                $('#pagamento-section').show();

                // Preencher os valores de pagamento
                // Limpar o valor BRL, removendo "R$", espaços e convertendo vírgula para ponto
                const valorNumerico = valorBRL.replace(/[^\d,]/g, '').replace(',', '.');
                $('#payment_amount').val(valorNumerico);

                // Atualizar o resumo do pagamento
                $('#payment-service-name').text(servicoNome);
                $('#payment-service-value').text('R$ ' + valorBRL);

                // Rolar até a seção de pagamento
                $('html, body').animate({
                    scrollTop: $('#pagamento-section').offset().top - 100
                }, 500);
            });
        }

        // Eventos para seleção de método de pagamento
        $(document).on('click', '.select-payment-method', function() {
            const method = $(this).data('method');

            // Destacar o método selecionado
            $('.payment-method-card').removeClass('border-primary');
            $('.select-payment-method').removeClass('btn-primary').addClass('btn-outline-primary');

            $(this).closest('.payment-method-card').addClass('border-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');

            // Armazenar o método selecionado
            $('#payment_method').val(method);

            // Mostrar formulário específico de acordo com o método
            if (method === 'credit_card') {
                $('#credit-card-form').show();
            } else {
                $('#credit-card-form').hide();
            }

            // Atualizar o nome do método de pagamento no resumo
            let paymentMethodName = 'Desconhecido';
            if (method === 'boleto') paymentMethodName = 'Boleto Bancário';
            if (method === 'pix') paymentMethodName = 'Pix';
            if (method === 'credit_card') paymentMethodName = 'Cartão de Crédito';

            $('#payment-method-name').text(paymentMethodName);

            // Mostrar o resumo do pagamento e o botão de envio
            $('#payment-summary').show();
            $('#payment-total-value').text($('#payment-service-value').text());
            $('#submit-button').show();
        });

        // Formatar campos de cartão de crédito
        $('#card_number').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = '';

            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }

            $(this).val(formattedValue);
        });

        $('#card_cvv').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 4) value = value.substring(0, 4);
            $(this).val(value);
        });

        $('#card_cpf').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = '';

            if (value.length <= 3) {
                formattedValue = value;
            } else if (value.length <= 6) {
                formattedValue = value.substring(0, 3) + '.' + value.substring(3);
            } else if (value.length <= 9) {
                formattedValue = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
            } else {
                formattedValue = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9, 11);
            }

            $(this).val(formattedValue);
        });

        // Evento de submissão do formulário
        $('#envio-form').on('submit', function(e) {
            e.preventDefault();

            // Validar se há produtos
            if (produtos.length === 0) {
                showAlert('Por favor, adicione pelo menos um produto para o envio.', 'warning');
                return false;
            }

            // Validar se há caixas
            if (caixas.length === 0) {
                showAlert('Por favor, adicione pelo menos uma caixa para o envio.', 'warning');
                return false;
            }

            // Verificar se os campos ocultos de dimensões estão preenchidos
            const altura = $('#altura-hidden').val();
            const largura = $('#largura-hidden').val();
            const comprimento = $('#comprimento-hidden').val();
            const pesoCaixa = $('#peso-caixa-hidden').val();

            if (!altura || !largura || !comprimento || !pesoCaixa) {
                //console.error('Campos de dimensão não preenchidos:', {
                //    altura: altura,
                //    largura: largura,
                //    comprimento: comprimento,
                //    pesoCaixa: pesoCaixa
                //});

                // Tentar preencher com os valores da primeira caixa
                if (caixas.length > 0) {
                    $('#altura-hidden').val(caixas[0].altura);
                    $('#largura-hidden').val(caixas[0].largura);
                    $('#comprimento-hidden').val(caixas[0].comprimento);
                    $('#peso-caixa-hidden').val(caixas[0].peso);

                    //console.log('Dimensões corrigidas com a primeira caixa:', {
                    //    altura: caixas[0].altura,
                    //    largura: caixas[0].largura,
                    //    comprimento: caixas[0].comprimento,
                    //    pesoCaixa: caixas[0].peso
                    //});
                } else {
                    showAlert('Erro ao processar dimensões da caixa. Por favor, tente novamente.', 'danger');
                    return false;
                }
            }

            // Verificar o método de entrega
            if (!$('#servico_entrega').val()) {
                showAlert('Por favor, selecione um método de entrega.', 'warning');
                return false;
            }

            // Verificar o método de pagamento
            if (!$('#payment_method').val()) {
                showAlert('Por favor, selecione um método de pagamento.', 'warning');
                return false;
            }

            // Validar campos do cartão de crédito se for o método selecionado
            if ($('#payment_method').val() === 'credit_card') {
                if (!$('#card_name').val() || !$('#card_number').val() ||
                    !$('#card_expiry_month').val() || !$('#card_expiry_year').val() ||
                    !$('#card_cvv').val() || !$('#card_cpf').val()) {
                    showAlert('Por favor, preencha todos os dados do cartão de crédito.', 'warning');
                    return false;
                }
            }

            // Log para debug
            //console.log('Enviando dados:', {
            //    produtos: JSON.parse($('#produtos-json').val() || '[]'),
            //    caixas: JSON.parse($('#caixas-json').val() || '[]'),
            //    valorTotal: $('#valor-total-input').val(),
            //    pesoTotal: $('#peso-total-input').val(),
            //    dimensoes: {
            //        altura: $('#altura-hidden').val(),
            //        largura: $('#largura-hidden').val(),
            //        comprimento: $('#comprimento-hidden').val(),
            //        pesoCaixa: $('#peso-caixa-hidden').val()
            //    },
            //    servicoEntrega: $('#servico_entrega').val(),
            //    metodoPagamento: $('#payment_method').val(),
            //    valorPagamento: $('#payment_amount').val(),
            //    tipoEnvio: $('#tipo_envio').val(),
            //    tipoPessoa: $('#tipo_pessoa').val()
            //});

            // Se passou pela validação, enviar o formulário via AJAX
            $.ajax({
                url: "{{ route('api.envio.processar') }}",
                method: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    // Desabilitar o botão e mostrar indicador de carregamento
                    $('#submit-button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processando...');

                    // Log para verificar o valor do payment_amount
                    //  console.log('Valor do payment_amount:', $('#payment_amount').val());
                    //console.log('Tipo do payment_amount:', typeof $('#payment_amount').val());
                },
                success: function(response) {
                    // Habilitar o botão novamente
                    $('#submit-button').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Processar Envio');

                    if (response.success) {
                        // Exibir mensagem de sucesso
                        showAlert('Envio processado com sucesso! ' + response.message, 'success');
                    } else {
                        // Exibir mensagem de erro
                        showAlert('Erro ao processar envio: ' + response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    // Habilitar o botão novamente
                    $('#submit-button').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Processar Envio');

                    // Exibir mensagem de erro
                    let errorMessage = 'Erro ao processar envio. Tente novamente mais tarde.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        //console.error('Erro ao parsear resposta:', e);
                    }

                    showAlert(errorMessage, 'danger');
                }
            });
        });

        // Inicializar campos de telefone com máscara
        $('#origem_telefone, #destino_telefone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if ($(this).attr('id') === 'origem_telefone') {
                // Formato brasileiro: +55 11 98765-4321
                if (value.length <= 2) {
                    $(this).val('+' + value);
                } else if (value.length <= 4) {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2));
                } else if (value.length <= 8) {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2, 4) + ' ' + value.substring(4));
                } else {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2, 4) + ' ' +
                        value.substring(4, 9) + '-' + value.substring(9, 13));
                }
            } else {
                // Formato americano: +1 555 123-4567
                if (value.length <= 1) {
                    $(this).val('+' + value);
                } else if (value.length <= 4) {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1));
                } else if (value.length <= 7) {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1, 4) + ' ' + value.substring(4));
                } else {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1, 4) + ' ' +
                        value.substring(4, 7) + '-' + value.substring(7, 11));
                }
            }
        });

        // ===== WIZARD FUNCTIONS =====
        let etapaAtual = 1;
        const totalEtapas = 6;

        // Função para mostrar uma etapa específica
        function mostrarEtapa(etapa) {
            // Esconder todas as etapas
            $('[id^="step-"]').addClass('d-none');
            
            // Mostrar a etapa atual
            $('#step-' + etapa).removeClass('d-none');
            
            // Atualizar progresso do wizard
            atualizarProgressoWizard(etapa);
            
            // Scroll suave para o topo
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        }

        // Função para gerenciar o card informativo
        function gerenciarCardInformativo(etapa) {
            const infoCard = $('#info-card');
            const infoCardBadge = $('#info-card-badge');
            
            // Atualizar o badge com a etapa atual
            infoCardBadge.text('Etapa ' + etapa + ' de ' + totalEtapas);
            
            // Na primeira etapa: expandido
            if (etapa === 1) {
                infoCard.removeClass('collapsed').addClass('expanded');
                $('#info-card-content').show();
            } else {
                // Nas outras etapas: colapsado
                infoCard.removeClass('expanded').addClass('collapsed');
                $('#info-card-content').hide();
            }
        }

        // Evento de clique no cabeçalho do card informativo
        $('#info-card-header').on('click', function() {
            const infoCard = $('#info-card');
            const infoCardContent = $('#info-card-content');
            
            if (infoCard.hasClass('collapsed')) {
                // Expandir
                infoCard.removeClass('collapsed').addClass('expanded');
                infoCardContent.slideDown(300);
            } else {
                // Colapsar
                infoCard.removeClass('expanded').addClass('collapsed');
                infoCardContent.slideUp(300);
            }
        });

        // Inicializar wizard
        mostrarEtapa(etapaAtual);

        // Eventos dos botões do wizard
        $('#btn-step-1-next').on('click', function() {
            // Validar campos da etapa 1
            if (!$('#tipo_envio').val()) {
                showAlert('Por favor, selecione o tipo de envio.', 'warning');
                return;
            }
            if (!$('#tipo_pessoa').val()) {
                showAlert('Por favor, selecione o tipo de pessoa.', 'warning');
                return;
            }
            
            etapaAtual = 2;
            mostrarEtapa(etapaAtual);
        });

        $('#btn-step-2-next').on('click', function() {
            // Validar se há produtos e caixas
            if (produtos.length === 0) {
                showAlert('Por favor, adicione pelo menos um produto.', 'warning');
                return;
            }
            if (caixas.length === 0) {
                showAlert('Por favor, adicione pelo menos uma caixa.', 'warning');
                return;
            }
            
            etapaAtual = 3;
            mostrarEtapa(etapaAtual);
        });

        $('#btn-step-3-next').on('click', function() {
            // Função auxiliar para validar campo
            function validarCampo(campoId, nomeCampo) {
                const campo = $(campoId);
                const valor = campo.val() ? campo.val().trim() : '';
                if (!valor) {
                    console.log(`❌ Campo ${nomeCampo} está vazio:`, campoId, 'Valor:', campo.val());
                    return false;
                }
                console.log(`✅ Campo ${nomeCampo} está preenchido:`, campoId, 'Valor:', valor);
                return true;
            }
            
            console.log('=== INICIANDO VALIDAÇÃO DOS CAMPOS ===');
            
            // Validar campos de origem
            const camposOrigem = [
                { id: '#origem_nome', nome: 'Nome de Origem' },
                { id: '#origem_endereco', nome: 'Endereço de Origem' },
                { id: '#origem_cidade', nome: 'Cidade de Origem' },
                { id: '#origem_estado', nome: 'Estado de Origem' },
                { id: '#origem_cep', nome: 'CEP de Origem' },
                { id: '#origem_pais', nome: 'País de Origem' },
                { id: '#origem_telefone', nome: 'Telefone de Origem' },
                { id: '#origem_email', nome: 'Email de Origem' }
            ];
            
            // Validar campos de destino
            const camposDestino = [
                { id: '#destino_nome', nome: 'Nome de Destino' },
                { id: '#destino_endereco', nome: 'Endereço de Destino' },
                { id: '#destino_cidade', nome: 'Cidade de Destino' },
                { id: '#destino_estado', nome: 'Estado de Destino' },
                { id: '#destino_cep', nome: 'CEP de Destino' },
                { id: '#destino_pais', nome: 'País de Destino' },
                { id: '#destino_telefone', nome: 'Telefone de Destino' },
                { id: '#destino_email', nome: 'Email de Destino' }
            ];
            
            console.log('=== VALIDANDO CAMPOS DE ORIGEM ===');
            // Verificar campos de origem
            for (let campo of camposOrigem) {
                if (!validarCampo(campo.id, campo.nome)) {
                    showAlert(`Por favor, preencha o campo: ${campo.nome}`, 'warning');
                    $(campo.id).focus();
                    return;
                }
            }
            
            console.log('=== VALIDANDO CAMPOS DE DESTINO ===');
            // Verificar campos de destino
            for (let campo of camposDestino) {
                if (!validarCampo(campo.id, campo.nome)) {
                    showAlert(`Por favor, preencha o campo: ${campo.nome}`, 'warning');
                    $(campo.id).focus();
                    return;
                }
            }
            
            // Se chegou até aqui, todos os campos estão preenchidos
            console.log('🎉 Todos os campos estão preenchidos corretamente!');
            
            // Abrir modal de revisão
            const modal = new bootstrap.Modal(document.getElementById('modal-revisao-final'));
            modal.show();
            
            // Montar resumo dos dados
            montarResumoRevisao();
        });

        $('#btn-confirmar-revisao').on('click', function() {
            // Mostrar loader
            showAlert('<i class="fas fa-spinner fa-spin me-2"></i>Calculando cotações de envio...', 'info');
            
            // Coletar dados para cotação
            const dadosCotacao = {
                origem: $('#origem_cep').val(),
                destino: $('#destino_cep').val(),
                altura: parseFloat($('#altura').val()),
                largura: parseFloat($('#largura').val()),
                comprimento: parseFloat($('#comprimento').val()),
                peso: parseFloat($('#peso_caixa').val()),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Fazer requisição de cotação
            $.ajax({
                url: '/calcular-cotacao',
                method: 'POST',
                data: dadosCotacao,
                success: function(response) {
                    // Esconder alerta de loading
                    $('#alert-container').empty();
                    
                    if (response.status === 'success' && response.data.success) {
                        // Armazenar cotações na sessão ou variável global
                        window.cotacoesFedEx = response.data.cotacoesFedEx;
                        window.dadosCotacao = response.data;
                        
                        // Fechar modal de revisão
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modal-revisao-final'));
                        modal.hide();
                        
                        // Ir para etapa 5 (seleção de serviço)
                        etapaAtual = 5;
                        mostrarEtapa(etapaAtual);
                        
                        // Preencher as opções de serviço
                        preencherOpcoesServico(response.data.cotacoesFedEx);
                    } else {
                        showAlert('Erro ao calcular cotação: ' + (response.message || 'Serviço indisponível'), 'danger');
                    }
                },
                error: function(xhr) {
                    // Esconder alerta de loading
                    $('#alert-container').empty();
                    
                    let errorMessage = 'Erro ao calcular cotação';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'danger');
                }
            });
        });

        $('#btn-step-5-next').on('click', function() {
            // Validar se um serviço foi selecionado
            if (!window.servicoSelecionado) {
                showAlert('Por favor, selecione um serviço de entrega.', 'warning');
                return;
            }
            
            // Armazenar o serviço selecionado no formulário
            $('#servico_entrega').val(window.servicoSelecionado.tipo);
            
            // Adicionar informações do serviço selecionado ao resumo
            const servicoInfo = window.cotacoesFedEx.find(c => c.servicoTipo === window.servicoSelecionado.tipo);
            if (servicoInfo) {
                window.servicoInfo = servicoInfo;
            }
            
            etapaAtual = 6;
            mostrarEtapa(etapaAtual);
        });

        $('#btn-editar-etapas').on('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modal-revisao-final'));
            modal.hide();
            etapaAtual = 1;
            mostrarEtapa(etapaAtual);
        });

        // Função para montar o resumo da revisão
        function montarResumoRevisao() {
            let resumo = '<div class="row">';
            
            // Informações básicas
            resumo += '<div class="col-md-6"><h6><i class="fas fa-info-circle me-2"></i>Informações Básicas</h6>';
            resumo += '<ul class="list-unstyled">';
            resumo += '<li><strong>Tipo de Envio:</strong> ' + $('#tipo_envio option:selected').text() + '</li>';
            resumo += '<li><strong>Tipo de Pessoa:</strong> ' + $('#tipo_pessoa option:selected').text() + '</li>';
            resumo += '</ul></div>';
            
            // Produtos
            resumo += '<div class="col-md-6"><h6><i class="fas fa-box me-2"></i>Produtos (' + produtos.length + ')</h6>';
            resumo += '<ul class="list-unstyled">';
            produtos.forEach(function(produto) {
                resumo += '<li>' + produto.nome + ' - Qtd: ' + produto.quantidade + ' - R$ ' + (produto.valor * produto.quantidade).toFixed(2) + '</li>';
            });
            resumo += '</ul></div>';
            
            // Caixas
            resumo += '<div class="col-md-6"><h6><i class="fas fa-cube me-2"></i>Caixas (' + caixas.length + ')</h6>';
            resumo += '<ul class="list-unstyled">';
            caixas.forEach(function(caixa, index) {
                resumo += '<li>Caixa ' + (index + 1) + ': ' + caixa.altura + '×' + caixa.largura + '×' + caixa.comprimento + 'cm - ' + caixa.peso + 'kg</li>';
            });
            resumo += '</ul></div>';
            
            // Origem
            resumo += '<div class="col-md-6"><h6><i class="fas fa-map-marker-alt me-2"></i>Origem</h6>';
            resumo += '<ul class="list-unstyled">';
            resumo += '<li><strong>' + $('#origem_nome').val() + '</strong></li>';
            resumo += '<li>' + $('#origem_endereco').val() + '</li>';
            resumo += '<li>' + $('#origem_cidade').val() + ' - ' + $('#origem_estado').val() + '</li>';
            resumo += '<li>CEP: ' + $('#origem_cep').val() + '</li>';
            resumo += '</ul></div>';
            
            // Destino
            resumo += '<div class="col-md-6"><h6><i class="fas fa-map-marker me-2"></i>Destino</h6>';
            resumo += '<ul class="list-unstyled">';
            resumo += '<li><strong>' + $('#destino_nome').val() + '</strong></li>';
            resumo += '<li>' + $('#destino_endereco').val() + '</li>';
            resumo += '<li>' + $('#destino_cidade').val() + ' - ' + $('#destino_estado').val() + '</li>';
            resumo += '<li>CEP: ' + $('#destino_cep').val() + '</li>';
            resumo += '</ul></div>';
            
            resumo += '</div>';
            
            $('#resumo-revisao-final').html(resumo);
        }

        // ===== FIM WIZARD FUNCTIONS =====

        // Adicionar função para buscar CEP e preencher campos automaticamente
        $(document).ready(function() {
            // Função para mostrar alertas
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                // Verificar se já existe um container de alerta
                if ($('#alert-container-cep').length === 0) {
                    // Criar container de alerta antes do formulário
                    $('.card-body:first').prepend('<div id="alert-container-cep"></div>');
                }

                // Adicionar o alerta e rolar até ele
                $('#alert-container-cep').html(alertHtml);
                $('html, body').animate({
                    scrollTop: $('#alert-container-cep').offset().top - 100
                }, 500);

                // Auto-fechamento após 5 segundos para alertas
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }

            // Máscara para o campo de CEP de origem
            $('#origem_cep').on('input', function() {
                // Remove caracteres não numéricos
                let cep = $(this).val().replace(/\D/g, '');

                // Limita a 8 dígitos
                if (cep.length > 8) {
                    cep = cep.substring(0, 8);
                }

                // Formata o CEP com hífen após 5 dígitos
                if (cep.length > 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5);
                }

                // Atualiza o valor do campo
                $(this).val(cep);

                // Se tiver 8 dígitos (sem contar o hífen), busca o CEP
                if (cep.replace(/\D/g, '').length === 8) {
                    buscarCEP(cep, 'origem');
                }
            });

            // Máscara para o campo de CEP de destino
            $('#destino_cep').on('input', function() {
                // Remove caracteres não numéricos
                let cep = $(this).val().replace(/\D/g, '');

                // Limita a 8 dígitos
                if (cep.length > 8) {
                    cep = cep.substring(0, 8);
                }

                // Formata o CEP com hífen após 5 dígitos
                if (cep.length > 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5);
                }

                // Atualiza o valor do campo
                $(this).val(cep);

                // Se tiver 8 dígitos (sem contar o hífen), busca o CEP
                if (cep.replace(/\D/g, '').length === 8) {
                    buscarCEP(cep, 'destino');
                }
            });

            // Função para buscar informações do CEP usando a API ViaCEP
            function buscarCEP(cep, tipo) {
                // Remove caracteres não numéricos
                cep = cep.replace(/\D/g, '');

                // Verifica se o CEP tem 8 dígitos
                if (cep.length !== 8) {
                    return;
                }

                // Define os campos com base no tipo (origem ou destino)
                const campoEndereco = `#${tipo}_endereco`;
                const campoCidade = `#${tipo}_cidade`;
                const campoEstado = `#${tipo}_estado`;
                const campoComplemento = `#${tipo}_complemento`;
                const campoPais = `#${tipo}_pais`;

                // Mostra mensagem de carregamento
                $(campoEndereco).attr('placeholder', 'Buscando CEP...');

                // Primeira tentativa: API ViaCEP com JSONP para evitar problemas de CORS
                $.ajax({
                    url: `https://viacep.com.br/ws/${cep}/json/?callback=?`,
                    dataType: 'jsonp',
                    timeout: 3000, // 3 segundos de timeout
                    success: function(data) {
                        if (!data.erro) {
                            preencherCampos(data);
                        } else {
                            // Se a primeira API falhar, tentar a segunda
                            tentarApiAlternativa();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        //console.log('Erro na primeira tentativa:', textStatus, errorThrown);
                        // Se a primeira API falhar, tentar a segunda
                        tentarApiAlternativa();
                    }
                });

                // Função para preencher os campos com os dados do CEP
                function preencherCampos(data) {
                    //console.log(`Dados do CEP (${tipo}):`, data);

                    // Preenche os campos com os dados retornados
                    $(campoEndereco).val(data.logradouro || data.rua || '');
                    $(campoCidade).val(data.localidade || data.cidade || '');
                    $(campoEstado).val(data.uf || data.estado || '');

                    // Se for um CEP brasileiro, seleciona Brasil no país
                    if (tipo === 'destino') {
                        // Verifica se Brasil está na lista
                        if ($(campoPais).find('option[value="BR"]').length > 0) {
                            $(campoPais).val('BR');
                        }
                    }

                    // Se tiver complemento, preenche também
                    if (data.complemento) {
                        $(campoComplemento).val(data.complemento);
                    }

                    // Limpa o placeholder
                    $(campoEndereco).attr('placeholder', '');

                    //console.log(`CEP (${tipo}) encontrado e preenchido com sucesso`);
                }

                // Função para tentar uma API alternativa
                function tentarApiAlternativa() {
                    //console.log('Tentando API alternativa para o CEP:', cep);

                    // API alternativa: BrasilAPI
                    $.ajax({
                        url: `https://brasilapi.com.br/api/cep/v1/${cep}`,
                        dataType: 'json',
                        timeout: 3000, // 3 segundos de timeout
                        success: function(data) {
                            if (data && data.cep) {
                                preencherCampos(data);
                            } else {
                                tentarGemini();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            //console.log('Erro na segunda tentativa:', textStatus, errorThrown);

                            // Última tentativa: API PostalCode
                            $.ajax({
                                url: `https://ws.apicep.com/cep/${cep}.json`,
                                dataType: 'json',
                                timeout: 3000, // 3 segundos de timeout
                                success: function(data) {
                                    if (data && data.status === 200) {
                                        preencherCampos(data);
                                    } else {
                                        tentarGemini();
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    //console.log('Erro na terceira tentativa:', textStatus, errorThrown);
                                    tentarGemini();
                                }
                            });
                        }
                    });
                }

                // Função para tentar consulta via Gemini
                function tentarGemini() {
                    //console.log('Tentando consulta via Gemini para o CEP:', cep);
                    
                    // Mostrar indicador de IA
                    $(campoEndereco).attr('placeholder', 'Consultando IA...');
                    
                    $.ajax({
                        url: '/gemini-cep-api.php',
                        method: 'POST',
                        data: JSON.stringify({
                            cep: cep
                        }),
                        contentType: 'application/json',
                        timeout: 10000, // 10 segundos de timeout para IA
                        success: function(response) {
                            if (response.success && response.tipo === 'cep') {
                                const data = response.data;
                                
                                // Preencher campos com dados do Gemini
                                $(campoEndereco).val(data.rua || '');
                                $(campoCidade).val(data.cidade || '');
                                $(campoEstado).val(data.estado || '');
                                
                                // Se tiver país, selecionar
                                if (data.pais) {
                                    const paisSelect = $(campoPais);
                                    const paisOption = paisSelect.find(`option:contains("${data.pais}")`);
                                    if (paisOption.length > 0) {
                                        paisSelect.val(paisOption.val());
                                    }
                                }
                                
                                // Limpar placeholder
                                $(campoEndereco).attr('placeholder', '');
                                
                                // Mostrar mensagem de sucesso da IA
                                showAlert(`<strong>Sucesso!</strong> Endereço encontrado via IA para o CEP ${cep}.`, 'success');
                                
                                //console.log(`CEP (${tipo}) encontrado via Gemini e preenchido com sucesso`);
                            } else {
                                informarErro('CEP não encontrado nas APIs tradicionais nem via IA');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            //console.log('Erro na consulta Gemini:', textStatus, errorThrown);
                            informarErro('CEP não encontrado');
                        }
                    });
                }

                // Função para informar erro
                function informarErro(mensagem) {
                    // Limpa os campos
                    $(campoEndereco).val('');
                    $(campoCidade).val('');
                    $(campoEstado).val('');
                    $(campoComplemento).val('');

                    // Limpa o placeholder
                    $(campoEndereco).attr('placeholder', '');

                    //          console.error(`Erro ao consultar CEP (${tipo}):`, mensagem);

                    // Alerta mais amigável
                    showAlert(`<strong>Atenção:</strong> ${mensagem}. Por favor, preencha os dados manualmente.`, 'warning');
                }
            }

            // Função para consultar CEP via Gemini quando endereço, país, estado e cidade são preenchidos
            function consultarCEPviaEndereco(tipo) {
                const endereco = $(`#${tipo}_endereco`).val();
                const pais = $(`#${tipo}_pais option:selected`).text();
                const estado = $(`#${tipo}_estado option:selected`).text();
                const cidade = $(`#${tipo}_cidade`).val();
                
                // Verificar se todos os campos necessários estão preenchidos
                if (!endereco || !pais || !estado || !cidade) {
                    return;
                }
                
                // Verificar se o CEP já está preenchido
                const cepAtual = $(`#${tipo}_cep`).val();
                if (cepAtual && cepAtual.replace(/\D/g, '').length === 8) {
                    return; // CEP já está preenchido
                }
                
                // Mostrar indicador de IA no campo CEP
                $(`#${tipo}_cep`).attr('placeholder', 'Consultando IA...');
                
                $.ajax({
                    url: '/gemini-cep-api.php',
                    method: 'POST',
                    data: JSON.stringify({
                        endereco: endereco,
                        pais: pais,
                        estado: estado,
                        cidade: cidade
                    }),
                    contentType: 'application/json',
                    timeout: 10000, // 10 segundos de timeout para IA
                    success: function(response) {
                        if (response.success && response.tipo === 'endereco') {
                            const cep = response.data.cep;
                            
                            // Preencher o campo CEP
                            $(`#${tipo}_cep`).val(cep);
                            
                            // Limpar placeholder
                            $(`#${tipo}_cep`).attr('placeholder', '');
                            
                            // Mostrar mensagem de sucesso da IA
                            showAlert(`<strong>Sucesso!</strong> CEP ${cep} encontrado via IA para o endereço informado.`, 'success');
                            
                            //console.log(`CEP (${tipo}) encontrado via Gemini: ${cep}`);
                        } else {
                            // Limpar placeholder
                            $(`#${tipo}_cep`).attr('placeholder', '');
                            
                            // Mostrar mensagem informativa
                            showAlert(`<strong>Informação:</strong> Não foi possível encontrar o CEP via IA. Preencha manualmente se necessário.`, 'info');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        //console.log('Erro na consulta Gemini para CEP:', textStatus, errorThrown);
                        
                        // Limpar placeholder
                        $(`#${tipo}_cep`).attr('placeholder', '');
                        
                        // Mostrar mensagem informativa
                        showAlert(`<strong>Informação:</strong> Não foi possível consultar o CEP via IA. Preencha manualmente se necessário.`, 'info');
                    }
                });
            }

            // Adicionar eventos para consultar CEP quando endereço, país, estado e cidade são preenchidos
            // Para origem
            $('#origem_endereco, #origem_pais, #origem_estado, #origem_cidade').on('change blur', function() {
                setTimeout(() => consultarCEPviaEndereco('origem'), 500);
            });
            
            // Para destino
            $('#destino_endereco, #destino_pais, #destino_estado, #destino_cidade').on('change blur', function() {
                setTimeout(() => consultarCEPviaEndereco('destino'), 500);
            });
        });
    } // <- Fechamento da função inicializarApp

    // Atualiza o valor da parcela sempre que o número de parcelas ou o valor total mudar
    function atualizarValorParcela() {
        // Pega o valor total do pagamento (em string, pode vir com R$ e vírgula)
        let totalStr = $('#payment_amount').val() || '';
        totalStr = totalStr.replace(/[^\d,\.]/g, '').replace(',', '.');
        const total = parseFloat(totalStr) || 0;
        const parcelas = parseInt($('#installments').val()) || 1;
        if (parcelas > 0 && total > 0) {
            const valorParcela = (total / parcelas).toFixed(2);
            $('#installment_value').val(valorParcela);
        } else {
            $('#installment_value').val('');
        }
    }

    // Sempre que o número de parcelas mudar, atualize o valor da parcela
    $('#installments').on('change', atualizarValorParcela);
    // Sempre que o valor total mudar, atualize o valor da parcela
    $('#payment_amount').on('input', atualizarValorParcela);

    // Sempre que selecionar serviço de entrega, atualize o valor da parcela
    $(document).on('click', '.selecionar-servico', function() {
        setTimeout(atualizarValorParcela, 100); // Pequeno delay para garantir atualização
    });

    // Sempre que selecionar método de pagamento, atualize o valor da parcela
    $(document).on('click', '.select-payment-method', function() {
        setTimeout(atualizarValorParcela, 100);
    });

    // Sempre que mostrar o formulário de cartão de crédito, atualize o valor da parcela
    $('#credit-card-form').on('show', atualizarValorParcela);

    // Antes de submeter o formulário, atualize o valor da parcela
    $('#envio-form').on('submit', function(e) {
        atualizarValorParcela();
        // Se for 1x, desabilite os campos de parcelamento para não enviar
        if ($('#installments').val() == '1') {
            $('#installment_value').prop('disabled', true);
            $('#installments').prop('disabled', true);
        } else {
            $('#installment_value').prop('disabled', false);
            $('#installments').prop('disabled', false);
        }
        // ... resto do código ...
    });

    // Função para preencher as opções de serviço com as cotações
    function preencherOpcoesServico(cotacoes) {
        const container = $('#step-5 .card-body');
        
        // Limpar conteúdo anterior
        container.empty();
        
        // Adicionar cabeçalho
        container.append(`
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Selecione o serviço de entrega FedEx</strong><br>
                Abaixo estão as opções disponíveis para seu envio. Clique em uma opção para selecioná-la.
            </div>
        `);
        
        // Criar cards para cada cotação
        cotacoes.forEach(function(cotacao, index) {
            const cardHtml = `
                <div class="servico-option card mb-3" data-servico="${cotacao.servicoTipo}" data-index="${index}">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="servico-info">
                            <h6 class="card-title mb-1">${cotacao.servico}</h6>
                            <p class="text-muted mb-0">
                                <i class="fas fa-clock me-1"></i>${cotacao.tempoEntrega}
                            </p>
                        </div>
                        <div class="price-info">
                            <div class="price-main">R$ ${cotacao.valorTotal}</div>
                        </div>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });
        
        // Adicionar evento de clique nos cards
        $('.servico-option').on('click', function() {
            $('.servico-option').removeClass('selected');
            $(this).addClass('selected');
            
            const servicoTipo = $(this).data('servico');
            const index = $(this).data('index');
            window.servicoSelecionado = {
                tipo: servicoTipo,
                index: index
            };
        });
        
        // Adicionar botão de continuar
        container.append(`
            <div class="text-end mt-4">
                <button type="button" class="btn btn-primary btn-lg" id="btn-step-5-next" disabled>
                    <i class="fas fa-arrow-right me-2"></i>Continuar
                </button>
            </div>
        `);
        
        // Habilitar botão quando um serviço for selecionado
        $('.servico-option').on('click', function() {
            $('#btn-step-5-next').prop('disabled', false);
        });
    }

    // Função para atualizar o progresso do wizard
    function atualizarProgressoWizard(etapa) {
        const totalEtapas = 6; // Agora são 6 etapas
        const progresso = (etapa / totalEtapas) * 100;
        
        $('#wizard-progress-bar').css('width', progresso + '%');
        $('#wizard-progress-bar').attr('aria-valuenow', etapa);
        $('#wizard-progress-label').text('Etapa ' + etapa + ' de ' + totalEtapas);
        $('#info-card-badge').text('Etapa ' + etapa + ' de ' + totalEtapas);
    }
</script>

<!-- Seção para exibir logs de depuração -->
<div id="debug-logs-section" class="mt-5 mb-3 bg-gray-100 rounded-md p-4 hidden" style="display: none;">
    <h3 class="text-lg font-semibold mb-2 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg"  class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Logs de Depuração
        <button id="toggle-logs" class="ml-2 text-xs text-blue-600 hover:text-blue-800">Ocultar</button>
    </h3>
    <div id="logs-container" class="bg-gray-900 text-green-400 p-4 rounded-md font-mono text-sm overflow-auto" style="max-height: 400px;">
        <div id="logs-content"></div>
    </div>
</div>

<!-- Script para mostrar a seção de logs e renderizar os logs JavaScript -->
<script>
    // Função para adicionar os logs à seção de depuração
    function renderLogs(logs) {
        const logsContainer = document.getElementById('logs-content');
        if (!logsContainer) return;

        // Limpar o conteúdo anterior
        logsContainer.innerHTML = '';

        // Criar um elemento para injetar os scripts
        const scriptContainer = document.createElement('div');
        scriptContainer.style.display = 'none';
        document.body.appendChild(scriptContainer);

        // Adicionar cada script ao container
        logs.forEach(logScript => {
            const scriptElement = document.createElement('script');
            scriptElement.innerHTML = logScript.replace(/<script>/g, '').replace(/<\/script>/g, '');
            scriptContainer.appendChild(scriptElement);
        });

        // Remover o container de scripts após execução
        setTimeout(() => {
            document.body.removeChild(scriptContainer);
        }, 100);

        // Adicionar mensagem informativa
        const infoMessage = document.createElement('div');
        infoMessage.textContent = 'Os logs foram enviados para o console do navegador. Pressione F12 para abrir as Ferramentas de Desenvolvedor e veja a aba "Console".';
        infoMessage.className = 'text-white bg-blue-600 p-2 rounded-md mb-2';
        logsContainer.appendChild(infoMessage);
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

    // Adicionar ao código existente que processa o formulário
    document.getElementById('form-envio').addEventListener('submit', function(e) {
        e.preventDefault();

        // Verificar se o serviço foi selecionado
        if (!servicoSelecionado) {
            alert('Por favor, selecione um serviço de entrega.');
            return false;
        }

        // Verificar se o método de pagamento foi selecionado
        if (!document.querySelector('input[name="payment_method"]:checked')) {
            alert('Por favor, selecione um método de pagamento.');
            return false;
        }

        // Mostrar loading
        document.getElementById('btn-submit').disabled = true;
        document.getElementById('btn-submit').textContent = 'Processando...';
        document.getElementById('loading-overlay').classList.remove('hidden');

        // Obter dados do formulário
        const formData = new FormData(this);

        // Enviar requisição para processar o envio
        fetch('{{ route("api.envio.processar") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar loading
                document.getElementById('loading-overlay').classList.add('hidden');

                // Renderizar logs se disponíveis
                if (data.logs && data.logs.length > 0) {
                    document.getElementById('debug-logs-section').classList.remove('hidden');
                    renderLogs(data.logs);
                }

                if (data.success) {
                    // Mostrar mensagem de sucesso
                    Swal.fire({
                        title: 'Sucesso!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Ver rastreamento'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirecionar para a página de rastreamento
                            window.location.href = '{{ route("rastreamento") }}?tracking=' + data.shipment.tracking_number;
                        }
                    });
                } else {
                    // Mostrar mensagem de erro
                    Swal.fire({
                        title: 'Erro!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                    // Reativar botão de envio
                    document.getElementById('btn-submit').disabled = false;
                    document.getElementById('btn-submit').textContent = 'Finalizar Envio';
                }
            })
            .catch(error => {
                // Ocultar loading
                document.getElementById('loading-overlay').classList.add('hidden');

                // Mostrar erro
                Swal.fire({
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao processar o envio. Por favor, tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });

                // Reativar botão de envio
                document.getElementById('btn-submit').disabled = false;
                document.getElementById('btn-submit').textContent = 'Finalizar Envio';

                //  console.error('Erro:', error);
            });
    });

    // Verificar se estamos em ambiente de desenvolvimento
    const isDev = "{{ app()->environment() }}" === "local";
    const isAdmin = "{{ auth()->check() && auth()->user()->is_admin ? 'true' : 'false' }}" === "true";

    // Mostrar seção de logs apenas em desenvolvimento ou para administradores
    if (isDev || isAdmin) {
        document.getElementById('debug-logs-section').classList.remove('hidden');
    }
</script>

<!-- Script para verificar ambiente e perfil do usuário -->
<script>
    // Definição de variáveis de ambiente fornecidas pelo backend
    var appEnvironment = "{{ app()->environment() }}";
    var isUserAdmin = "{{ auth()->check() && auth()->user()->is_admin ? 'true' : 'false' }}" === "true";

    // Verificar se estamos em ambiente de desenvolvimento ou se o usuário é admin
    if (appEnvironment === "local" || isUserAdmin) {
        document.getElementById('debug-logs-section').classList.remove('hidden');
    }
</script>

<!-- Modal de Confirmação de Produto -->
<div class="modal fade" id="confirmarProdutoModal" tabindex="-1" aria-labelledby="confirmarProdutoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmarProdutoModalLabel"><i class="fas fa-check-circle me-2"></i>Confirmar Adição de Produto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i> Verifique as informações abaixo antes de confirmar a adição do produto.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            <tr class="bg-light">
                                <th width="30%" class="align-middle"><i class="fas fa-box me-2"></i>Produto:</th>
                                <td id="modal-produto-nome" class="fw-bold"></td>
                            </tr>
                            <tr>
                                <th class="align-middle"><i class="fas fa-barcode me-2"></i>NCM:</th>
                                <td id="modal-produto-ncm"></td>
                            </tr>
                            <tr>
                                <th class="align-middle"><i class="fas fa-dollar-sign me-2"></i>Valor:</th>
                                <td id="modal-produto-valor" class="text-success fw-bold"></td>
                            </tr>
                            <tr>
                                <th class="align-middle"><i class="fas fa-balance-scale me-2"></i>Unidade:</th>
                                <td id="modal-produto-unidade"></td>
                            </tr>
                            <tr>
                                <th class="align-middle"><i class="fas fa-sort-amount-up me-2"></i>Quantidade:</th>
                                <td id="modal-produto-quantidade"></td>
                            </tr>
                            <tr class="bg-light">
                                <th class="align-middle"><i class="fas fa-calculator me-2"></i>Total:</th>
                                <td id="modal-produto-total" class="text-success fw-bold"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelarProdutoBtn">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-outline-primary" id="editarProdutoBtn">
                    <i class="fas fa-edit me-2"></i>Editar
                </button>
                <button type="button" class="btn btn-success" id="confirmarProdutoBtn">
                    <i class="fas fa-check me-2"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="envio"]').addClass('active');
        $('#content-container').show();
    });
</script>
@endsection
