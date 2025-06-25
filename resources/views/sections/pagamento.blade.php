@php
    Log::info('Debug View - Payments:', [
        'pending' => $pendingPayments->count(),
        'completed' => $completedPayments->count(),
        'cancelled' => $cancelledPayments->count()
    ]);
@endphp

<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-credit-card me-2"></i> Meus Pagamentos
    </div>
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