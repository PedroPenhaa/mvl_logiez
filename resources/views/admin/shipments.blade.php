@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-shipping-fast me-2"></i>Gerenciar Envios
            </h1>
            <p class="text-muted mb-0">Lista completa de todos os envios do sistema</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Envios: {{ $shipments->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($shipments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Tracking</th>
                                <th>Transportadora</th>
                                <th>Serviço</th>
                                <th>Status</th>
                                <th>Peso (kg)</th>
                                <th>Dimensões</th>
                                <th>Valor</th>
                                <th>Data Envio</th>
                                <th>Data Entrega</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $shipment)
                            <tr>
                                <td>{{ $shipment->id }}</td>
                                <td>{{ $shipment->user ? $shipment->user->name : 'N/A' }}</td>
                                <td>
                                    @if($shipment->tracking_number)
                                        <span class="badge bg-info">{{ $shipment->tracking_number }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $shipment->carrier }}</td>
                                <td>{{ $shipment->service_name ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $shipment->status == 'delivered' ? 'success' : 
                                        ($shipment->status == 'pending' ? 'warning' : 
                                        ($shipment->status == 'cancelled' ? 'danger' : 'info')) 
                                    }}">
                                        {{ ucfirst($shipment->status) }}
                                    </span>
                                </td>
                                <td>{{ $shipment->package_weight }}</td>
                                <td>
                                    {{ $shipment->package_length }}x{{ $shipment->package_width }}x{{ $shipment->package_height }} cm
                                </td>
                                <td>
                                    @if($shipment->total_price_brl)
                                        R$ {{ number_format($shipment->total_price_brl, 2, ',', '.') }}
                                    @elseif($shipment->total_price)
                                        $ {{ number_format($shipment->total_price, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $shipment->ship_date ? $shipment->ship_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $shipment->delivery_date ? $shipment->delivery_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.shipment.details', $shipment->id) }}" class="btn btn-sm btn-info">
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
                    {{ $shipments->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum envio encontrado</h5>
                    <p class="text-muted">Não há envios registrados no sistema.</p>
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