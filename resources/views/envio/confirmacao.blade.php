@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-check-circle me-2"></i> Confirmação de Envio</h3>
                </div>
                <div class="card-body p-5">
                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="display-1 text-success mb-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="mb-3">Seu envio foi registrado com sucesso!</h2>
                        <p class="lead text-muted">
                            Obrigado por escolher a LogiEZ para suas necessidades de envio internacional.
                        </p>
                    </div>

                    <!-- Seção de pagamento - Exibe informações específicas baseadas no método -->
                    @if (request()->has('hash') && session('payment_info'))
                        <div class="card mb-4 border-light">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Informações de Pagamento</h4>
                            </div>
                            <div class="card-body">
                                @php
                                    $paymentInfo = session('payment_info');
                                    $paymentMethod = $paymentInfo['method'] ?? '';
                                @endphp

                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    @if ($paymentMethod == 'credit_card')
                                        Seu pagamento com cartão de crédito foi processado com sucesso.
                                    @elseif ($paymentMethod == 'boleto')
                                        Por favor, efetue o pagamento do boleto até a data de vencimento para que seu envio seja processado.
                                    @elseif ($paymentMethod == 'pix')
                                        Escaneie o QR Code abaixo ou copie o código PIX para realizar o pagamento.
                                    @else
                                        Aguardando confirmação do pagamento para processar seu envio.
                                    @endif
                                </div>

                                @if ($paymentMethod == 'pix')
                                    <div class="row">
                                        <div class="col-md-6 mb-3 text-center">
                                            @if (isset($paymentInfo['qrCode']))
                                                <div class="mb-3">
                                                    <h5>QR Code PIX</h5>
                                                    <img src="data:image/png;base64,{{ $paymentInfo['qrCode'] }}" alt="QR Code PIX" class="img-fluid" style="max-width: 250px;">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <h5>Código PIX Copia e Cola</h5>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="pix-code" value="{{ $paymentInfo['pixKey'] ?? '' }}" readonly>
                                                <button class="btn btn-outline-primary" type="button" id="copy-pix-btn">
                                                    <i class="fas fa-copy"></i> Copiar
                                                </button>
                                            </div>
                                            <div class="alert alert-success d-none" id="copy-success-alert">
                                                Código PIX copiado com sucesso!
                                            </div>
                                            <p class="text-muted small">Utilize o aplicativo do seu banco para escanear o QR Code ou colar o código acima.</p>
                                            <p class="text-muted small">O pagamento será confirmado automaticamente em alguns instantes após a compensação.</p>
                                        </div>
                                    </div>
                                @elseif ($paymentMethod == 'boleto')
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h5>Dados do Boleto</h5>
                                            @if (isset($paymentInfo['boletoUrl']))
                                                <div class="mb-3">
                                                    <a href="{{ $paymentInfo['boletoUrl'] }}" target="_blank" class="btn btn-primary">
                                                        <i class="fas fa-file-pdf me-2"></i> Abrir Boleto
                                                    </a>
                                                </div>
                                            @endif
                                            
                                            @if (isset($paymentInfo['barCode']))
                                                <div class="mb-3">
                                                    <h6>Código de Barras:</h6>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" id="barcode" value="{{ $paymentInfo['barCode'] }}" readonly>
                                                        <button class="btn btn-outline-primary" type="button" id="copy-barcode-btn">
                                                            <i class="fas fa-copy"></i> Copiar
                                                        </button>
                                                    </div>
                                                    <div class="alert alert-success d-none" id="copy-barcode-success-alert">
                                                        Código de barras copiado com sucesso!
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if (isset($paymentInfo['dueDate']))
                                                <p class="mb-2"><strong>Vencimento:</strong> {{ \Carbon\Carbon::parse($paymentInfo['dueDate'])->format('d/m/Y') }}</p>
                                            @endif
                                            
                                            @if (isset($paymentInfo['value']))
                                                <p class="mb-2"><strong>Valor:</strong> R$ {{ number_format($paymentInfo['value'], 2, ',', '.') }}</p>
                                            @endif
                                            
                                            <p class="text-muted small">Você pode pagar o boleto em qualquer banco, casa lotérica ou internet banking até a data de vencimento.</p>
                                            <p class="text-muted small">Seu envio será processado após a confirmação do pagamento.</p>
                                        </div>
                                    </div>
                                @elseif ($paymentMethod == 'credit_card')
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i> Pagamento com cartão de crédito aprovado!
                                            </div>
                                            
                                            @if (isset($paymentInfo['value']))
                                                <p class="mb-2"><strong>Valor:</strong> R$ {{ number_format($paymentInfo['value'], 2, ',', '.') }}</p>
                                            @endif
                                            
                                            @if (isset($paymentInfo['installments']) && $paymentInfo['installments'] > 1)
                                                <p class="mb-2"><strong>Parcelamento:</strong> {{ $paymentInfo['installments'] }}x de R$ {{ number_format($paymentInfo['value'] / $paymentInfo['installments'], 2, ',', '.') }}</p>
                                            @endif
                                            
                                            <p class="text-muted small">Seu envio será processado em breve.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Detalhes do Envio -->
                    @if (request()->has('hash'))
                        <div class="card mb-4 border-light">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">Detalhes do Envio</h4>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">
                                    <strong>Código de Rastreamento:</strong> 
                                    <span id="tracking-number">Carregando...</span>
                                </p>
                                <p class="mb-3">
                                    <strong>Status:</strong> 
                                    <span id="envio-status">Carregando...</span>
                                </p>
                                <p class="mb-3">
                                    <strong>Data de Criação:</strong> 
                                    <span id="envio-data">Carregando...</span>
                                </p>
                                <p class="mb-3">
                                    <strong>Método de Pagamento:</strong> 
                                    <span id="payment-method">Carregando...</span>
                                </p>
                            </div>
                        </div>

                        <div class="card mb-4 border-light">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">O que acontece agora?</h4>
                            </div>
                            <div class="card-body">
                                <ol class="timeline">
                                    <li class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h5>Envio Registrado</h5>
                                            <p>Seu pedido de envio foi registrado com sucesso.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker {{ $paymentMethod == 'credit_card' ? 'bg-success' : '' }}"></div>
                                        <div class="timeline-content">
                                            <h5>Pagamento {{ $paymentMethod == 'credit_card' ? 'Processado' : 'Aguardando' }}</h5>
                                            <p>
                                                @if ($paymentMethod == 'credit_card')
                                                    Seu pagamento foi processado com sucesso.
                                                @elseif ($paymentMethod == 'boleto')
                                                    Aguardando o pagamento do boleto bancário.
                                                @elseif ($paymentMethod == 'pix')
                                                    Aguardando a confirmação do pagamento via PIX.
                                                @else
                                                    Aguardando confirmação do pagamento.
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5>Preparação do Envio</h5>
                                            <p>Após a confirmação do pagamento, sua remessa será preparada para coleta pela FedEx.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5>Em Trânsito</h5>
                                            <p>Seu pacote será coletado e entrará em trânsito.</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5>Entrega</h5>
                                            <p>Seu pacote será entregue no destino final.</p>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('rastreamento') }}" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Acompanhar Envio
                        </a>
                        <a href="/" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-home me-2"></i> Voltar ao Início
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (request()->has('hash'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar detalhes do envio via AJAX
    const hash = "{{ request()->get('hash') }}";
    
    fetch(`/api/rastreamento/detalhes?hash=${hash}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tracking-number').textContent = data.shipment.tracking_number || 'Aguardando...';
                document.getElementById('envio-status').textContent = formatStatus(data.shipment.status);
                document.getElementById('envio-data').textContent = formatDate(data.shipment.created_at);
                document.getElementById('payment-method').textContent = formatPaymentMethod(data.payment?.method);
            } else {
                console.error('Erro ao carregar detalhes:', data.message);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        });
        
    // Adicionar funcionalidade para copiar código PIX
    const copyPixBtn = document.getElementById('copy-pix-btn');
    if (copyPixBtn) {
        copyPixBtn.addEventListener('click', function() {
            const pixCode = document.getElementById('pix-code');
            pixCode.select();
            document.execCommand('copy');
            
            const alertEl = document.getElementById('copy-success-alert');
            alertEl.classList.remove('d-none');
            
            setTimeout(function() {
                alertEl.classList.add('d-none');
            }, 3000);
        });
    }
    
    // Adicionar funcionalidade para copiar código de barras
    const copyBarcodeBtn = document.getElementById('copy-barcode-btn');
    if (copyBarcodeBtn) {
        copyBarcodeBtn.addEventListener('click', function() {
            const barcode = document.getElementById('barcode');
            barcode.select();
            document.execCommand('copy');
            
            const alertEl = document.getElementById('copy-barcode-success-alert');
            alertEl.classList.remove('d-none');
            
            setTimeout(function() {
                alertEl.classList.add('d-none');
            }, 3000);
        });
    }
    
    // Funções auxiliares para formatação
    function formatStatus(status) {
        const statusMap = {
            'pending_payment': 'Aguardando Pagamento',
            'payment_confirmed': 'Pagamento Confirmado',
            'created': 'Criado',
            'in_transit': 'Em Trânsito',
            'delivered': 'Entregue',
            'canceled': 'Cancelado'
        };
        
        return statusMap[status] || status;
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR');
    }
    
    function formatPaymentMethod(method) {
        const methodMap = {
            'credit_card': 'Cartão de Crédito',
            'boleto': 'Boleto Bancário',
            'pix': 'PIX'
        };
        
        return methodMap[method] || method;
    }
});
</script>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
    list-style: none;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    background-color: white;
    left: -23px;
    top: 5px;
}

.timeline-marker.bg-success {
    background-color: #28a745;
    border-color: #28a745;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: -15px;
    height: 100%;
    width: 2px;
    background-color: #dee2e6;
}
</style>
@endsection 