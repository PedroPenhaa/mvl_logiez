<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-file-invoice-dollar me-2"></i> Detalhes do Pagamento
    </div>
    <div class="card-body">
        <div class="mb-4">
            <a href="/pagamentos" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Voltar para Lista de Pagamentos
            </a>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-info-circle me-2"></i> Informações do Pagamento</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="40%">ID da Transação:</th>
                                    <td>{{ $payment->transaction_id }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($payment->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @elseif($payment->status == 'completed')
                                            <span class="badge bg-success">Concluído</span>
                                        @elseif($payment->status == 'cancelled')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $payment->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Método de Pagamento:</th>
                                    <td>
                                        @if($payment->payment_method == 'pix')
                                            <i class="fas fa-qrcode me-1"></i> PIX
                                        @elseif($payment->payment_method == 'boleto')
                                            <i class="fas fa-barcode me-1"></i> Boleto Bancário
                                        @elseif($payment->payment_method == 'cartao')
                                            <i class="fas fa-credit-card me-1"></i> Cartão de Crédito
                                        @else
                                            {{ $payment->payment_method }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Valor:</th>
                                    <td class="text-success fw-bold">{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Gateway:</th>
                                    <td>{{ $payment->payment_gateway }}</td>
                                </tr>
                                <tr>
                                    <th>Data de Criação:</th>
                                    <td>{{ $payment->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @if($payment->payment_date)
                                <tr>
                                    <th>Data do Pagamento:</th>
                                    <td>{{ $payment->payment_date->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endif
                                @if($payment->due_date)
                                <tr>
                                    <th>Data de Vencimento:</th>
                                    <td>
                                        @if($payment->due_date->isPast() && $payment->status == 'pending')
                                            <span class="text-danger">{{ $payment->due_date->format('d/m/Y') }} (Vencido)</span>
                                        @else
                                            {{ $payment->due_date->format('d/m/Y') }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($payment->shipment)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-shipping-fast me-2"></i> Informações do Envio</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="40%">Código de Rastreamento:</th>
                                    <td>{{ $payment->shipment->tracking_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Serviço:</th>
                                    <td>{{ $payment->shipment->service_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Origem:</th>
                                    <td>{{ $payment->shipment->origin_city ?? 'N/A' }}, {{ $payment->shipment->origin_country ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Destino:</th>
                                    <td>{{ $payment->shipment->destination_city ?? 'N/A' }}, {{ $payment->shipment->destination_country ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Data do Envio:</th>
                                    <td>{{ $payment->shipment->created_at ? $payment->shipment->created_at->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        @if($payment->shipment && $payment->shipment->tracking_number)
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary section-link" data-section="rastreamento" data-tracking="{{ $payment->shipment->tracking_number }}">
                                <i class="fas fa-search-location me-2"></i> Rastrear Envio
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <div class="col-lg-6">
                @if($payment->status == 'pending')
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <strong><i class="fas fa-exclamation-circle me-2"></i> Pagamento Pendente</strong>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i> Este pagamento ainda não foi confirmado. Por favor, utilize uma das opções abaixo para concluir o pagamento.
                            </div>
                            
                            @if($payment->payment_method == 'pix' && $payment->qrcode)
                                <div class="text-center mb-4">
                                    <h5 class="mb-3">QR Code PIX</h5>
                                    <div class="border p-3 mb-3 d-inline-block">
                                        <img src="{{ $payment->qrcode }}" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="/pagamentos/pix/{{ $payment->transaction_id }}" class="btn btn-primary">
                                            <i class="fas fa-qrcode me-2"></i> Ver QR Code Completo
                                        </a>
                                    </div>
                                </div>
                            @elseif($payment->payment_method == 'boleto' && $payment->barcode)
                                <div class="text-center mb-4">
                                    <h5 class="mb-3">Boleto Bancário</h5>
                                    <div class="border p-3 mb-3">
                                        <div class="fw-bold text-monospace mb-2" style="font-size: 0.8rem;">{{ $payment->barcode }}</div>
                                        <i class="fas fa-barcode fa-3x text-dark"></i>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-calendar-alt me-2"></i> Vencimento: {{ $payment->due_date ? $payment->due_date->format('d/m/Y') : 'N/A' }}
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="/pagamentos/boleto/{{ $payment->transaction_id }}" class="btn btn-primary">
                                            <i class="fas fa-file-pdf me-2"></i> Visualizar Boleto
                                        </a>
                                    </div>
                                </div>
                            @elseif($payment->payment_method == 'cartao')
                                <div class="text-center mb-4">
                                    <h5 class="mb-3">Pagamento com Cartão</h5>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> O pagamento com cartão está sendo processado. Você receberá uma confirmação assim que for aprovado.
                                    </div>
                                </div>
                            @endif
                            
                            @if(app()->environment('local'))
                                <div class="mt-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Ambiente de Desenvolvimento</h6>
                                            <p class="card-text">Para testes, você pode simular a conclusão deste pagamento.</p>
                                            <a href="/pagamentos/simular-callback/{{ $payment->transaction_id }}" class="btn btn-success">
                                                <i class="fas fa-check-circle me-2"></i> Simular Pagamento Concluído
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($payment->status == 'completed')
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <strong><i class="fas fa-check-circle me-2"></i> Pagamento Concluído</strong>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success">
                                <i class="fas fa-info-circle me-2"></i> Este pagamento foi processado com sucesso.
                            </div>
                            
                            <div class="text-center mb-4">
                                <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                                <h5>Pagamento Confirmado</h5>
                                <p>O pagamento no valor de <strong>{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</strong> foi concluído com sucesso em <strong>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : 'N/A' }}</strong>.</p>
                            </div>
                            
                            @if($payment->payment_method == 'cartao')
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Detalhes do Cartão</h6>
                                        <p class="card-text">Pagamento processado via cartão de crédito.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <strong><i class="fas fa-times-circle me-2"></i> Pagamento Cancelado</strong>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-info-circle me-2"></i> Este pagamento foi cancelado.
                            </div>
                            
                            <div class="text-center mb-4">
                                <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
                                <h5>Pagamento Cancelado</h5>
                                <p>O pagamento no valor de <strong>{{ 'R$ ' . number_format($payment->amount, 2, ',', '.') }}</strong> foi cancelado.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($payment->notes)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-sticky-note me-2"></i> Observações</strong>
                    </div>
                    <div class="card-body">
                        <p>{{ $payment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Evento para botões de navegação entre seções
    $('.section-link').on('click', function() {
        const section = $(this).data('section');
        const tracking = $(this).data('tracking');
        
        if (section === 'rastreamento' && tracking) {
            // Armazenar o código de rastreamento na sessionStorage para ser usado na página de rastreamento
            sessionStorage.setItem('lastTrackingCode', tracking);
        }
        
        $('.menu-item[data-section="' + section + '"]').click();
    });
});
</script> 