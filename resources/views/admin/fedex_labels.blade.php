@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-tag me-2"></i>Etiquetas FedEx
            </h1>
            <p class="text-muted mb-0">Lista de todas as etiquetas FedEx geradas</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Etiquetas: {{ $labels->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($labels->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Envio</th>
                                <th>Tracking Number</th>
                                <th>Formato</th>
                                <th>URL</th>
                                <th>Data Criação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labels as $label)
                            <tr>
                                <td>{{ $label->id }}</td>
                                <td>{{ $label->shipment ? $label->shipment->tracking_number : 'N/A' }}</td>
                                <td>{{ $label->tracking_number }}</td>
                                <td>{{ $label->label_format }}</td>
                                <td>
                                    @if($label->label_url)
                                        <a href="{{ $label->label_url }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $label->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $labels->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-tag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma etiqueta encontrada</h5>
                    <p class="text-muted">Não há etiquetas FedEx geradas.</p>
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

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection 