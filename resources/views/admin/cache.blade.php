@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-database me-2"></i>Dados em Cache
            </h1>
            <p class="text-muted mb-0">Lista de todos os dados armazenados em cache</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Registros: {{ $cache->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($cache->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Chave</th>
                                <th>Valor</th>
                                <th>Expira em</th>
                                <th>Data Criação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cache as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->key }}</td>
                                <td>
                                    @if(strlen($item->value) > 50)
                                        {{ Str::limit($item->value, 50) }}...
                                    @else
                                        {{ $item->value }}
                                    @endif
                                </td>
                                <td>{{ $item->expiration ? $item->expiration->format('d/m/Y H:i') : 'Nunca' }}</td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $cache->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum registro encontrado</h5>
                    <p class="text-muted">Não há dados em cache.</p>
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