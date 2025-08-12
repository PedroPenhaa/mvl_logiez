@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-receipt me-2"></i>Provas de Entrega
            </h1>
            <p class="text-muted mb-0">Lista de todas as provas de entrega</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Provas: {{ $proofs->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($proofs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Envio</th>
                                <th>Data Entrega</th>
                                <th>Assinatura</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proofs as $proof)
                            <tr>
                                <td>{{ $proof->id }}</td>
                                <td>{{ $proof->shipment ? $proof->shipment->tracking_number : 'N/A' }}</td>
                                <td>{{ $proof->delivery_date ? $proof->delivery_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $proof->signature ?: 'N/A' }}</td>
                                <td>{{ $proof->notes ?: 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $proofs->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma prova encontrada</h5>
                    <p class="text-muted">Não há provas de entrega registradas.</p>
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
</style>
@endsection 