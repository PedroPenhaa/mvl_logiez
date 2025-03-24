@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Cotação FedEx
                    </h5>
                    <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
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

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="cotacao-fedex-form" action="{{ url('/processar-cotacao-fedex') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Detalhes do Envio</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origem_cep" class="form-label">CEP/Código Postal Origem</label>
                                    <input type="text" class="form-control" id="origem_cep" name="origem_cep" 
                                        value="{{ $dados['origem_cep'] ?? '12345678' }}" required maxlength="20">
                                    <div class="invalid-feedback">
                                        Por favor, informe o CEP/Código Postal de origem.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="origem_pais" class="form-label">País Origem (código 2 letras)</label>
                                    <input type="text" class="form-control" id="origem_pais" name="origem_pais" 
                                        value="{{ $dados['origem_pais'] ?? 'BR' }}" required pattern="[A-Z]{2}" maxlength="2">
                                    <div class="invalid-feedback">
                                        Por favor, informe o código do país de origem (2 letras maiúsculas).
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destino_cep" class="form-label">CEP/Código Postal Destino</label>
                                    <input type="text" class="form-control" id="destino_cep" name="destino_cep"
                                        value="{{ $dados['destino_cep'] ?? '10001' }}" required maxlength="20">
                                    <div class="invalid-feedback">
                                        Por favor, informe o CEP/Código Postal de destino.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destino_pais" class="form-label">País Destino (código 2 letras)</label>
                                    <input type="text" class="form-control" id="destino_pais" name="destino_pais"
                                        value="{{ $dados['destino_pais'] ?? 'US' }}" required pattern="[A-Z]{2}" maxlength="2">
                                    <div class="invalid-feedback">
                                        Por favor, informe o código do país de destino (2 letras maiúsculas).
                                    </div>
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
                                    <input type="number" step="0.1" min="1" max="500" class="form-control" id="altura" name="altura"
                                        value="{{ $dados['altura'] ?? '20' }}" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe uma altura válida (entre 1 e 500 cm).
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="largura" class="form-label">Largura (cm)</label>
                                    <input type="number" step="0.1" min="1" max="500" class="form-control" id="largura" name="largura"
                                        value="{{ $dados['largura'] ?? '20' }}" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe uma largura válida (entre 1 e 500 cm).
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                    <input type="number" step="0.1" min="1" max="500" class="form-control" id="comprimento" name="comprimento"
                                        value="{{ $dados['comprimento'] ?? '20' }}" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe um comprimento válido (entre 1 e 500 cm).
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="peso" class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.1" min="0.1" max="999" class="form-control" id="peso" name="peso"
                                        value="{{ $dados['peso'] ?? '1' }}" required>
                                    <div class="invalid-feedback">
                                        Por favor, informe um peso válido (entre 0.1 e 999 kg).
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="simular" name="simular"
                                {{ isset($dados['simular']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="simular">Forçar simulação (ignorar API real)</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-calculator me-2"></i>Calcular Cotação
                            </button>
                            <button type="button" id="limpar-form" class="btn btn-outline-secondary">
                                <i class="fas fa-broom me-2"></i>Limpar
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

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <p class="mb-0"><strong>Data da Consulta:</strong> {{ $resultado['dataConsulta'] ?? date('Y-m-d H:i:s') }}</p>
                            
                            <div class="d-flex gap-2">
                                <button onclick="window.print();" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-2"></i>Imprimir
                                </button>
                                <a href="{{ url('/exportar-cotacao-pdf') }}?hash={{ md5(json_encode($resultado ?? [])) }}" 
                                   class="btn btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>Baixar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .card-header, form, .btn, .sidebar, .toggle-sidebar {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação do Bootstrap
        const form = document.getElementById('cotacao-fedex-form');
        
        // Loop through inputs to add custom validation 
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
        
        // Limpar formulário
        document.getElementById('limpar-form').addEventListener('click', function() {
            form.reset();
            form.classList.remove('was-validated');
            
            // Reset validation classes
            const allInputs = form.querySelectorAll('input');
            allInputs.forEach(input => {
                input.classList.remove('is-valid');
                input.classList.remove('is-invalid');
            });
            
            // Set default values
            document.getElementById('origem_cep').value = '12345678';
            document.getElementById('origem_pais').value = 'BR';
            document.getElementById('destino_cep').value = '10001';
            document.getElementById('destino_pais').value = 'US';
            document.getElementById('altura').value = '20';
            document.getElementById('largura').value = '20';
            document.getElementById('comprimento').value = '20';
            document.getElementById('peso').value = '1';
        });
        
        // Converter países para maiúsculas
        document.getElementById('origem_pais').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        document.getElementById('destino_pais').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endsection 