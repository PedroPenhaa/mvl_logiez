@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-dollar-sign me-2"></i>Taxas de Envio
            </h1>
            <p class="text-muted mb-0">Lista de todas as taxas de envio configuradas</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Taxas: {{ $rates->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($rates->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Serviço</th>
                                <th>Peso Mín</th>
                                <th>Peso Máx</th>
                                <th>Valor</th>
                                <th>Moeda</th>
                                <th>Ativo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rates as $rate)
                            <tr>
                                <td>{{ $rate->id }}</td>
                                <td>{{ $rate->origin_zip_code }}</td>
                                <td>{{ $rate->destination_zip_code }}</td>
                                <td>{{ $rate->service_name }}</td>
                                <td>{{ $rate->min_weight }} kg</td>
                                <td>{{ $rate->max_weight }} kg</td>
                                <td>{{ $rate->currency }} {{ number_format($rate->rate, 2, ',', '.') }}</td>
                                <td>{{ $rate->currency }}</td>
                                <td>
                                    <span class="badge bg-{{ $rate->is_active ? 'success' : 'danger' }}">
                                        {{ $rate->is_active ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $rates->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma taxa encontrada</h5>
                    <p class="text-muted">Não há taxas de envio configuradas.</p>
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
</style>
@endsection 