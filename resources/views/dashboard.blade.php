@extends('layouts.app')

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <h4 class="alert-heading">Sucesso!</h4>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center header-container">
        <div class="header-text order-2 order-md-1">
            <h1 style="font-size: 30px; margin-bottom: 0; color: #430776;">Olá {{ auth()->user()->name }}! 👋</h1>
            <p class="text-muted" style="font-size: 14px; font-weight: 300; color: #6B7280; text-align: left; margin: 0;">Simplificamos envios internacionais, sem burocracia e com redução de custos</p>
        </div>
        <div class="header-logo order-1 order-md-2">
            <img src="{{ asset('img/logo_dashboard.avif') }}" alt="Logo Header" class="img-fluid" style="min-height: 130px; min-width: 104%; margin-top: -17px;">
        </div>
    </div>

    <div class="card mb-4 feature-card">
        <div class="card-body p-4">
            <h2 class="display-6" style="font-size: 1.8rem; font-weight: 700; color: #430776; position: relative; padding-left: 15px; border-left: 4px solid #7209B7;">
                Com nosso envio, <span style="color: #7209B7">vá mais longe</span>
            </h2>
            
            <div class="row g-4">
                <div class="col-md-6 order-2 order-md-1">
                    <div class="position-relative feature-image-container">
                        <img src="{{ asset('img/login3.avif') }}" alt="Boxes" class="feature-image">
                    </div>
                </div>
                <div class="col-md-6 order-1 order-md-2">
                  <!--
                  <p class="lead mb-4" style="font-size: 1rem;">Somos uma plataforma inovadora que simplifica o processo de envios internacionais, eliminando a burocracia e reduzindo custos.</p>
                  -->
                    <ul class="feature-list">
                        <li class="feature-item">
                            <span class="feature-icon">📊</span>
                            <span style="font-size: 0.8rem;">Economize com precisão: Calcule custos de envio exatos em segundos.</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">⚖️</span>
                            <span style="font-size: 0.8rem;">Escolha inteligente: Compare peso cubado e real para otimizar suas despesas.</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">⚡</span>
                            <span style="font-size: 0.8rem;">Agilidade total: Gere etiquetas automaticamente e envie sem atrasos.</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">📱</span>
                            <span style="font-size: 0.8rem;">Controle na palma da mão: Rastreie seus envios em tempo real, onde estiver.</span>
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon">📄</span>
                            <span style="font-size: 0.8rem;">Exportação sem stress: Simplifique a documentação com nosso suporte especializado.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Envios em Andamento -->
    @if($enviosEmAndamento->count() > 0)
    <div class="card mb-4 envios-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="display-6" style="font-size: 1.8rem; font-weight: 700; color: #430776; position: relative; padding-left: 15px; border-left: 4px solid #7209B7;">
                    Seus <span style="color: #7209B7">envios em andamento</span>
                </h2>
                <a href="{{ route('rastreamento') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Ver todos
                </a>
            </div>
            
            <div class="row g-3">
                @foreach($enviosEmAndamento as $envio)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="envio-card">
                        <div class="envio-header">
                            <div class="envio-status">
                                <span class="status-badge status-{{ strtolower(str_replace('_', '-', $envio->status)) }}">
                                    {{ ucfirst(str_replace('_', ' ', $envio->status)) }}
                                </span>
                            </div>
                            <div class="envio-tracking">
                                <small class="text-muted">#{{ $envio->tracking_number ?? 'N/A' }}</small>
                            </div>
                        </div>
                        
                        <div class="envio-content">
                            <div class="envio-route">
                                <div class="route-point origin">
                                    <div class="point-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="point-info">
                                        <small class="text-muted">Origem</small>
                                        <div class="location">
                                            {{ $envio->senderAddress->city ?? 'N/A' }}, {{ $envio->senderAddress->state ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="route-line">
                                    <div class="route-progress">
                                        <div class="progress-dot active"></div>
                                        <div class="progress-line"></div>
                                        <div class="progress-dot {{ $envio->status === 'DELIVERED' ? 'active' : '' }}"></div>
                                    </div>
                                </div>
                                
                                <div class="route-point destination">
                                    <div class="point-icon">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <div class="point-info">
                                        <small class="text-muted">Destino</small>
                                        <div class="location">
                                            {{ $envio->recipientAddress->city ?? 'N/A' }}, {{ $envio->recipientAddress->state ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="envio-details">
                                <div class="detail-item">
                                    <i class="fas fa-weight-hanging me-2"></i>
                                    <span>{{ $envio->package_weight ?? 'N/A' }} kg</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar me-2"></i>
                                    <span>{{ $envio->ship_date ? $envio->ship_date->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                @if($envio->estimated_delivery_date)
                                <div class="detail-item">
                                    <i class="fas fa-clock me-2"></i>
                                    <span>Entrega: {{ $envio->estimated_delivery_date->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="envio-footer">
                            <a href="{{ route('rastreamento') }}?tracking={{ $envio->tracking_number }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-search me-1"></i> Rastrear Envio
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!--

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Envios realizados</h3>
                        <a href="#" class="text-decoration-none">ver todos</a>
                    </div>
                    
                    <div class="envio-item border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-home me-2"></i>
                                <h5 class="d-inline">Código de rastreamento - 794616896420</h5>
                            </div>
                            <button class="btn btn-dark">Rastrear</button>
                        </div>
                        <div class="mt-2">
                            <p class="mb-1">Destino: São Paulo, SP, Brazil</p>
                            <p class="mb-0">Destino: Vancouver, WA, United States</p>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Notificações</h3>
                        <span class="badge bg-primary">2 notificações</span>
                    </div>
                    
                    <div class="notification-item border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1">Seu pacote foi coletado e está em separação.</p>
                                <small class="text-muted">Há 2 horas</small>
                            </div>
                            <div class="ms-auto">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1">Recebemos a confirmação de pagamento referente ao pedido #654</p>
                                <small class="text-muted">Há 5 horas</small>
                            </div>
                            <div class="ms-auto">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    -->

    @if(session('user_data'))
        <div class="d-none">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações Completas do Usuário</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informações Pessoais</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nome:</th>
                                    <td>{{ session('user_data')['name'] }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ session('user_data')['email'] }}</td>
                                </tr>
                                <tr>
                                    <th>CPF:</th>
                                    <td>{{ session('user_data')['cpf'] }}</td>
                                </tr>
                                <tr>
                                    <th>Telefone:</th>
                                    <td>{{ session('user_data')['phone'] ?? 'Não informado' }}</td>
                                </tr>
                                @if(session('user_data')['birth_date'])
                                <tr>
                                    <th>Data de Nascimento:</th>
                                    <td>{{ \Carbon\Carbon::parse(session('user_data')['birth_date'])->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if(session('user_data')['address'])
                                <tr>
                                    <th>Endereço:</th>
                                    <td>{{ session('user_data')['address'] }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Autenticação</h6>
                            <div class="alert alert-info">
                                <p><i class="fab fa-google me-2"></i> Você está conectado com sua conta do Google</p>
                                <p class="mb-0"><small>ID: {{ session('user_data')['id'] }}</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Forçar marcar dashboard como ativo no menu
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="dashboard"]').addClass('active');
        
        // Mostrar conteúdo diretamente
        $('#content-container').show();
    });
</script>
@endsection

<style>
/* Aplicar Rubik globalmente */
body, h1, h2, h3, h4, h5, h6, p, span, div, a, button {
    font-family: 'Rubik', sans-serif !important;
}

.feature-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    padding: 0;
}

.text-gradient {
    background: linear-gradient(120deg, #2563eb, #4f46e5);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
}

.feature-image-container {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9;
    min-height: 200px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    background: linear-gradient(145deg, #7209B7, #430776);
}

.feature-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    /*padding: 20px;*/
    transition: transform 0.3s ease;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 0px;
    padding: 0.6rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.7);
}

/* Regras de Responsividade */
@media (max-width: 768px) {
    .feature-image-container {
        min-height: 200px;
    }
    
    h1 {
        font-size: 24px !important;
    }
    
    .text-muted {
        font-size: 12px !important;
    }
    
    .display-6 {
        font-size: 1.4rem !important;
    }
    
    .feature-item {
        padding: 0.4rem;
        margin-bottom: 5px;
    }
    
    .feature-icon {
        font-size: 1.2rem;
        margin-right: 0.5rem;
    }
    
    .feature-item span {
        font-size: 0.75rem !important;
    }
    
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        text-align: center;
    }
    
    /* Reorganização do header para mobile */
    .header-container {
        flex-direction: column;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .header-logo {
        order: 1;
        margin-bottom: 15px;
    }
    
    .header-text {
        order: 2;
        text-align: center;
    }
    
    .header-text h1 {
        text-align: center !important;
        margin-bottom: 8px !important;
    }
    
    .header-text p {
        text-align: center !important;
        margin: 0 !important;
    }
    
    /* Melhorias para o logo no header em mobile */
    .header-logo img {
        margin-top: 0 !important;
        min-width: auto !important;
        width: 100%;
        max-width: 250px;
        height: auto;
        object-fit: contain;
    }
    
    /* Melhorias para a imagem principal em mobile */
    .feature-image-container {
        margin-bottom: 20px;
        border-radius: 12px;
    }
    
    .feature-image {
        padding: 15px;
        object-fit: contain;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .feature-image-container {
        min-height: 250px;
    }
    
    h1 {
        font-size: 26px !important;
    }
    
    .display-6 {
        font-size: 1.6rem !important;
    }
    
    .feature-item span {
        font-size: 0.85rem !important;
    }
}

@media (min-width: 1025px) and (max-width: 1440px) {
    .feature-image-container {
        min-height: 300px;
    }
}

@media (min-width: 1441px) {
    .feature-image-container {
        min-height: 400px;
    }
    
    .feature-item {
        padding: 0.8rem;
    }
    
    .feature-item span {
        font-size: 0.9rem !important;
    }
}

/* Ajustes para garantir espaçamento correto em todos os dispositivos */
.card-body {
    padding: 0;
}

.row.g-4 {
    margin: 0;
    padding: 0;
}

/* Ajustes para melhor visualização em telas muito pequenas */
@media (max-width: 480px) {
    .card-body {
        padding: 1rem;
    }
    
    .feature-item {
        flex-direction: row;
        align-items: flex-start;
    }
    
    .feature-icon {
        margin-top: 2px;
    }
    
    /* Melhorias específicas para smartphones */
    .header-logo img {
        max-width: 400px !important;
        margin-top: 0 !important;
        min-height: 94px;
    }
    
    .feature-image-container {
        min-height: 160px;
        margin-bottom: 15px;
    }
    
    .feature-image {
        padding: 10px;
    }
    
    .display-6 {
        font-size: 1.2rem !important;
        padding-left: 10px !important;
    }
    
    /* Melhor espaçamento entre elementos em mobile */
    .header-container {
        margin-bottom: 25px !important;
    }
    
    .feature-card {
        margin-bottom: 20px !important;
    }
    
    .feature-card .card-body {
        padding: 1.5rem !important;
    }
    
    .row.g-4 {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .col-md-6 {
        margin-bottom: 15px;
    }
}

/* Regras de Responsividade para a Imagem */
@media (max-width: 480px) {
    .feature-image-container {
        aspect-ratio: 4/3;
        min-height: 160px;
        border-radius: 10px;
    }
    
    .feature-image {
        padding: 10px;
        object-fit: contain;
    }
}

@media (min-width: 481px) and (max-width: 768px) {
    .feature-image-container {
        aspect-ratio: 16/9;
        min-height: 200px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .feature-image-container {
        aspect-ratio: 16/9;
        min-height: 250px;
    }
}

@media (min-width: 1025px) and (max-width: 1440px) {
    .feature-image-container {
        aspect-ratio: 16/9;
        min-height: 300px;
    }
}

@media (min-width: 1441px) {
    .feature-image-container {
        aspect-ratio: 16/9;
        min-height: 350px;
    }
    
    .feature-image {
        padding: 30px;
    }
}

/* Estilos para a seção de envios em andamento */
.envios-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
}

.envio-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid rgba(67, 7, 118, 0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.envio-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(67, 7, 118, 0.15);
}

.envio-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7, #fbbf24);
    color: #92400e;
}

.status-in-transit {
    background: linear-gradient(135deg, #dbeafe, #3b82f6);
    color: #1e40af;
}

.status-processing {
    background: linear-gradient(135deg, #e0e7ff, #6366f1);
    color: #3730a3;
}

.status-picked-up {
    background: linear-gradient(135deg, #d1fae5, #10b981);
    color: #065f46;
}

.status-in-delivery {
    background: linear-gradient(135deg, #fce7f3, #ec4899);
    color: #be185d;
}

.envio-tracking {
    text-align: right;
}

.envio-content {
    flex: 1;
    margin-bottom: 1rem;
}

.envio-route {
    margin-bottom: 1rem;
}

.route-point {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.route-point.origin {
    margin-bottom: 1rem;
}

.point-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.route-point.origin .point-icon {
    background: linear-gradient(135deg, #7209B7, #430776);
    color: white;
}

.route-point.destination .point-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.point-info {
    flex: 1;
}

.location {
    font-weight: 600;
    color: #374151;
    font-size: 0.9rem;
}

.route-line {
    margin: 0.5rem 0;
    padding-left: 16px;
}

.route-progress {
    display: flex;
    align-items: center;
    height: 20px;
}

.progress-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    transition: all 0.3s ease;
}

.progress-dot.active {
    background: #7209B7;
    box-shadow: 0 0 0 4px rgba(114, 9, 183, 0.2);
}

.progress-line {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, #7209B7, #d1d5db);
    margin: 0 0.5rem;
}

.envio-details {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    color: #6b7280;
    background: #f9fafb;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    flex: 1;
    min-width: 120px;
}

.detail-item i {
    color: #7209B7;
    font-size: 0.7rem;
}

.envio-footer {
    margin-top: auto;
}

.envio-footer .btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 0.6rem 1rem;
    background: linear-gradient(135deg, #7209B7, #430776);
    border: none;
    transition: all 0.3s ease;
}

.envio-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(114, 9, 183, 0.3);
}

/* Responsividade para envios */
@media (max-width: 768px) {
    .envio-card {
        padding: 1rem;
    }
    
    .envio-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .envio-tracking {
        text-align: left;
    }
    
    .envio-details {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .detail-item {
        min-width: auto;
        justify-content: center;
    }
    
    .route-point {
        margin-bottom: 0.75rem;
    }
    
    .point-icon {
        width: 28px;
        height: 28px;
        margin-right: 0.5rem;
    }
    
    .location {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .envios-card .card-body {
        padding: 1.5rem !important;
    }
    
    .envio-card {
        padding: 0.8rem;
    }
    
    .status-badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    
    .point-icon {
        width: 24px;
        height: 24px;
    }
    
    .location {
        font-size: 0.8rem;
    }
    
    .detail-item {
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
    }
}
</style>
