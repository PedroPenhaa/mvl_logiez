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

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="mb-4">Com nosso envio, vá mais longe</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="position-relative" style="height: 250px;">
                        <img src="{{ asset('img/boxes.png') }}" alt="Boxes" class="img-fluid position-absolute" 
                            style="width: 85%; 
                                   height: 90%; 
                                   object-fit: cover;
                                   border-radius: 15px;
                                   box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                                   transition: transform 0.3s ease;
                                   filter: brightness(1.02) contrast(1.02);"
                            onmouseover="this.style.transform='scale(1.02)'"
                            onmouseout="this.style.transform='scale(1)'">
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="mb-4">Somos uma plataforma inovadora que simplifica o processo de envios internacionais, eliminando a burocracia e reduzindo custos.</p>
                    
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-calculator text-primary me-2"></i>
                            Economize com precisão: Calcule custos de envio exatos em segundos.
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-balance-scale text-primary me-2"></i>
                            Escolha inteligente: Compare peso cubado e real para otimizar suas despesas.
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-bolt text-primary me-2"></i>
                            Agilidade total: Gere etiquetas automaticamente e envie sem atrasos.
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-mobile-alt text-primary me-2"></i>
                            Controle na palma da mão: Rastreie seus envios em tempo real, onde estiver.
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-file-export text-primary me-2"></i>
                            Exportação sem stress: Simplifique a documentação com nosso suporte especializado.
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <h4 class="mb-0">3,654</h4>
                            <small class="text-muted">Clientes Atendidos</small>
                        </div>
                        <div>
                            <h4 class="mb-0">4,154</h4>
                            <small class="text-muted">Envios Realizados</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Explorar</button>
                            <button class="btn btn-outline-primary">Entre em contato</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
