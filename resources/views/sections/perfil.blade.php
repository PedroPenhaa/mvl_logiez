@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header do Perfil - Desktop/Tablet Only -->
    <div class="row mb-4 d-none d-md-block">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cb0 100%); border-radius: 20px;">
                <div class="card-body d-flex align-items-center" style="height: 10vh; min-height: 100px; padding: 1.5rem 2rem;">
                    <div class="container-fluid">
                        <div class="row align-items-center justify-content-center g-0">
                            <!-- Avatar Section -->
                            <div class="col-auto me-4">
                                <div class="position-relative">
                                    <div class="avatar-circle d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); backdrop-filter: blur(15px); border: 3px solid rgba(255,255,255,0.4);">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                    <div class="position-absolute" style="bottom: -2px; right: -2px;">
                                        <span class="badge bg-success rounded-circle d-flex align-items-center justify-content-center border border-2 border-white" style="width: 24px; height: 24px;">
                                            <i class="fas fa-check" style="font-size: 11px;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Info Section - Flexível -->
                            <div class="col">
                                <div class="d-flex flex-column">
                                    <h4 class="text-white fw-bold mb-2 perfil-nome" style="font-size: 1.5rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3); line-height: 1.2;">
                                        {{ $usuario['nome'] }}
                                    </h4>
                                    <div class="d-flex flex-wrap align-items-center gap-4">
                                        <div class="d-flex align-items-center text-white-50">
                                            <i class="fas fa-envelope me-2" style="font-size: 15px; opacity: 0.8;"></i>
                                            <span class="perfil-email" style="font-size: 0.95rem;">{{ $usuario['email'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center text-white-50">
                                            <i class="fas fa-calendar-alt me-2" style="font-size: 15px; opacity: 0.8;"></i>
                                            <span style="font-size: 0.95rem;">{{ isset($usuario['data_cadastro']) ? $usuario['data_cadastro'] : 'Out/2023' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center text-success fw-semibold">
                                            <i class="fas fa-shield-check me-2" style="font-size: 15px;"></i>
                                            <span style="font-size: 0.95rem;">Conta Verificada</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions Section -->
                            <div class="col-auto ms-4">
                                <div id="message-area" class="mb-2"></div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <button id="editar-perfil-btn" class="btn btn-light px-4 py-2 fw-semibold shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil" style="border-radius: 15px; background: rgba(255,255,255,0.95); backdrop-filter: blur(15px); border: none; min-width: 130px; font-size: 0.95rem;">
                                        <i class="fas fa-user-edit me-2"></i>
                                        Editar Perfil
                                    </button>
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="text-center">
                                            <div class="fw-bold text-white lh-1" style="font-size: 1.2rem;">{{ count($shipments) }}</div>
                                            <small class="text-white-50" style="font-size: 0.8rem;">Envios</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="fw-bold text-white lh-1" style="font-size: 1.2rem;">100%</div>
                                            <small class="text-white-50" style="font-size: 0.8rem;">Perfil</small>
                                        </div>
                                    </div>
                                </div>
    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Mobile NOVO - Simples e Limpo -->
    <div class="d-block d-md-none mobile-profile-header mb-3">
        <div class="mobile-user-card">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
                <div class="status-dot"></div>
            </div>
            <div class="user-info">
                <h5 class="user-name">{{ $usuario['nome'] }}</h5>
                <p class="user-email">{{ $usuario['email'] }}</p>
            </div>
            <div class="user-actions">
                <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
        <div id="message-area-mobile" class="mobile-message"></div>
                </div>
                
                <!-- CSS Limpo e Moderno -->
                <style>
                    /* Desktop Effects */
                    .avatar-circle {
                        transition: all 0.3s ease;
                    }
                    
                    .avatar-circle:hover {
                        transform: scale(1.08);
                        box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
                    }
                    
                    #editar-perfil-btn {
                        transition: all 0.3s ease;
                    }
                    
                    #editar-perfil-btn:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 8px 25px rgba(0,0,0,0.25) !important;
                        background: rgba(255,255,255,1) !important;
                    }

                    /* Mobile Header NOVO */
                    @media (max-width: 767px) {
                        .container-fluid {
                            padding: 1rem !important;
                            padding-top: 1rem !important;
                            background: #f8f9fa;
                        }
                        
                        /* Forçar exibição mobile */
                        .mobile-layout {
                            display: block !important;
                        }
                        
                        .mobile-profile-header {
                            display: block !important;
                        }

                        /* Header do perfil mobile - diferente do header do sistema */
                        .mobile-profile-header {
                            margin: -1rem -1rem 1rem -1rem;
                           /* padding: 1rem; */
                        }
                        
                        /* Preservar o header do sistema */
                        body > .mobile-header {
                            position: fixed !important;
                            top: 0 !important;
                            left: 0 !important;
                            right: 0 !important;
                            height: 40px !important;
                            background: #6f42c1 !important;
                            z-index: 998 !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: flex-end !important;
                            padding: 0 15px !important;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
                            margin: 0 !important;
                        }
                        
                        /* Garantir que o botão hambúrguer apareça */
                        .toggle-sidebar {
                            position: relative !important;
                            background: transparent !important;
                            color: white !important;
                            border: none !important;
                            width: 35px !important;
                            height: 35px !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            cursor: pointer !important;
                            transition: all 0.3s ease !important;
                            font-size: 18px !important;
                        }

                        .mobile-user-card {
                            background: white;
                            border-radius: 16px;
                            padding: 1rem;
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            border: none;
                        }

                        .user-avatar {
                            position: relative;
                            width: 50px;
                            height: 50px;
                            background: linear-gradient(135deg, #6f42c1, #8b5cb0);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        }

                        .user-avatar i {
                            color: white;
                            font-size: 1.2rem;
                        }

                        .status-dot {
                            position: absolute;
                            bottom: 2px;
                            right: 2px;
                            width: 12px;
                            height: 12px;
                            background: #28a745;
                            border: 2px solid white;
                            border-radius: 50%;
                        }

                        .user-info {
                            flex: 1;
                        }

                        .user-name {
                            font-size: 1.1rem;
                            font-weight: 700;
                            color: #212529;
                            margin: 0 0 0.25rem 0;
                            line-height: 1.2;
                        }

                        .user-email {
                            font-size: 0.85rem;
                            color: #6c757d;
                            margin: 0;
                        }

                        .edit-btn {
                            width: 40px;
                            height: 40px;
                            border: none;
                            background: #6f42c1;
                            color: white;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 0.9rem;
                            transition: all 0.2s ease;
                            flex-shrink: 0;
                        }

                        .edit-btn:hover {
                            background: #5a2d91;
                            transform: scale(1.1);
                        }

                        .mobile-message {
                            margin-top: 0.75rem;
                        }

                        /* Mobile Stats Cards Minimalistas */
                        .mobile-stat-card {
                            background: white;
                            border-radius: 12px;
                            padding: 0.875rem;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                            border: 1px solid #e9ecef;
                        }

                        .stat-icon {
                            width: 32px;
                            height: 32px;
                            background: #6f42c1;
                            border-radius: 8px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        }

                        .mobile-stat-card.status-verified .stat-icon {
                            background: #28a745;
                        }

                        .stat-icon i {
                            color: white;
                            font-size: 0.9rem;
                        }

                        .stat-info {
                            display: flex;
                            flex-direction: column;
                        }

                        .stat-number {
                            font-size: 1.1rem;
                            font-weight: 700;
                            color: #212529;
                            line-height: 1;
                        }

                        .stat-label {
                            font-size: 0.75rem;
                            color: #6c757d;
                            font-weight: 500;
                        }

                        /* Mobile Profile Cards Limpos */
                        .mobile-profile-card {
                            background: white;
                            border-radius: 16px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
                            border: 1px solid #e9ecef;
                        }

                        .profile-section {
                            padding: 1rem;
                        }

                        .section-title {
                            color: #212529;
                            font-size: 0.95rem;
                            font-weight: 700;
                            margin-bottom: 0.75rem;
                            padding-bottom: 0.5rem;
                            border-bottom: 1px solid #e9ecef;
                        }

                        .section-title i {
                            color: #6f42c1;
                            margin-right: 0.5rem;
                        }

                        .info-grid {
                            display: flex;
                            flex-direction: column;
                            gap: 0.5rem;
                        }

                        .info-row {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 0.75rem;
                            background: #f8f9fa;
                            border-radius: 8px;
                        }

                        .info-label {
                            font-size: 0.8rem;
                            color: #6c757d;
                            font-weight: 600;
                        }

                        .info-value {
                            font-size: 0.85rem;
                            color: #212529;
                            font-weight: 600;
                            text-align: right;
                        }

                        /* Modal Mobile */
                        .modal-dialog {
                            margin: 0.5rem;
                        }

                        .modal-content {
                            border-radius: 16px;
                            border: none;
                        }

                        .modal-header {
                            border-radius: 16px 16px 0 0;
                            padding: 1rem;
                        }

                        .modal-body {
                            padding: 1rem;
                        }
                    }
                    
                    /* Estilos para o modal de tracking */
                    .modal-xl {
                        max-width: 1200px;
                    }
                    
                    /* Estilos para responsividade da timeline no modal */
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
                    
                    .timeline-title {
                        margin-top: 0;
                        font-size: 1.2rem;
                        font-weight: 700;
                        color: #2c3e50;
                        margin-bottom: 8px;
                        line-height: 1.3;
                    }
                    
                    /* Loader moderno para o modal */
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
                    
                    /* Responsividade para mobile no modal */
                    @media (max-width: 767px) {
                        .modal-xl {
                            max-width: 95%;
                            margin: 0.5rem;
                        }
                        
                        .timeline:before {
                            left: 35px;
                        }
                        
                        .timeline-badge {
                            left: 35px !important;
                        }
                        
                        .timeline-panel {
                            width: calc(100% - 70px) !important;
                            margin-left: 70px !important;
                            padding: 20px;
                        }
                        
                        .timeline-item {
                            padding-left: 0 !important;
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
                </style>
            </div>
        </div>
    </div>

    <!-- Layout Desktop/Tablet (≥768px) -->
    <div class="d-none d-md-block">
        <div class="row g-4">
            <!-- Cards laterais primeiro no tablet -->
            <div class="col-12 d-lg-none mb-4">
                <div class="row g-3">
                    <!-- Card de Estatísticas -->
                    <div class="col-6">
                        <div class="card border-0 h-100" style="border-radius: 20px; background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                            <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <i class="fas fa-shipping-fast fa-2x text-primary mb-1" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));"></i>
                        </div>
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.4rem;">{{ count($shipments) }}</h5>
                                <small class="text-muted fw-semibold">Envios</small>
                            </div>
                        </div>
                    </div>
                    <!-- Card de Status -->
                    <div class="col-6">
                        <div class="card border-0 h-100" style="border-radius: 20px; background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%); box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                            <div class="card-body p-3 text-center d-flex flex-column justify-content-center">
                                <div class="mb-2">
                                    <i class="fas fa-user-check fa-2x text-success mb-1" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));"></i>
                                </div>
                                <h6 class="fw-bold text-success mb-1" style="font-size: 1.1rem;">Verificada</h6>
                                <small class="text-muted fw-semibold">Conta</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna principal -->
            <div class="col-lg-8 col-12">
                <!-- Informações Pessoais -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-sm-6 col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-id-card me-1"></i>CPF
                                    </label>
                                    <div class="fw-semibold text-dark perfil-cpf">{{ $usuario['cpf'] }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-phone me-1"></i>Telefone
                                    </label>
                                    <div class="fw-semibold text-dark perfil-telefone">{{ $usuario['telefone'] }}</div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    
                <!-- Endereço -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-map-marker-alt me-2 text-success"></i>
                            Endereço
                        </h5>
                        </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-map-marker-alt me-1"></i>Rua e Número
                                    </label>
                                    <div class="fw-semibold text-dark">
                                    <span class="perfil-rua">{{ $usuario['rua'] }}</span>, 
                                    <span class="perfil-numero">{{ $usuario['numero'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-mail-bulk me-1"></i>CEP
                                    </label>
                                    <div class="fw-semibold text-dark perfil-cep">{{ $usuario['cep'] }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-city me-1"></i>Cidade/Estado
                                    </label>
                                    <div class="fw-semibold text-dark">
                                    <span class="perfil-cidade">{{ $usuario['cidade'] }}</span> - 
                                    <span class="perfil-estado">{{ $usuario['estado'] }}</span>
                                    </div>
                                </div>
                            </div>
                            @if($usuario['complemento'])
                            <div class="col-12">
                                <div class="info-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid rgba(0,0,0,0.05);">
                                    <label class="text-muted small fw-semibold mb-1">
                                        <i class="fas fa-home me-1"></i>Complemento
                                    </label>
                                    <div class="fw-semibold text-dark perfil-complemento">{{ $usuario['complemento'] }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Coluna lateral - só desktop -->
            <div class="col-lg-4 d-none d-lg-block">
                <!-- Card de Estatísticas -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-shipping-fast fa-3x text-primary mb-2"></i>
                        </div>
                        <h4 class="fw-bold text-dark">{{ count($shipments) }}</h4>
                        <p class="text-muted mb-0">Envios Realizados</p>
                    </div>
                </div>

                <!-- Card de Status -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-check fa-3x text-success mb-2"></i>
                        </div>
                        <h5 class="fw-bold text-success">Conta Verificada</h5>
                        <p class="text-muted mb-0">Perfil completo e ativo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Mobile NOVO (<768px) -->
    <div class="d-block d-md-none mobile-layout">
        <!-- Quick Stats Cards -->
        <div class="mobile-stats-container mb-3">
            <div class="row g-2">
                <div class="col-6">
                    <div class="mobile-stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ count($shipments) }}</span>
                            <span class="stat-label">Envios</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mobile-stat-card status-verified">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Verificado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Profile Card -->
        <div class="mobile-profile-card mb-3">
            <div class="profile-section">
                <h6 class="section-title">
                    <i class="fas fa-user me-2"></i>Dados Pessoais
                </h6>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">CPF</span>
                        <span class="info-value perfil-cpf">{{ $usuario['cpf'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Telefone</span>
                        <span class="info-value perfil-telefone">{{ $usuario['telefone'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Address Card -->
        <div class="mobile-profile-card mb-3">
            <div class="profile-section">
                <h6 class="section-title">
                    <i class="fas fa-map-marker-alt me-2"></i>Endereço
                </h6>
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Endereço</span>
                        <span class="info-value">
                            <span class="perfil-rua">{{ $usuario['rua'] }}</span>, 
                            <span class="perfil-numero">{{ $usuario['numero'] }}</span>
                        </span>
                    </div>
                    @if($usuario['complemento'])
                    <div class="info-row">
                        <span class="info-label">Complemento</span>
                        <span class="info-value perfil-complemento">{{ $usuario['complemento'] }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">CEP</span>
                        <span class="info-value perfil-cep">{{ $usuario['cep'] }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cidade/UF</span>
                        <span class="info-value">
                            <span class="perfil-cidade">{{ $usuario['cidade'] }}</span> - 
                            <span class="perfil-estado">{{ $usuario['estado'] }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Envios -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-history me-2 text-warning"></i>
                        Histórico de Envios
                    </h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    @if(count($shipments) > 0)
                        <!-- Desktop Table -->
                        <div class="d-none d-md-block">
                    <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-semibold">
                                                <i class="fas fa-barcode me-1"></i> Código
                                            </th>
                                            <th class="border-0 fw-semibold">
                                                <i class="fas fa-calendar me-1"></i> Data
                                            </th>
                                            <th class="border-0 fw-semibold">
                                                <i class="fas fa-truck me-1"></i> Serviço
                                            </th>
                                            <th class="border-0 fw-semibold">
                                                <i class="fas fa-info-circle me-1"></i> Status
                                            </th>
                                            <th class="border-0 fw-semibold">
                                                <i class="fas fa-cog me-1"></i> Ações
                                            </th>
                                </tr>
                            </thead>
                            <tbody>
                                    @foreach($shipments as $shipment)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-primary">{{ $shipment->tracking_number ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $shipment->created_at ? $shipment->created_at->format('d/m/Y') : 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark px-3 py-2">{{ $shipment->service_name ?? $shipment->carrier }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = 'secondary';
                                                    $statusIcon = 'fas fa-clock';
                                                    
                                                    if($shipment->status === 'created') {
                                                        $statusClass = 'primary';
                                                        $statusIcon = 'fas fa-plus-circle';
                                                    }
                                                    elseif($shipment->status === 'in_transit') {
                                                        $statusClass = 'info';
                                                        $statusIcon = 'fas fa-shipping-fast';
                                                    }
                                                    elseif($shipment->status === 'delivered') {
                                                        $statusClass = 'success';
                                                        $statusIcon = 'fas fa-check-circle';
                                                    }
                                                    elseif($shipment->status === 'exception') {
                                                        $statusClass = 'warning';
                                                        $statusIcon = 'fas fa-exclamation-triangle';
                                                    }
                                                    elseif($shipment->status === 'cancelled') {
                                                        $statusClass = 'danger';
                                                        $statusIcon = 'fas fa-times-circle';
                                                    }
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }} px-3 py-2">
                                                    <i class="{{ $statusIcon }} me-1"></i>
                                                    {{ $shipment->status_description ?? ucfirst(str_replace('_', ' ', $shipment->status ?? 'pending')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm rounded-pill section-link" data-section="rastreamento" data-tracking="{{ $shipment->tracking_number }}">
                                                    <i class="fas fa-search me-1"></i> Rastrear
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                            </tbody>
                        </table>
                        </div>
                        
                        <!-- Mobile Cards -->
                        <div class="d-block d-md-none">
                            @foreach($shipments as $shipment)
                                <div class="card border-0 mb-3" style="background-color: #f8f9fa; border-radius: 12px;">
                                    <div class="card-body p-3">
                                        <!-- Header do card -->
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <div class="fw-bold text-primary mb-1">{{ $shipment->tracking_number ?? 'N/A' }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $shipment->created_at ? $shipment->created_at->format('d/m/Y') : 'N/A' }}
                                                </small>
                                            </div>
                                            @php
                                                $statusClass = 'secondary';
                                                $statusIcon = 'fas fa-clock';
                                                
                                                if($shipment->status === 'created') {
                                                    $statusClass = 'primary';
                                                    $statusIcon = 'fas fa-plus-circle';
                                                }
                                                elseif($shipment->status === 'in_transit') {
                                                    $statusClass = 'info';
                                                    $statusIcon = 'fas fa-shipping-fast';
                                                }
                                                elseif($shipment->status === 'delivered') {
                                                    $statusClass = 'success';
                                                    $statusIcon = 'fas fa-check-circle';
                                                }
                                                elseif($shipment->status === 'exception') {
                                                    $statusClass = 'warning';
                                                    $statusIcon = 'fas fa-exclamation-triangle';
                                                }
                                                elseif($shipment->status === 'cancelled') {
                                                    $statusClass = 'danger';
                                                    $statusIcon = 'fas fa-times-circle';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} px-2 py-1">
                                                <i class="{{ $statusIcon }} me-1"></i>
                                                {{ $shipment->status_description ?? ucfirst(str_replace('_', ' ', $shipment->status ?? 'pending')) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Serviço -->
                                        <div class="mb-3">
                                            <span class="badge bg-light text-dark px-2 py-1">
                                                <i class="fas fa-truck me-1"></i>
                                                {{ $shipment->service_name ?? $shipment->carrier }}
                                            </span>
                                        </div>
                                        
                                        <!-- Botão de ação -->
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary btn-sm section-link" data-section="rastreamento" data-tracking="{{ $shipment->tracking_number }}">
                                                <i class="fas fa-search me-1"></i> Rastrear Envio
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-shipping-fast fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Nenhum envio encontrado</h5>
                            <p class="text-muted mb-0">Você ainda não realizou nenhum envio. Que tal fazer o primeiro?</p>
                        </div>
                    @endif
                </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- Modal para Editar Perfil -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cb0 100%); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="modalEditarPerfilLabel">
                    <i class="fas fa-edit me-2"></i> Editar Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
            <form id="perfil-form" action="{{ route('api.perfil.atualizar') }}" method="POST">
                @csrf
                
                    <!-- Informações Pessoais -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-user-circle me-2"></i>
                            Informações Pessoais
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">Nome Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="nome" name="nome" value="{{ $usuario['nome'] }}" required placeholder="Ex: João da Silva">
                    </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ $usuario['email'] }}" required placeholder="Ex: seu.email@exemplo.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="cpf" class="form-label fw-semibold">CPF</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-id-card text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="cpf" name="cpf" value="{{ $usuario['cpf'] }}" required placeholder="Ex: 123.456.789-00">
                        </div>
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-semibold">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="tel" class="form-control border-start-0" id="telefone" name="telefone" value="{{ $usuario['telefone'] }}" required placeholder="Ex: (11) 98765-4321">
                                </div>
                        </div>
                    </div>
                </div>
                
                    <!-- Endereço -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Endereço
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="rua" class="form-label fw-semibold">Rua</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-road text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="rua" name="rua" value="{{ $usuario['rua'] }}" required placeholder="Ex: Av. Paulista">
                    </div>
                            </div>
                            <div class="col-md-4">
                                <label for="numero" class="form-label fw-semibold">Número</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-hashtag text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="numero" name="numero" value="{{ $usuario['numero'] }}" required placeholder="Ex: 1000">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="complemento" class="form-label fw-semibold">Complemento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-plus text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="complemento" name="complemento" value="{{ $usuario['complemento'] }}" placeholder="Ex: Apto 101, Bloco B">
                        </div>
                            </div>
                            <div class="col-md-4">
                                <label for="cidade" class="form-label fw-semibold">Cidade</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-city text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="cidade" name="cidade" value="{{ $usuario['cidade'] }}" required placeholder="Ex: São Paulo">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="form-label fw-semibold">Estado</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-map text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="estado" name="estado" value="{{ $usuario['estado'] }}" required placeholder="Ex: SP">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="cep" class="form-label fw-semibold">CEP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-mail-bulk text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="cep" name="cep" value="{{ $usuario['cep'] }}" required placeholder="Ex: 01310-100">
                        </div>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light btn-lg px-4 me-2" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="perfil-form" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-save me-2"></i> Salvar Alterações
                </button>
                </div>
        </div>
    </div>
</div>

<!-- Modal para exibir tracking do envio -->
<div class="modal fade" id="modalTrackingEnvio" tabindex="-1" aria-labelledby="modalTrackingEnvioLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cb0 100%); border-radius: 20px 20px 0 0;">
                <h5 class="modal-title text-white fw-bold" id="modalTrackingEnvioLabel">
                    <i class="fas fa-map-marker-alt me-2"></i> Rastreamento de Envio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Loader -->
                <div class="d-flex justify-content-center mt-4" id="modal-tracking-loader">
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
                
                <!-- Mensagens de sucesso/erro -->
                <div id="modal-tracking-success" class="alert alert-success mt-4" style="display: none;">
                    <i class="fas fa-check-circle me-2"></i> Consulta realizada com sucesso
                </div>
                
                <div id="modal-tracking-error" class="alert alert-danger mt-4" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> <span id="modal-tracking-error-message"></span>
                </div>
                
                <!-- Resultado do rastreamento -->
                <div id="modal-tracking-resultado" style="display: none;">
                    <div class="card mb-4 shadow-lg border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i> <span id="modal-rastreamento-codigo"></span></h5>
                            <div>
                                <span class="badge bg-primary me-2" id="modal-rastreamento-servico"></span>
                                <button id="modal-btn-solicitar-comprovante" class="btn btn-sm btn-outline-primary ms-2" style="display: none;" title="Solicitar comprovante de entrega assinado">
                                    <i class="fas fa-file-signature"></i> Comprovante de Entrega
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-md-0 mb-3">
                                    <div class="info-grupo p-3 rounded bg-light mb-2">
                                        <p class="mb-2"><strong><i class="fas fa-calendar-alt me-2 text-muted"></i>Data de Postagem:</strong> <span id="modal-data-postagem">-</span></p>
                                        <p class="mb-2"><strong><i class="fas fa-map-marker-alt me-2 text-muted"></i>Origem:</strong> <span id="modal-origem-envio">-</span></p>
                                        <p class="mb-2"><strong><i class="fas fa-map-pin me-2 text-muted"></i>Destino:</strong> <span id="modal-destino-envio">-</span></p>
                                        <p class="mb-0"><strong><i class="fas fa-clock me-2 text-muted"></i>Entrega Prevista:</strong> <span id="modal-entrega-prevista">-</span></p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div id="modal-status-atual-container" class="status-atual text-center p-3 rounded shadow-sm" style="background-color: rgba(99, 73, 158, 0.1);">
                                        <p class="mb-1 text-muted"><i class="fas fa-info-circle me-1"></i>Status Atual</p>
                                        <h4 class="mb-0" id="modal-status-atual" style="color: #63499E">-</h4>
                                        <p class="mt-2 mb-0 small" id="modal-status-atualizacao">Atualizado em: -</p>
                                    </div>
                                    
                                    <div id="modal-status-atraso-container" class="mt-3 status-atraso text-center p-3 rounded shadow-sm" style="background-color: rgba(220, 53, 69, 0.1); display: none;">
                                        <p class="mb-1 text-muted"><i class="fas fa-exclamation-circle me-1"></i>Atraso Identificado</p>
                                        <p class="mb-0 text-danger" id="modal-status-atraso-detalhes"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histórico de Rastreamento</h5>
                        <button id="modal-toggle-timeline" class="btn btn-outline-primary btn-sm" type="button">
                            <i class="fas fa-chevron-down me-1"></i>Mostrar Trackings
                        </button>
                    </div>
                    
                    <div class="timeline-container" id="modal-timeline-container" style="display: none;">
                        <ul class="timeline" id="modal-rastreamento-timeline">
                            <!-- Eventos de rastreamento serão inseridos aqui dinamicamente -->
                        </ul>
                    </div>
                    
                    <div id="modal-rastreamento-simulado-alert" class="alert alert-warning mt-4 shadow-sm" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i> <span id="modal-rastreamento-simulado-message"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary btn-lg px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="perfil"]').addClass('active');
        $('#content-container').show();
        
        // Adicionar evento aos botões de link de seção
        $('.section-link').on('click', function() {
            const section = $(this).data('section');
            const trackingNumber = $(this).data('tracking');
            
            if (section === 'rastreamento' && trackingNumber) {
                // Fazer requisição de rastreamento diretamente
                fazerRastreamento(trackingNumber);
            } else {
                // Comportamento padrão - mudar de seção
                $('.menu-item[data-section="' + section + '"]').click();
            }
        });
        
        // Função para fazer rastreamento
        function fazerRastreamento(trackingNumber) {
            // Abrir o modal
            const modal = new bootstrap.Modal(document.getElementById('modalTrackingEnvio'));
            modal.show();
            
            // Limpar e preparar o modal
            limparModalTracking();
            
            // Mostrar loader
            $('#modal-tracking-loader').show();
            
            // Fazer a requisição AJAX
            $.ajax({
                url: '{{ route("api.rastreamento.buscar") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    codigo_rastreamento: trackingNumber,
                    forcarSimulacao: false
                },
                dataType: 'json',
                success: function(response) {
                    // Ocultar loader
                    $('#modal-tracking-loader').hide();
                    
                    if (response.success) {
                        // Mostrar mensagem de sucesso
                        $('#modal-tracking-success').show();
                        
                        // Preencher dados do resultado no modal
                        mostrarResultadoRastreamentoModal(response);
                    } else {
                        // Mostrar mensagem de erro
                        $('#modal-tracking-error').show();
                        $('#modal-tracking-error-message').text(response.message || 'Não foi possível obter informações de rastreamento.');
                    }
                },
                error: function(xhr) {
                    // Ocultar loader
                    $('#modal-tracking-loader').hide();
                    
                    let errorMsg = 'Erro ao processar a solicitação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#modal-tracking-error').show();
                    $('#modal-tracking-error-message').text(errorMsg);
                }
            });
        }
        
        // Função para limpar o modal
        function limparModalTracking() {
            $('#modal-tracking-loader').hide();
            $('#modal-tracking-success').hide();
            $('#modal-tracking-error').hide();
            $('#modal-tracking-resultado').hide();
        }
        
        // Função para mostrar resultado do rastreamento no modal
        function mostrarResultadoRastreamentoModal(response) {
            // Preencher dados do resultado no modal
            $('#modal-rastreamento-codigo').text(response.codigo || '-');
            $('#modal-origem-envio').text(response.origem || '-');
            $('#modal-destino-envio').text(response.destino || '-');
            $('#modal-data-postagem').text(formatarData(response.dataPostagem) || '-');
            $('#modal-entrega-prevista').text(formatarData(response.dataEntregaPrevista) || '-');
            $('#modal-status-atual').text(response.status || '-');
            $('#modal-rastreamento-servico').text(response.servicoDescricao || 'FedEx');
            
            // Atualizar a última atualização
            if (response.ultimaAtualizacao) {
                $('#modal-status-atualizacao').text('Atualizado em: ' + formatarDataHora(response.ultimaAtualizacao));
            } else {
                $('#modal-status-atualizacao').text('');
            }
            
            // Verificar se há atraso
            if (response.temAtraso) {
                $('#modal-status-atraso-container').show();
                $('#modal-status-atraso-detalhes').text(response.detalhesAtraso || 'Há um atraso na entrega');
            } else {
                $('#modal-status-atraso-container').hide();
            }
            
            // Verificar se está entregue
            if (response.entregue) {
                $('#modal-status-atual-container').css('background-color', 'rgba(40, 167, 69, 0.1)');
                $('#modal-status-atual').css('color', '#28a745');
                $('#modal-btn-solicitar-comprovante').show();
            } else {
                $('#modal-status-atual-container').css('background-color', 'rgba(99, 73, 158, 0.1)');
                $('#modal-status-atual').css('color', '#63499E');
                $('#modal-btn-solicitar-comprovante').hide();
            }
            
            // Preencher a timeline no modal
            preencherTimelineModal(response.eventos);
            
            // Verificar se é simulado
            if (response.simulado) {
                $('#modal-rastreamento-simulado-alert').show();
                $('#modal-rastreamento-simulado-message').text(response.mensagem || 'Atenção: Estes dados são simulados para demonstração.');
            } else {
                $('#modal-rastreamento-simulado-alert').hide();
            }
            
            // Exibir resultados
            $('#modal-tracking-resultado').show();
        }
        
        // Função para mostrar resultado do rastreamento
        function mostrarResultadoRastreamento(response) {
            // Preencher dados do resultado
            $('#rastreamento-codigo').text(response.codigo || '-');
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
            
            // Verificar se está entregue
            if (response.entregue) {
                $('#status-atual-container').css('background-color', 'rgba(40, 167, 69, 0.1)');
                $('#status-atual').css('color', '#28a745');
                $('#btn-solicitar-comprovante').show();
            } else {
                $('#status-atual-container').css('background-color', 'rgba(99, 73, 158, 0.1)');
                $('#status-atual').css('color', '#63499E');
                $('#btn-solicitar-comprovante').hide();
            }
            
            // Preencher a timeline
            preencherTimeline(response.eventos);
            
            // Verificar se é simulado
            if (response.simulado) {
                $('#rastreamento-simulado-alert').show();
                $('#rastreamento-simulado-message').text(response.mensagem || 'Atenção: Estes dados são simulados para demonstração.');
            } else {
                $('#rastreamento-simulado-alert').hide();
            }
            
            // Exibir resultados
            $('#rastreamento-resultado').show();
            
            // Scroll para os resultados
            $('html, body').animate({
                scrollTop: $('#rastreamento-resultado').offset().top - 100
            }, 800);
        }
        
        // Função para formatar data
        function formatarData(dataString) {
            if (!dataString) return '';
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
        
        // Função para preencher timeline
        function preencherTimeline(eventos) {
            const timeline = $('#rastreamento-timeline');
            timeline.empty();
            
            if (!eventos || eventos.length === 0) {
                timeline.append('<li class="text-center text-muted p-4">Nenhum evento de rastreamento disponível.</li>');
                return;
            }
            
            $.each(eventos, function(index, evento) {
                let icone = 'box';
                let corClasse = 'bg-secondary';
                
                if (evento.status) {
                    const status = evento.status.toLowerCase();
                    if (status.includes('entreg') || status.includes('ready for recipient')) {
                        icone = 'check-circle';
                        corClasse = 'bg-success';
                    } else if (status.includes('rota') || status.includes('transit')) {
                        icone = 'truck';
                        corClasse = 'bg-primary';
                    } else if (status.includes('chegada') || status.includes('arrived')) {
                        icone = 'plane-arrival';
                        corClasse = 'bg-info';
                    } else if (status.includes('saída') || status.includes('departed')) {
                        icone = 'plane-departure';
                        corClasse = 'bg-primary';
                    } else if (status.includes('atraso') || status.includes('exceção')) {
                        icone = 'exclamation-triangle';
                        corClasse = 'bg-warning';
                    }
                }
                
                const dataHoraFormatada = formatarDataHora(evento.data, evento.hora);
                
                const timelineItem = $(`
                    <li class="timeline-item">
                        <div class="timeline-badge ${corClasse}"><i class="fas fa-${icone}"></i></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">${evento.status || 'Evento'}</h6>
                                <p class="mb-0"><small class="text-muted"><i class="fas fa-clock"></i> ${dataHoraFormatada}</small></p>
                            </div>
                            <div class="timeline-body">
                                ${evento.descricao ? `<p class="mb-2">${evento.descricao}</p>` : ''}
                                ${evento.local ? `<p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>${evento.local}</p>` : ''}
                            </div>
                        </div>
                    </li>
                `);
                
                timeline.append(timelineItem);
            });
        }
        
        // Função para preencher timeline no modal
        function preencherTimelineModal(eventos) {
            const timeline = $('#modal-rastreamento-timeline');
            timeline.empty();
            
            if (!eventos || eventos.length === 0) {
                timeline.append('<li class="text-center text-muted p-4">Nenhum evento de rastreamento disponível.</li>');
                return;
            }
            
            $.each(eventos, function(index, evento) {
                let icone = 'box';
                let corClasse = 'bg-secondary';
                
                if (evento.status) {
                    const status = evento.status.toLowerCase();
                    if (status.includes('entreg') || status.includes('ready for recipient')) {
                        icone = 'check-circle';
                        corClasse = 'bg-success';
                    } else if (status.includes('rota') || status.includes('transit')) {
                        icone = 'truck';
                        corClasse = 'bg-primary';
                    } else if (status.includes('chegada') || status.includes('arrived')) {
                        icone = 'plane-arrival';
                        corClasse = 'bg-info';
                    } else if (status.includes('saída') || status.includes('departed')) {
                        icone = 'plane-departure';
                        corClasse = 'bg-primary';
                    } else if (status.includes('atraso') || status.includes('exceção')) {
                        icone = 'exclamation-triangle';
                        corClasse = 'bg-warning';
                    }
                }
                
                const dataHoraFormatada = formatarDataHora(evento.data, evento.hora);
                
                const timelineItem = $(`
                    <li class="timeline-item">
                        <div class="timeline-badge ${corClasse}"><i class="fas fa-${icone}"></i></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h6 class="timeline-title">${evento.status || 'Evento'}</h6>
                                <p class="mb-0"><small class="text-muted"><i class="fas fa-clock"></i> ${dataHoraFormatada}</small></p>
                            </div>
                            <div class="timeline-body">
                                ${evento.descricao ? `<p class="mb-2">${evento.descricao}</p>` : ''}
                                ${evento.local ? `<p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>${evento.local}</p>` : ''}
                            </div>
                        </div>
                    </li>
                `);
                
                timeline.append(timelineItem);
            });
        }
        
        // Controle do toggle da timeline no modal
        $('#modal-toggle-timeline').on('click', function() {
            const timelineContainer = $('#modal-timeline-container');
            const button = $(this);
            const icon = button.find('i');
            
            if (timelineContainer.is(':visible')) {
                // Recolher timeline
                timelineContainer.slideUp(400, function() {
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    button.html('<i class="fas fa-chevron-down me-1"></i>Mostrar Trackings');
                });
            } else {
                // Expandir timeline
                timelineContainer.slideDown(400, function() {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    button.html('<i class="fas fa-chevron-up me-1"></i>Ocultar Trackings');
                });
            }
        });
        
        // Teste para verificar se o botão está funcionando
        $('#editar-perfil-btn').on('click', function() {
            console.log('Botão Editar Perfil clicado!');
        });
        

        
        // Processar o formulário de edição via AJAX
        $('#perfil-form').on('submit', function(e) {
            e.preventDefault();
            console.log('Formulário enviado!');
            
            // Mostrar indicador de carregamento
            showLoader();
            
            // Enviar o formulário via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Resposta recebida:', response);
                    hideLoader();
                    
                    if (response.success) {
                        // Atualizar dados de visualização com os valores retornados
                        $('.perfil-nome').text(response.usuario.nome);
                        $('.perfil-email').text(response.usuario.email);
                        $('.perfil-cpf').text(response.usuario.cpf);
                        $('.perfil-telefone').text(response.usuario.telefone);
                        $('.perfil-rua').text(response.usuario.rua);
                        $('.perfil-numero').text(response.usuario.numero);
                        $('.perfil-complemento').text(response.usuario.complemento);
                        $('.perfil-cidade').text(response.usuario.cidade);
                        $('.perfil-estado').text(response.usuario.estado);
                        $('.perfil-cep').text(response.usuario.cep);
                        
                        // Fechar o modal - método mais simples
                        console.log('Fechando modal...');
                        $('#modalEditarPerfil').modal('hide');
                        
                        // Adicionar mensagem de sucesso
                        const successMessage = `
                            <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> ${response.message || 'Perfil atualizado com sucesso!'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        
                        // Inserir na área dedicada para mensagens (desktop/tablet)
                        $('#message-area').html(successMessage);
                        
                        // Inserir também na área mobile
                        $('#message-area-mobile').html(successMessage);
                        
                        // Configurar um timeout para remover a mensagem de sucesso após 5 segundos
                        setTimeout(function() {
                            $('#success-message').fadeOut(500, function() {
                                $(this).remove();
                            });
                        }, 5000);
                        
                        console.log('Perfil atualizado com sucesso!');
                    } else {
                        // Mostrar mensagem de erro
                        showAlert('danger', response.message || 'Erro ao atualizar o perfil. Por favor, tente novamente.');
                    }
                },
                error: function(xhr) {
                    console.log('Erro na requisição:', xhr);
                    hideLoader();
                    
                    let errorMessage = 'Erro ao atualizar o perfil. Por favor, tente novamente.';
                    
                    // Tentar extrair mensagem de erro da resposta
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    showAlert('danger', errorMessage);
                }
            });
        });
        
        // Funções auxiliares
        function showLoader() {
            // Verificar se o loader existe, senão criar
            if ($('#global-loader').length === 0) {
                $('body').append('<div id="global-loader" class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center" style="top: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 9999;"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
            } else {
                $('#global-loader').show();
            }
        }
        
        function hideLoader() {
            // Remover completamente o loader em vez de apenas ocultá-lo
            $('#global-loader').remove();
        }
        
        function showAlert(type, message) {
            // Criar o elemento de alerta
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Inserir na área de mensagens desktop/tablet
            $('#message-area').html(alertHtml);
            
            // Inserir também na área mobile
            $('#message-area-mobile').html(alertHtml);
            
            // Fazer scroll para o topo para garantir que o usuário veja a mensagem
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            
            // Definir timeout para remover o alerta de forma segura
            setTimeout(function() {
                // Remover das áreas de mensagem
                $('#message-area .alert').fadeOut(500, function() {
                    $(this).remove();
                });
                $('#message-area-mobile .alert').fadeOut(500, function() {
                        $(this).remove();
                    });
            }, 5000);
        }
        
        // Aplicar máscaras aos campos
        // Nota: Isso requer que o jQuery Mask Plugin esteja carregado
        // Se não estiver disponível, carregá-lo dinamicamente
        if (typeof $.fn.mask !== 'function') {
            // Carregar o script jQuery Mask Plugin dinamicamente
            $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', function() {
                aplicarMascaras();
            });
        } else {
            aplicarMascaras();
        }
        
        function aplicarMascaras() {
            $('#cpf').mask('000.000.000-00', {reverse: true});
            $('#telefone').mask('(00) 00000-0000');
            $('#cep').mask('00000-000');
        }
    });
</script>
@endsection

@endsection 