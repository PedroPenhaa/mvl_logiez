@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-calculator me-2"></i>Gerenciar Cotações
            </h1>
            <p class="text-muted mb-0">Lista completa de todas as cotações do sistema</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Card Principal -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Total de Cotações: {{ $quotes->total() }}
            </h6>
        </div>
        <div class="card-body">
            @if($quotes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Peso (kg)</th>
                                <th>Dimensões</th>
                                <th>Serviço Escolhido</th>
                                <th>Valor Total</th>
                                <th>Moeda</th>
                                <th>Status</th>
                                <th>Data Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotes as $quote)
                            <tr>
                                <td>{{ $quote->id }}</td>
                                <td>{{ $quote->user ? $quote->user->name : 'N/A' }}</td>
                                <td>{{ $quote->origin_zip_code ?: 'N/A' }}</td>
                                <td>{{ $quote->destination_zip_code ?: 'N/A' }}</td>
                                <td>{{ $quote->weight ?: 'N/A' }}</td>
                                <td>
                                    @if($quote->length && $quote->width && $quote->height)
                                        {{ $quote->length }}x{{ $quote->width }}x{{ $quote->height }} cm
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $quote->selected_service ?: 'N/A' }}</td>
                                <td>
                                    @if($quote->total_price)
                                        <strong>{{ $quote->currency ?: 'USD' }} {{ number_format($quote->total_price, 2, ',', '.') }}</strong>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $quote->currency ?: 'USD' }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $quote->status == 'completed' ? 'success' : 
                                        ($quote->status == 'pending' ? 'warning' : 
                                        ($quote->status == 'cancelled' ? 'danger' : 'info')) 
                                    }}">
                                        {{ ucfirst($quote->status) }}
                                    </span>
                                </td>
                                <td>{{ $quote->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info view-quote-details" data-quote-id="{{ $quote->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $quotes->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma cotação encontrada</h5>
                    <p class="text-muted">Não há cotações registradas no sistema.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para detalhes da cotação -->
<div class="modal fade" id="quoteDetailsModal" tabindex="-1" aria-labelledby="quoteDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteDetailsModalLabel">Detalhes da Cotação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quoteDetailsContent">
                <!-- Conteúdo será carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
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

@section('scripts')
<script>
$(document).ready(function() {
    $('.view-quote-details').on('click', function() {
        var quoteId = $(this).data('quote-id');
        // Aqui você pode implementar uma chamada AJAX para buscar os detalhes da cotação
        // Por enquanto, vamos apenas mostrar um alerta
        alert('Detalhes da cotação ID: ' + quoteId + '\n\nFuncionalidade em desenvolvimento.');
        
        // Exemplo de implementação AJAX:
        /*
        $.get('/admin/quotes/' + quoteId + '/details', function(data) {
            $('#quoteDetailsContent').html(data);
            $('#quoteDetailsModal').modal('show');
        });
        */
    });
});
</script>
@endsection 