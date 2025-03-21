<div class="cotacao-container">
    <div class="progress-tracker mb-4">
        <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="step-labels d-flex justify-content-between mt-2">
            <div class="step active">
                <span class="step-dot"></span>
                <span class="step-text">Cotação</span>
            </div>
            <div class="step">
                <span class="step-dot"></span>
                <span class="step-text">Tipo de Envio</span>
            </div>
            <div class="step">
                <span class="step-dot"></span>
                <span class="step-text">Dados do Envio</span>
            </div>
            <div class="step">
                <span class="step-dot"></span>
                <span class="step-text">Confirmação</span>
            </div>
            <div class="step">
                <span class="step-dot"></span>
                <span class="step-text">Pagamento</span>
            </div>
            <div class="step">
                <span class="step-dot"></span>
                <span class="step-text">Etiqueta</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-calculator me-2 text-primary"></i> Cotação de Envio
        </div>
        <div class="card-body">
            <form id="cotacao-form" action="{{ route('api.cotacao.calcular') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-12">
                        <h5 class="mb-3">Origem e Destino</h5>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="pais_origem" class="form-label">País de Origem</label>
                                <select class="form-select" id="pais_origem" name="pais_origem" required>
                                    <option value="" selected disabled>Selecione o país</option>
                                    <option value="BR">Brasil</option>
                                    <!-- Outros países seriam adicionados aqui -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cep_origem" class="form-label">CEP de Origem</label>
                                <input type="text" class="form-control" id="cep_origem" name="cep_origem" placeholder="Digite o CEP" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pais_destino" class="form-label">País de Destino</label>
                                <select class="form-select" id="pais_destino" name="pais_destino" required>
                                    <option value="" selected disabled>Selecione o país</option>
                                    <option value="US">Estados Unidos</option>
                                    <option value="CA">Canadá</option>
                                    <option value="ES">Espanha</option>
                                    <option value="PT">Portugal</option>
                                    <option value="UK">Reino Unido</option>
                                    <option value="JP">Japão</option>
                                    <!-- Outros países seriam adicionados aqui -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cep_destino" class="form-label">Código Postal de Destino</label>
                                <input type="text" class="form-control" id="cep_destino" name="cep_destino" placeholder="Digite o Código Postal" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-12">
                        <h5 class="mb-3">Dimensões e Peso</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="altura" class="form-label">Altura (cm)</label>
                                <input type="number" step="0.01" min="0.1" class="form-control" id="altura" name="altura" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="largura" class="form-label">Largura (cm)</label>
                                <input type="number" step="0.01" min="0.1" class="form-control" id="largura" name="largura" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                <input type="number" step="0.01" min="0.1" class="form-control" id="comprimento" name="comprimento" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="peso" class="form-label">Peso Real (kg)</label>
                                <input type="number" step="0.01" min="0.1" class="form-control" id="peso" name="peso" required>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i> O peso cubado será calculado automaticamente usando a fórmula: (Altura × Largura × Comprimento) ÷ 200
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <h5 class="mb-3">Informações do Envio</h5>
                        <div class="mb-3">
                            <label for="tipo_envio" class="form-label">Tipo de Envio</label>
                            <select class="form-select" id="tipo_envio" name="tipo_envio" required>
                                <option value="" selected disabled>Selecione o tipo</option>
                                <option value="mercadoria">Mercadoria</option>
                                <option value="documento">Documento</option>
                                <option value="amostra">Amostra</option>
                                <option value="presente">Presente</option>
                            </select>
                        </div>
                        <div class="mb-3" id="conteudo_mercadoria_container" style="display: none;">
                            <label for="conteudo_mercadoria" class="form-label">Descrição da Mercadoria</label>
                            <textarea class="form-control" id="conteudo_mercadoria" name="conteudo_mercadoria" rows="2" placeholder="Descreva a mercadoria"></textarea>
                            <div class="form-text">A descrição detalhada é importante para a liberação alfandegária.</div>
                        </div>
                        <div class="mb-3" id="is_liquido_container" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_liquido" name="is_liquido">
                                <label class="form-check-label" for="is_liquido">
                                    O item contém líquidos
                                </label>
                            </div>
                            <div class="form-text text-warning" id="liquido_warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Envios com conteúdo líquido podem requerer documentação adicional.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-12 mb-3">
                        <h5 class="mb-3">Preferências de Coleta</h5>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="solicitar_coleta" name="solicitar_coleta">
                                <label class="form-check-label" for="solicitar_coleta">
                                    Solicitar coleta no local de origem
                                </label>
                            </div>
                        </div>
                        <div id="data_coleta_container" style="display: none;">
                            <label for="data_coleta" class="form-label">Data Preferencial para Coleta</label>
                            <input type="date" class="form-control" id="data_coleta" name="data_coleta">
                            <div class="form-text">
                                Selecione uma data nos próximos 7 dias úteis.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    <button type="button" id="btn-calcular-cotacao" class="btn btn-primary btn-lg">
                        <i class="fas fa-calculator me-2"></i> Calcular Cotação
                    </button>
                </div>
            </form>
            
            <div class="loader-container text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Calculando...</span>
                </div>
                <p class="mt-2">Calculando as melhores opções de envio...</p>
            </div>
            
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
                                    <div class="col-6 text-end"><span id="peso-cubado-result">0</span> kg</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-6">Peso Real:</div>
                                    <div class="col-6 text-end"><span id="peso-real-result">0</span> kg</div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><strong>Peso Utilizado:</strong></div>
                                    <div class="col-6 text-end"><strong><span id="peso-utilizado-result">0</span> kg</strong></div>
                                </div>
                                <div class="alert alert-info mt-3 mb-0 py-2" role="alert">
                                    <i class="fas fa-info-circle me-2"></i> Utilizamos o maior valor entre o peso cubado e o peso real para cálculo da tarifa.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="card mb-0 h-100">
                            <div class="card-header bg-light">
                                <strong>Opções de Envio</strong>
                            </div>
                            <div class="card-body">
                                <div id="opcoes-servico">
                                    <!-- Opções de serviço serão adicionadas dinamicamente -->
                                    <div class="servico-opcao mb-3 p-3 border rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="servico" id="servico1" checked>
                                            <label class="form-check-label w-100" for="servico1">
                                                <div class="d-flex justify-content-between">
                                                    <strong>DHL Express</strong>
                                                    <span class="badge bg-primary">Recomendado</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <span class="text-muted">Prazo: 3-5 dias úteis</span>
                                                    <strong>R$ 435,90</strong>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="servico-opcao mb-3 p-3 border rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="servico" id="servico2">
                                            <label class="form-check-label w-100" for="servico2">
                                                <div class="d-flex justify-content-between">
                                                    <strong>DHL Economy</strong>
                                                    <span class="badge bg-success">Econômico</span>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <span class="text-muted">Prazo: 7-10 dias úteis</span>
                                                    <strong>R$ 321,75</strong>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-secondary me-2" id="btn-nova-cotacao">
                            <i class="fas fa-redo me-2"></i> Nova Cotação
                        </button>
                        <button class="btn btn-primary nav-section" data-section="envio">
                            <i class="fas fa-arrow-right me-2"></i> Prosseguir para Envio
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cotacao-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .progress-tracker {
        margin-bottom: 30px;
    }
    
    .step {
        text-align: center;
        position: relative;
        width: 16.66%;
    }
    
    .step-dot {
        width: 20px;
        height: 20px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: block;
        margin: 0 auto 8px;
        z-index: 1;
        position: relative;
        border: 3px solid #fff;
    }
    
    .step.active .step-dot {
        background-color: var(--primary-color);
    }
    
    .step-text {
        font-size: 0.8rem;
        color: var(--gray);
    }
    
    .step.active .step-text {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .servico-opcao {
        transition: all 0.2s ease;
    }
    
    .servico-opcao:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .servico-opcao label {
        cursor: pointer;
    }
    
    .form-check-input:checked + .form-check-label .badge {
        background-color: var(--primary-color) !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Cálculo do peso cubado
        function calcularPesoCubado() {
            const altura = parseFloat($('#altura').val()) || 0;
            const largura = parseFloat($('#largura').val()) || 0;
            const comprimento = parseFloat($('#comprimento').val()) || 0;
            
            if (altura > 0 && largura > 0 && comprimento > 0) {
                // Fórmula: (altura x largura x comprimento) / 200
                const pesoCubado = (altura * largura * comprimento) / 200;
                return pesoCubado.toFixed(2);
            }
            return 0;
        }
        
        // Mostrar/esconder os campos de descrição de mercadoria
        $('#tipo_envio').on('change', function() {
            if ($(this).val() === 'mercadoria' || $(this).val() === 'amostra') {
                $('#conteudo_mercadoria_container').show();
                $('#is_liquido_container').show();
            } else {
                $('#conteudo_mercadoria_container').hide();
                $('#is_liquido_container').hide();
            }
        });
        
        // Mostrar aviso para itens líquidos
        $('#is_liquido').on('change', function() {
            if ($(this).is(':checked')) {
                $('#liquido_warning').show();
            } else {
                $('#liquido_warning').hide();
            }
        });
        
        // Mostrar/esconder calendário de coleta
        $('#solicitar_coleta').on('change', function() {
            if ($(this).is(':checked')) {
                $('#data_coleta_container').show();
            } else {
                $('#data_coleta_container').hide();
            }
        });
        
        // Limitar datas disponíveis para coleta (próximos 7 dias úteis)
        const today = new Date();
        let nextWeek = new Date();
        nextWeek.setDate(today.getDate() + 14); // 2 semanas para garantir 7 dias úteis
        
        $('#data_coleta').attr('min', today.toISOString().split('T')[0]);
        $('#data_coleta').attr('max', nextWeek.toISOString().split('T')[0]);
        
        // Calcular cotação
        $('#btn-calcular-cotacao').on('click', function(e) {
            e.preventDefault();
            
            // Validar formulário
            const form = document.getElementById('cotacao-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Mostrar loader
            $('.loader-container').show();
            $('#cotacao-resultado').hide();
            
            // Simular tempo de processamento (3 segundos)
            setTimeout(function() {
                // Calcular peso cubado
                const pesoCubado = calcularPesoCubado();
                const pesoReal = parseFloat($('#peso').val());
                const pesoUtilizado = Math.max(pesoCubado, pesoReal);
                
                // Exibir resultados
                $('#peso-cubado-result').text(pesoCubado);
                $('#peso-real-result').text(pesoReal.toFixed(2));
                $('#peso-utilizado-result').text(pesoUtilizado.toFixed(2));
                
                // Mostrar resultados
                $('.loader-container').hide();
                $('#cotacao-resultado').show();
                
                // Rolar para os resultados
                $('html, body').animate({
                    scrollTop: $('#cotacao-resultado').offset().top - 100
                }, 500);
            }, 3000);
        });
        
        // Botão de nova cotação
        $('#btn-nova-cotacao').on('click', function() {
            $('#cotacao-resultado').hide();
            $('#cotacao-form')[0].reset();
            $('#conteudo_mercadoria_container').hide();
            $('#is_liquido_container').hide();
            $('#data_coleta_container').hide();
            $('html, body').animate({
                scrollTop: $('.cotacao-container').offset().top - 100
            }, 500);
        });
        
        // Selecionar opção de serviço ao clicar em qualquer lugar na linha
        $('.servico-opcao').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    });
</script> 