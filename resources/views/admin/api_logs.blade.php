@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-code me-2"></i>Logs da API
            </h1>
            <p class="text-muted mb-0">Lista de todos os logs de chamadas da API</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Logs: {{ $logs->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Endpoint</th>
                                <th>Método</th>
                                <th>Status</th>
                                <th>Tempo (ms)</th>
                                <th>IP</th>
                                <th>Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->endpoint }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->method == 'GET' ? 'success' : ($log->method == 'POST' ? 'primary' : 'warning') }}">
                                        {{ $log->method }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->status_code >= 200 && $log->status_code < 300 ? 'success' : ($log->status_code >= 400 ? 'danger' : 'warning') }}">
                                        {{ $log->status_code }}
                                    </span>
                                </td>
                                <td>{{ $log->response_time }}</td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-code fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum log encontrado</h5>
                    <p class="text-muted">Não há logs da API registrados.</p>
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