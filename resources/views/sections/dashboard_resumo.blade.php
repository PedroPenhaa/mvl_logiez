@if(auth()->check())
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-tachometer-alt me-2"></i> Meu Resumo de Serviços
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Últimos Envios -->
                    <div class="col-lg-6 mb-4">
                        <h5><i class="fas fa-shipping-fast me-2"></i> Últimos Envios</h5>
                        @if(isset($shipments) && $shipments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Rastreamento</th>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shipments as $shipment)
                                            <tr>
                                                <td>{{ $shipment->tracking_number }}</td>
                                                <td>{{ $shipment->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($shipment->status == 'completed')
                                                        <span class="badge bg-success">Entregue</span>
                                                    @elseif($shipment->status == 'in_transit')
                                                        <span class="badge bg-info">Em Trânsito</span>
                                                    @elseif($shipment->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Pendente</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $shipment->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-info rastrear-envio" data-tracking="{{ $shipment->tracking_number }}">
                                                        <i class="fas fa-search"></i> Rastrear
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light text-center p-3">
                                <i class="fas fa-box-open fa-2x mb-2 text-muted"></i>
                                <p class="mb-0">Você ainda não realizou envios.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Últimos Pagamentos -->
                    <div class="col-lg-6">
                        <h5><i class="fas fa-credit-card me-2"></i> Pagamentos Recentes</h5>
                        <ul class="nav nav-tabs mb-3" id="pagamentosTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pendentes-tab" data-bs-toggle="tab" data-bs-target="#pendentes" type="button" role="tab" aria-controls="pendentes" aria-selected="true">
                                    Pendentes <span class="badge bg-warning text-dark ms-1">{{ isset($pendingPayments) ? count($pendingPayments) : 0 }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="concluidos-tab" data-bs-toggle="tab" data-bs-target="#concluidos" type="button" role="tab" aria-controls="concluidos" aria-selected="false">
                                    Concluídos <span class="badge bg-success ms-1">{{ isset($completedPayments) ? count($completedPayments) : 0 }}</span>
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pagamentosTabContent">
                            <!-- Pagamentos Pendentes -->
                            <div class="tab-pane fade show active" id="pendentes" role="tabpanel" aria-labelledby="pendentes-tab">
                                @if(isset($pendingPayments) && count($pendingPayments) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Valor</th>
                                                    <th>Método</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pendingPayments as $payment)
                                                    <tr>
                                                        <td>{{ substr($payment->transaction_id, 0, 8) }}</td>
                                                        <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                                        <td>
                                                            @if($payment->payment_method == 'pix')
                                                                <span class="badge bg-info text-dark">PIX</span>
                                                            @elseif($payment->payment_method == 'boleto')
                                                                <span class="badge bg-secondary">Boleto</span>
                                                            @elseif($payment->payment_method == 'cartao')
                                                                <span class="badge bg-primary">Cartão</span>
                                                            @else
                                                                <span class="badge bg-light text-dark">{{ $payment->payment_method }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center p-3">
                                        <i class="far fa-check-circle fa-2x mb-2 text-muted"></i>
                                        <p class="mb-0">Não há pagamentos pendentes.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Pagamentos Concluídos -->
                            <div class="tab-pane fade" id="concluidos" role="tabpanel" aria-labelledby="concluidos-tab">
                                @if(isset($completedPayments) && count($completedPayments) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Valor</th>
                                                    <th>Data</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($completedPayments as $payment)
                                                    <tr>
                                                        <td>{{ substr($payment->transaction_id, 0, 8) }}</td>
                                                        <td>R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : $payment->updated_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center p-3">
                                        <i class="fas fa-info-circle fa-2x mb-2 text-muted"></i>
                                        <p class="mb-0">Não há pagamentos concluídos.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3 text-end">
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i> Ver Todos os Pagamentos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Seção padrão do dashboard (cards de serviços) -->
@include('sections.dashboard')

<script>
$(document).ready(function() {
    // Evento para botões de rastreamento de envio
    $('.rastrear-envio').on('click', function() {
        const trackingNumber = $(this).data('tracking');
        
        // Armazenar código de rastreamento para uso na página de rastreamento
        sessionStorage.setItem('lastTrackingCode', trackingNumber);
        
        // Navegar para a página de rastreamento
        $('.menu-item[data-section="rastreamento"]').click();
    });
});
</script> 