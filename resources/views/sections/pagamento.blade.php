@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cotacao.css') }}">
<style>
    /* Estilos específicos para a tela de pagamentos */
    body {
        background: #f6f7fb;
        font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
        color: #3d246c;
    }
    
    /* Cards estilo cotação */
    .card {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        background: #fff !important;
        margin-bottom: 1.5rem !important;
    }
    
    .card:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }
    
    .card-header {
        background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%) !important;
        color: white !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 0.75rem 1rem !important;
        border: none !important;
    }
    
    .card-header h5 {
        font-size: 1rem !important;
        margin: 0 !important;
        font-weight: 600 !important;
    }
    
    .card-body {
        padding: 1rem !important;
        border-radius: 0 0 12px 12px !important;
    }
    
    /* Inputs/selects padrão cotação */
    .form-control, .form-select {
        border-radius: 14px !important;
        border: 1.5px solid #e0e0e0 !important;
        background: #fff !important;
        color: #3d246c !important;
        font-size: 1.08rem !important;
        padding: 5px 20px !important;
        height: 50px !important;
        box-shadow: 0 2px 8px 0 rgba(111,66,193,0.04) !important;
        transition: border 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: left;
        font-size: 14px !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #8f5be8 !important;
        box-shadow: 0 0 0 2px #e9d6ff !important;
        background: #fff !important;
        color: #3d246c !important;
    }
    .form-control::placeholder {
        color: #6f42c1 !important;
        opacity: 0.7;
        font-weight: 400;
    }
    
    /* Botões padrão cotação */
    .btn-primary, .btn-success, .btn-outline-primary {
        background: linear-gradient(90deg, #8f5be8 0%, #6f42c1 100%) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 600 !important;
        border-radius: 14px !important;
        box-shadow: 0 2px 8px 0 rgba(111, 66, 193, 0.10) !important;
        transition: background 0.2s, box-shadow 0.2s;
        min-height: 56px;
        font-size: 1.08rem;
        padding: 0 2.5rem;
    }
    .btn-primary:hover, .btn-success:hover, .btn-outline-primary:hover {
        background: linear-gradient(90deg, #6f42c1 0%, #8f5be8 100%) !important;
        color: #fff !important;
        box-shadow: 0 4px 16px 0 rgba(111, 66, 193, 0.18) !important;
    }
    .btn-outline-secondary {
        border-color: #8f5be8 !important;
        color: #6f42c1 !important;
        background: #f3e7ff !important;
        font-weight: 500 !important;
        border-radius: 14px !important;
        min-height: 56px;
        font-size: 1.08rem;
    }
    .btn-outline-secondary:hover {
        background: #8f5be8 !important;
        color: #fff !important;
    }
    
    /* Tabela de pagamentos */
    .table {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px 0 rgba(111, 66, 193, 0.04);
    }
    .table th {
        background: #f3e7ff;
        color: #6f42c1;
        font-weight: 700;
        border: none;
    }
    .table td {
        border: none;
        color: #3d246c;
        vertical-align: middle;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
        background: #faf7ff;
    }
    .table-hover > tbody > tr:hover {
        background: #e9d6ff;
        color: #6f42c1;
    }
    
    /* Badges e status */
    .badge.bg-success {
        background: #28a745 !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-warning {
        background: #ffc107 !important;
        color: #000 !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-danger {
        background: #dc3545 !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-info {
        background: #17a2b8 !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-secondary {
        background: #6c757d !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-primary {
        background: #007bff !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    .badge.bg-light {
        background: #f8f9fa !important;
        color: #000 !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    
    /* Alertas */
    .alert-info {
        background: #e9d6ff;
        color: #6f42c1;
        border: none;
        font-weight: 500;
    }
    .alert-success {
        background: #d1ffe7;
        color: #1b7c4b;
        border: none;
    }
    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: none;
    }
    .alert-danger {
        background: #ffe1e1;
        color: #c82333;
        border: none;
    }
    .alert-light {
        background: #f8f9fa;
        color: #6c757d;
        border: none;
    }
    
    /* Navegação por abas */
    .nav-tabs {
        border-bottom: 2px solid #e9d6ff;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6f42c1;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link:hover {
        background: #f3e7ff;
        color: #6f42c1;
        border: none;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%);
        color: #fff;
        border: none;
        font-weight: 600;
    }
    
    /* Conteúdo das abas */
    .tab-content {
        background: #fff;
        border-radius: 0 0 12px 12px;
        padding: 1rem;
    }
    
    /* Responsividade */
    @media (max-width: 767px) {
        .card-body {
            padding: 1.2rem 0.7rem !important;
        }
        .form-control, .form-select {
            font-size: 1rem !important;
            padding: 0.7rem 0.8rem !important;
            height: 44px !important;
        }
        .btn-primary, .btn-success, .btn-outline-primary, .btn-outline-secondary {
            min-height: 44px !important;
            font-size: 1rem !important;
            padding: 0 1.2rem;
        }
        .nav-tabs .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
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
                    <i class="fas fa-credit-card me-2"></i>
                    <h1>Meus Pagamentos</h1>
                </div>
                <p class="description">Visualize todos os seus pagamentos, acompanhe o status e acesse os meios de pagamento</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Aqui você pode visualizar todos os seus pagamentos, acompanhar o status e acessar os meios de pagamento quando necessário.
        </div>
        
        <!-- Abas para diferentes status de pagamento -->
        <ul class="nav nav-tabs mb-4" id="pagamentoTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pendente-tab" data-bs-toggle="tab" data-bs-target="#pendente" type="button" role="tab" aria-controls="pendente" aria-selected="true">
                    <i class="fas fa-clock me-2"></i> Pendentes <span class="badge bg-warning text-dark ms-1">{{ count($pendingPayments) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="concluido-tab" data-bs-toggle="tab" data-bs-target="#concluido" type="button" role="tab" aria-controls="concluido" aria-selected="false">
                    <i class="fas fa-check-circle me-2"></i> Concluídos <span class="badge bg-success ms-1">{{ count($completedPayments) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelado-tab" data-bs-toggle="tab" data-bs-target="#cancelado" type="button" role="tab" aria-controls="cancelado" aria-selected="false">
                    <i class="fas fa-times-circle me-2"></i> Cancelados <span class="badge bg-danger ms-1">{{ count($cancelledPayments) }}</span>
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="pagamentoTabContent">
            <!-- Pagamentos Pendentes -->
            <div class="tab-pane fade show active" id="pendente" role="tabpanel" aria-labelledby="pendente-tab">
                @if(count($pendingPayments) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Transação</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Vencimento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->transaction_id }}</td>
                                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($payment->payment_method == 'pix')
                                                <span class="badge bg-info text-dark"><i class="fas fa-qrcode me-1"></i> PIX</span>
                                            @elseif($payment->payment_method == 'boleto')
                                                <span class="badge bg-secondary"><i class="fas fa-barcode me-1"></i> Boleto</span>
                                            @elseif($payment->payment_method == 'cartao' || $payment->payment_method == 'credit_card')
                                                <span class="badge bg-primary"><i class="fas fa-credit-card me-1"></i> Cartão</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $payment->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->due_date)
                                                @if($payment->due_date->isPast())
                                                    <span class="text-danger">{{ $payment->due_date->format('d/m/Y') }}</span>
                                                @else
                                                    {{ $payment->due_date->format('d/m/Y') }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-eye"></i> Detalhes
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light text-center p-5">
                        <i class="fas fa-hand-holding-usd fa-3x mb-3 text-muted"></i>
                        <h5>Você não possui pagamentos pendentes</h5>
                        <p class="text-muted">Todos os seus pagamentos foram processados ou você ainda não realizou envios.</p>
                    </div>
                @endif
            </div>
            
            <!-- Pagamentos Concluídos -->
            <div class="tab-pane fade" id="concluido" role="tabpanel" aria-labelledby="concluido-tab">
                @if(count($completedPayments) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Transação</th>
                                    <th>Data do Pagamento</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->transaction_id }}</td>
                                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($payment->payment_method == 'pix')
                                                <span class="badge bg-info text-dark"><i class="fas fa-qrcode me-1"></i> PIX</span>
                                            @elseif($payment->payment_method == 'boleto')
                                                <span class="badge bg-secondary"><i class="fas fa-barcode me-1"></i> Boleto</span>
                                            @elseif($payment->payment_method == 'cartao' || $payment->payment_method == 'credit_card')
                                                <span class="badge bg-primary"><i class="fas fa-credit-card me-1"></i> Cartão</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $payment->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i> Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light text-center p-5">
                        <i class="fas fa-check-circle fa-3x mb-3 text-muted"></i>
                        <h5>Você não possui pagamentos concluídos</h5>
                        <p class="text-muted">Seus pagamentos estão pendentes ou você ainda não realizou envios.</p>
                    </div>
                @endif
            </div>
            
            <!-- Pagamentos Cancelados -->
            <div class="tab-pane fade" id="cancelado" role="tabpanel" aria-labelledby="cancelado-tab">
                @if(count($cancelledPayments) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Transação</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cancelledPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->transaction_id }}</td>
                                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($payment->payment_method == 'pix')
                                                <span class="badge bg-info text-dark"><i class="fas fa-qrcode me-1"></i> PIX</span>
                                            @elseif($payment->payment_method == 'boleto')
                                                <span class="badge bg-secondary"><i class="fas fa-barcode me-1"></i> Boleto</span>
                                            @elseif($payment->payment_method == 'cartao' || $payment->payment_method == 'credit_card')
                                                <span class="badge bg-primary"><i class="fas fa-credit-card me-1"></i> Cartão</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $payment->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-eye"></i> Detalhes
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light text-center p-5">
                        <i class="fas fa-ban fa-3x mb-3 text-muted"></i>
                        <h5>Você não possui pagamentos cancelados</h5>
                        <p class="text-muted">Todos os seus pagamentos estão em processamento ou foram concluídos com sucesso.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Ativar aba baseada no hash da URL
        let hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }
        
        // Mudar hash na URL quando a aba é alterada
        $('.nav-tabs a').on('shown.bs.tab', function(e) {
            window.location.hash = e.target.hash;
        });
    });
</script>
@endsection 

@section('scripts')
<script>
    $(document).ready(function() {
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="pagamento"]').addClass('active');
        $('#content-container').show();
    });
</script>
@endsection 