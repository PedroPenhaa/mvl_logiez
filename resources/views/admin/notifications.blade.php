@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-bell me-2"></i>Notificações
            </h1>
            <p class="text-muted mb-0">Lista de todas as notificações do sistema</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Notificações: {{ $notifications->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Título</th>
                                <th>Mensagem</th>
                                <th>Tipo</th>
                                <th>Lida</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                            <tr>
                                <td>{{ $notification->id }}</td>
                                <td>{{ $notification->user ? $notification->user->name : 'N/A' }}</td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ Str::limit($notification->message, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $notification->type == 'success' ? 'success' : ($notification->type == 'error' ? 'danger' : 'info') }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $notification->is_read ? 'success' : 'warning' }}">
                                        {{ $notification->is_read ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma notificação encontrada</h5>
                    <p class="text-muted">Não há notificações registradas.</p>
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