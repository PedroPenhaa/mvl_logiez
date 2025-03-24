@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Cotação FedEx
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/processar-cotacao-fedex') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Detalhes do Envio</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origem_cep" class="form-label">CEP/Código Postal Origem</label>
                                    <input type="text" class="form-control" id="origem_cep" name="origem_cep" 
                                        value="{{ $dados['origem_cep'] ?? '12345678' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origem_pais" class="form-label">País Origem (código 2 letras)</label>
                                    <input type="text" class="form-control" id="origem_pais" name="origem_pais" 
                                        value="{{ $dados['origem_pais'] ?? 'BR' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destino_cep" class="form-label">CEP/Código Postal Destino</label>
                                    <input type="text" class="form-control" id="destino_cep" name="destino_cep"
                                        value="{{ $dados['destino_cep'] ?? '10001' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destino_pais" class="form-label">País Destino (código 2 letras)</label>
                                    <input type="text" class="form-control" id="destino_pais" name="destino_pais"
                                        value="{{ $dados['destino_pais'] ?? 'US' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Dimensões e Peso</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="altura" class="form-label">Altura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="altura" name="altura"
                                        value="{{ $dados['altura'] ?? '20' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="largura" class="form-label">Largura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="largura" name="largura"
                                        value="{{ $dados['largura'] ?? '20' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="comprimento" name="comprimento"
                                        value="{{ $dados['comprimento'] ?? '20' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="peso" class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.1" min="0.1" class="form-control" id="peso" name="peso"
                                        value="{{ $dados['peso'] ?? '1' }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="simular" name="simular"
                                {{ isset($dados['simular']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="simular">Forçar simulação (ignorar API real)</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calculator me-2"></i>Calcular Cotação
                            </button>
                        </div>
                    </form>

                    @if(isset($resultado))
                    <div class="mt-5">
                        <h5 class="border-bottom pb-2 mb-3">Resultado da Cotação</h5>
                        
                        @if($resultado['simulado'])
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i> {{ $resultado['mensagem'] ?? 'Cotação simulada' }}
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Peso Real:</strong> {{ $resultado['pesoReal'] }} kg</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Peso Cúbico:</strong> {{ $resultado['pesoCubico'] }} kg</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Peso Utilizado:</strong> {{ $resultado['pesoUtilizado'] }} kg</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Serviço</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Moeda</th>
                                        <th class="text-center">Prazo</th>
                                        <th class="text-center">Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($resultado['cotacoesFedEx'] as $cotacao)
                                    <tr>
                                        <td>{{ $cotacao['servico'] }}</td>
                                        <td class="text-center">{{ $cotacao['valorTotal'] }}</td>
                                        <td class="text-center">{{ $cotacao['moeda'] }}</td>
                                        <td class="text-center">{{ $cotacao['tempoEntrega'] ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $cotacao['dataEntrega'] ?? 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhuma cotação disponível</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <p><strong>Data da Consulta:</strong> {{ $resultado['dataConsulta'] ?? date('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 