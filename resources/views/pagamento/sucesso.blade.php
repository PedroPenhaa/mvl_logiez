@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success shadow">
                <div class="card-header bg-success text-white text-center">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3 class="mb-0">Pagamento Realizado com Sucesso!</h3>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-success">
                        <h5>Seu pagamento foi processado com sucesso!</h5>
                        <p class="mb-0">ID do Pagamento: <strong>{{ $payment_id ?? 'N/A' }}</strong></p>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-shipping-fast me-2"></i>Próximos Passos</h6>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-check text-success me-2"></i>Pagamento confirmado</li>
                                        <li><i class="fas fa-clock text-warning me-2"></i>Processando envio</li>
                                        <li><i class="fas fa-truck text-info me-2"></i>Preparando para entrega</li>
                                        <li><i class="fas fa-envelope text-primary me-2"></i>Email de confirmação enviado</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6><i class="fas fa-info-circle me-2"></i>Informações Importantes</h6>
                                    <ul class="list-unstyled text-start">
                                        <li><i class="fas fa-file-alt text-secondary me-2"></i>Comprovante disponível</li>
                                        <li><i class="fas fa-qrcode text-secondary me-2"></i>Código de rastreamento</li>
                                        <li><i class="fas fa-calendar text-secondary me-2"></i>Prazo de entrega</li>
                                        <li><i class="fas fa-phone text-secondary me-2"></i>Suporte disponível</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                            <i class="fas fa-home me-2"></i>Voltar ao Dashboard
                        </a>
                        <a href="{{ route('rastreamento') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-2"></i>Rastrear Envio
                        </a>
                        <button class="btn btn-outline-success" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir Comprovante
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 