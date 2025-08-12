@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0" style="color: #6f42c1;">
                <i class="fas fa-map me-2"></i>Gerenciar Endereços
            </h1>
            <p class="text-muted mb-0">Lista de endereços de remetentes e destinatários</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Endereços de Remetentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Endereços de Remetentes ({{ $senderAddresses->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($senderAddresses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Nome</th>
                                <th>Endereço</th>
                                <th>Cidade</th>
                                <th>Estado</th>
                                <th>CEP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($senderAddresses as $address)
                            <tr>
                                <td>{{ $address->id }}</td>
                                <td>{{ $address->user ? $address->user->name : 'N/A' }}</td>
                                <td>{{ $address->name }}</td>
                                <td>{{ $address->address }}</td>
                                <td>{{ $address->city }}</td>
                                <td>{{ $address->state }}</td>
                                <td>{{ $address->postal_code }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $senderAddresses->links() }}
                </div>
            @else
                <p class="text-muted mb-0">Nenhum endereço de remetente encontrado.</p>
            @endif
        </div>
    </div>

    <!-- Endereços de Destinatários -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                Endereços de Destinatários ({{ $recipientAddresses->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($recipientAddresses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Nome</th>
                                <th>Endereço</th>
                                <th>Cidade</th>
                                <th>Estado</th>
                                <th>CEP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipientAddresses as $address)
                            <tr>
                                <td>{{ $address->id }}</td>
                                <td>{{ $address->user ? $address->user->name : 'N/A' }}</td>
                                <td>{{ $address->name }}</td>
                                <td>{{ $address->address }}</td>
                                <td>{{ $address->city }}</td>
                                <td>{{ $address->state }}</td>
                                <td>{{ $address->postal_code }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $recipientAddresses->links() }}
                </div>
            @else
                <p class="text-muted mb-0">Nenhum endereço de destinatário encontrado.</p>
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