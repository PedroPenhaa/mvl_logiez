@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-credit-card me-2"></i>Gerenciar Pagamentos
            </h1>
            <p class="text-muted mb-0">Lista completa de todos os pagamentos do sistema</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Pagamentos: {{ $payments->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Transação ID</th>
                                <th>Método</th>
                                <th>Gateway</th>
                                <th>Valor</th>
                                <th>Moeda</th>
                                <th>Status</th>
                                <th>Pagador</th>
                                <th>Data Pagamento</th>
                                <th>Vencimento</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->user ? $payment->user->name : 'N/A' }}</td>
                                <td>
                                    @if($payment->transaction_id)
                                        <span class="badge bg-info">{{ $payment->transaction_id }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $payment->payment_method == 'credit_card' ? 'primary' : 
                                        ($payment->payment_method == 'boleto' ? 'warning' : 
                                        ($payment->payment_method == 'pix' ? 'success' : 'secondary')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($payment->payment_gateway) }}</td>
                                <td>
                                    <strong>{{ $payment->currency }} {{ number_format($payment->amount, 2, ',', '.') }}</strong>
                                </td>
                                <td>{{ $payment->currency }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $payment->status == 'confirmed' ? 'success' : 
                                        ($payment->status == 'pending' ? 'warning' : 
                                        ($payment->status == 'cancelled' ? 'danger' : 
                                        ($payment->status == 'failed' ? 'dark' : 'info'))) 
                                    }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->payer_name ?: 'N/A' }}</td>
                                <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.payment.details', $payment->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum pagamento encontrado</h5>
                    <p class="text-muted">Não há pagamentos registrados no sistema.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.table th {
    font-size: 0.875rem;
    font-weight: 600;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection 