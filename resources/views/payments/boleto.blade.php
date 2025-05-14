@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-barcode me-2"></i> Boleto Bancário</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Voltar para Detalhes do Pagamento
                        </a>
                        <a href="#" onclick="window.print()" class="btn btn-sm btn-outline-primary float-end">
                            <i class="fas fa-print me-2"></i> Imprimir Boleto
                        </a>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Utilize o código de barras abaixo para efetuar o pagamento do boleto bancário.
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Logiez Soluções em Logística</h6>
                                    <p class="text-muted mb-0">CNPJ: 00.000.000/0001-00</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h6 class="text-danger">Vencimento</h6>
                                    <p class="fw-bold">{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : date('d/m/Y', strtotime('+3 days')) }}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Beneficiário:</strong> Logiez Soluções em Logística</p>
                                    <p class="mb-1"><strong>Pagador:</strong> {{ $payment->payer_name }}</p>
                                    <p class="mb-1"><strong>Documento:</strong> {{ $payment->payer_document ?? 'Não informado' }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h6 class="text-danger">(=) Valor</h6>
                                    <p class="fw-bold">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1"><strong>Data do documento:</strong> {{ $payment->created_at->format('d/m/Y') }}</p>
                                    <p class="mb-1"><strong>Número do documento:</strong> {{ substr($payment->transaction_id, 3) }}</p>
                                    <p class="mb-1"><strong>Espécie do documento:</strong> DM</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6>Instruções</h6>
                                    <p class="mb-1">- Não receber após o vencimento</p>
                                    <p class="mb-1">- Em caso de dúvidas, entre em contato com o suporte</p>
                                    <p class="mb-1">- Pagamento referente ao envio internacional</p>
                                </div>
                            </div>
                            
                            <div class="border p-3 text-center mb-3">
                                <div class="mb-2">
                                    <span class="fw-bold">Linha digitável:</span>
                                </div>
                                <div class="border p-2 bg-light d-flex align-items-center justify-content-center mb-2">
                                    <div class="fw-bold text-monospace" style="font-size: 1.2rem; letter-spacing: 1px;">
                                        {{ $payment->barcode }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-barcode fa-4x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i> Instruções para pagamento</h6>
                            <ol class="card-text mb-0">
                                <li>O boleto pode ser pago em qualquer banco até a data de vencimento</li>
                                <li>Utilize o código de barras ou a linha digitável para pagamento nos canais bancários</li>
                                <li>Após o vencimento, o valor será atualizado com juros e multa</li>
                                <li>Guarde o comprovante de pagamento</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> O envio só será processado após a confirmação do pagamento do boleto. O prazo para confirmação é de até 3 dias úteis.
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

<style type="text/css" media="print">
    .btn, .alert, .card-header, .no-print {
        display: none !important;
    }
    
    body {
        padding: 0;
        margin: 0;
    }
    
    .container {
        width: 100%;
        max-width: 100%;
        padding: 0;
    }
    
    .card {
        border: none;
    }
    
    .card-body {
        padding: 0;
    }
</style>
@endsection 