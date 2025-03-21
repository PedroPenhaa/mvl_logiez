<div class="card">
    <div class="card-header">
        <i class="fas fa-calculator me-2"></i> Cotação de Envio
    </div>
    <div class="card-body">
        <form id="cotacao-form" action="{{ route('api.cotacao.calcular') }}" method="POST">
            @csrf
            <div class="row mb-4">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                    <label for="origem" class="form-label">Origem</label>
                    <input type="text" class="form-control" id="origem" name="origem" placeholder="Digite a origem" required>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                    <label for="destino" class="form-label">Destino</label>
                    <input type="text" class="form-control" id="destino" name="destino" placeholder="Digite o destino" required>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <label for="altura" class="form-label">Altura (cm)</label>
                    <input type="number" step="0.01" min="0.1" class="form-control" id="altura" name="altura" required>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <label for="largura" class="form-label">Largura (cm)</label>
                    <input type="number" step="0.01" min="0.1" class="form-control" id="largura" name="largura" required>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                    <input type="number" step="0.01" min="0.1" class="form-control" id="comprimento" name="comprimento" required>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <label for="peso" class="form-label">Peso (kg)</label>
                    <input type="number" step="0.01" min="0.1" class="form-control" id="peso" name="peso" required>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Calcular Cotação</button>
            </div>
        </form>
        
        <div class="loader mt-4" id="cotacao-loader"></div>
        
        <div id="cotacao-resultado" style="display: none;" class="mt-4">
            <hr>
            <h4 class="text-center mb-4">Resultado da Cotação</h4>
            
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-3">
                    <div class="card mb-lg-0 h-100">
                        <div class="card-header bg-light">
                            <strong>Informações de Peso</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">Peso Cubado:</div>
                                <div class="col-6 text-end"><span id="peso-cubado">0</span> kg</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">Peso Real:</div>
                                <div class="col-6 text-end"><span id="peso-real">0</span> kg</div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Peso Utilizado:</strong></div>
                                <div class="col-6 text-end"><strong><span id="peso-utilizado">0</span> kg</strong></div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                *Utilizamos o maior valor entre o peso cubado e o peso real para cálculo da tarifa.
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 mb-3">
                    <div class="card mb-0 h-100">
                        <div class="card-header bg-light">
                            <strong>Informações de Envio</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">Valor do Envio:</div>
                                <div class="col-6 text-end"><span id="preco">0 BRL</span></div>
                            </div>
                            <div class="row">
                                <div class="col-6">Prazo Estimado:</div>
                                <div class="col-6 text-end"><span id="prazo">0</span> dias</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <button class="btn btn-primary nav-section" data-section="envio">Prosseguir para Envio</button>
                </div>
            </div>
        </div>
    </div>
</div> 