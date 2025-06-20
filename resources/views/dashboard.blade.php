@extends('layouts.app')

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <h4 class="alert-heading">Sucesso!</h4>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center ">
        <div>
            <h1 style="font-size: 30px; margin-bottom: 4px;">Olá {{ auth()->user()->name }}! 👋</h1>
            <p class="text-muted mb-0" style="font-size: 12px;">Simplificamos envios internacionais, sem burocracia e com redução de custos</p>
        </div>
        <div>
            <img src="{{ asset('img/logo_dashboard.png') }}" alt="Logo Header" class="img-fluid" style="min-height: 130px; min-width: 104%; margin-top: -17px;">
        </div>
    </div>

    <div class="card mb-4 feature-card">
        <div class="card-body p-4">
            <h2 class="display-6 mb-4 text-gradient" style="font-size:1.5rem">Com nosso envio, vá mais longe</h2>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="position-relative feature-image-container">
                        <img src="{{ asset('img/boxes.png') }}" alt="Boxes" class="feature-image">
                    </div>
                </div>
                <div class="col-md-6">
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
.feature-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
}

.text-gradient {
    background: linear-gradient(120deg, #2563eb, #4f46e5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
}

.feature-image-container {
    height: 100%;
    max-height: 240;
    border-radius: 15px;
    overflow: hidden;
}

.feature-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 15px;
    transition: transform 0.5s ease;
}

.feature-image:hover {
    transform: scale(1.03);
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
    padding: 0.3rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.7);
}

.feature-item:hover {
    background: rgba(37, 99, 235, 0.05);
    transform: translateX(5px);
}

.feature-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.stats-container {
    display: flex;
    gap: 2rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.stat-item {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    background: linear-gradient(120deg, #2563eb, #4f46e5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0;
    font-weight: 500;
}

.lead {
    color: #4b5563;
    font-size: 1.1rem;
    line-height: 1.6;
}
</style>
