@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i> QR Code PIX</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Voltar para Detalhes do Pagamento
                        </a>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Escaneie o QR Code abaixo com o aplicativo do seu banco para realizar o pagamento via PIX.
                        </div>
                        
                        <div class="py-4">
                            <img src="{{ $payment->qrcode }}" alt="QR Code PIX" class="img-fluid" style="max-width: 300px;">
                        </div>
                        
                        <div class="alert alert-success">
                            <p class="mb-1"><strong>Valor a pagar:</strong> R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                            <p class="mb-0"><strong>Identificador:</strong> {{ $payment->transaction_id }}</p>
                        </div>
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i> Instruções</h6>
                            <ol class="card-text mb-0">
                                <li>Abra o aplicativo do seu banco</li>
                                <li>Escolha a opção PIX</li>
                                <li>Escaneie o QR Code acima</li>
                                <li>Confirme as informações e valor</li>
                                <li>Conclua o pagamento</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> O pagamento será confirmado automaticamente após a conclusão. Não feche esta página até a confirmação do pagamento.
                    </div>
                    
                    @if(app()->environment('local'))
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h6 class="card-title">Ambiente de Desenvolvimento</h6>
                            <p class="card-text">Para testes, você pode simular a conclusão deste pagamento.</p>
                            <a href="{{ route('payments.simulate.callback', ['transactionId' => $payment->transaction_id]) }}" class="btn btn-success">
                                <i class="fas fa-check-circle me-2"></i> Simular Pagamento Concluído
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 