@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cotacao.css') }}?v=1.1">
@endsection

@section('content')
<div class="cotacao-container">
    <!-- Header Section -->
    <div class="page-header-wrapper">
        <div class="page-header-content">
            <div class="header-content">
                <div class="title-section">
                    <div class="title-area">
                        <i class="fas fa-box-open me-2"></i>
                        <h1>Cotação de Envio Internacional</h1>
                    </div>
                    <p class="description">Calcule o valor do seu envio internacional de forma rápida e segura</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card main-card">
        <div class="card-body p-4">
            <form id="cotacao-form" method="POST" action="/calcular-cotacao">
                @csrf
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card feature-card origem-card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Origem
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="origem" name="origem" 
                                           placeholder="00000" required>
                                    <label for="origem">CEP de Origem</label>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="origem_pais" name="origem_pais" 
                                                   placeholder="País" readonly>
                                            <label for="origem_pais">País</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="origem_estado" name="origem_estado" 
                                                   placeholder="Estado" readonly>
                                            <label for="origem_estado">Estado</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="origem_cidade" name="origem_cidade" 
                                                   placeholder="Cidade" readonly>
                                            <label for="origem_cidade">Cidade</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP de origem</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card feature-card destino-card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-flag-checkered me-2"></i>
                                    Destino
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="destino" name="destino" 
                                           placeholder="00000" required>
                                    <label for="destino">CEP de Destino</label>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="destino_pais" name="destino_pais" 
                                                   placeholder="País" readonly>
                                            <label for="destino_pais">País</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="destino_estado" name="destino_estado" 
                                                   placeholder="Estado" readonly>
                                            <label for="destino_estado">Estado</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="destino_cidade" name="destino_cidade" 
                                                   placeholder="Cidade" readonly>
                                            <label for="destino_cidade">Cidade</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP do país de destino</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient-secondary text-white py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-cube me-3 fs-4"></i>
                                    <div>
                                        <h5 class="mb-0 fw-bold">Caixas e Embalagem</h5>
                                        <small class="opacity-75">Defina as dimensões e peso das caixas</small>
                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Formulário de Caixa -->
                                <div class="row g-3 mb-4">
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label for="altura" class="form-label fw-semibold">
                                            <i class="fas fa-arrows-alt-v me-1 text-secondary"></i>Altura
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="altura" min="0" value="0">
                                            <span class="input-group-text bg-light">cm</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label for="largura" class="form-label fw-semibold">
                                            <i class="fas fa-arrows-alt-h me-1 text-secondary"></i>Largura
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="largura" min="0" value="0">
                                            <span class="input-group-text bg-light">cm</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label for="comprimento" class="form-label fw-semibold">
                                            <i class="fas fa-arrows-alt-h me-1 text-secondary"></i>Comprimento
                                    </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="comprimento" min="0" value="0">
                                            <span class="input-group-text bg-light">cm</span>
                                </div>
                            </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label for="peso" class="form-label fw-semibold">
                                            <i class="fas fa-weight-hanging me-1 text-secondary"></i>Peso
                                    </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="peso" min="0" step="0.01" value="0.0">
                                            <span class="input-group-text bg-light">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12 d-flex align-items-end">
                                        <button type="button" class="btn btn-secondary w-100" id="adicionar-caixa">
                                            <i class="fas fa-plus-circle me-2"></i>Adicionar Caixa
                                        </button>
                                    </div>
                                </div>

                                <!-- Alertas -->
                                <div class="alert alert-warning border-0 d-none" id="sem-caixas-alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Nenhuma caixa adicionada. Adicione pelo menos uma caixa para continuar.
                                </div>

                                <!-- Resumo das Caixas -->
                                <div id="resumo-caixas" class="d-none">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold text-secondary">
                                            <i class="fas fa-cubes me-2"></i>Caixas Adicionadas
                                        </h6>
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-cube me-1"></i>
                                            <span id="total-caixas">0</span> caixa(s)
                                        </span>
                            </div>
                                    <div class="row g-3" id="caixas-cards"></div>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Dica Importante</h6>
                                    <p class="mb-0">Para cotação internacional, é importante fornecer as dimensões e peso corretos para obter um valor preciso.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="cotacao-loader" class="text-center my-4" style="display: none;">
                    <div class="loader-container">
                        <div class="spinner-grow text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-3">Calculando as melhores opções de envio...</p>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="calcular-cotacao">
                        <i class="fas fa-calculator me-2"></i> Calcular Cotação
                    </button>
                    <button type="button" id="limpar-form" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-broom me-2"></i>Limpar
                    </button>
                </div>
            </form>
            
            <div id="cotacao-resultado" class="mt-5" style="display: none;"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Garantir que o loader está escondido inicialmente
    $('#cotacao-loader').hide();
    
    // Array para armazenar as caixas
    var caixas = [];
    
    // Limpar formulário
    $('#limpar-form').on('click', function() {
        $('#cotacao-form')[0].reset();
        $('#cotacao-resultado').hide();
        // Limpar campos de endereço
        $('#origem_pais, #origem_estado, #origem_cidade').val('');
        $('#destino_pais, #destino_estado, #destino_cidade').val('');
        // Limpar caixas
        caixas = [];
        renderizarCaixas();
    });
    
    // Função para renderizar as caixas
    function renderizarCaixas() {
        const container = $('#caixas-cards');
        container.empty();
        
        if (caixas.length === 0) {
            $('#sem-caixas-alert').removeClass('d-none');
            $('#resumo-caixas').addClass('d-none');
            return;
        }
        
        $('#sem-caixas-alert').addClass('d-none');
        $('#resumo-caixas').removeClass('d-none');
        
        caixas.forEach(function(caixa, index) {
            const volume = (caixa.altura * caixa.largura * caixa.comprimento / 1000).toFixed(2);
            const quantidade = caixa.quantidade || 1;
            
            const card = `
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-cube text-primary"></i>
                                    </div>
                                    <h6 class="card-title mb-0 text-dark fw-semibold">
                                        Caixa ${index + 1}
                                    </h6>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm btn-remover-caixa" data-index="${index}" style="border-radius: 8px; padding: 4px 8px;">
                                    <i class="fas fa-trash" style="font-size: 12px;"></i>
                                </button>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-purple rounded-3 p-2 text-center" style="background-color: #6f42c1 !important;">
                                        <small class="text-white d-block mb-1">Altura</small>
                                        <span class="fw-bold text-white">${caixa.altura} cm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-purple rounded-3 p-2 text-center" style="background-color: #6f42c1 !important;">
                                        <small class="text-white d-block mb-1">Largura</small>
                                        <span class="fw-bold text-white">${caixa.largura} cm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-purple rounded-3 p-2 text-center" style="background-color: #6f42c1 !important;">
                                        <small class="text-white d-block mb-1">Comprimento</small>
                                        <span class="fw-bold text-white">${caixa.comprimento} cm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-purple rounded-3 p-2 text-center" style="background-color: #6f42c1 !important;">
                                        <small class="text-white d-block mb-1">Peso</small>
                                        <span class="fw-bold text-white">${caixa.peso} kg</span>
                                    </div>
                                </div>
                            </div>
                            
                                                        
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted fw-medium">Quantidade:</small>
                                <div class="btn-group" role="group" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    <button type="button" class="btn btn-outline-primary btn-diminuir" data-index="${index}" style="border-radius: 6px 0 0 6px; border-width: 1px; padding: 2px; font-size: 10px; line-height: 1; width: 40px; min-width: 10px;">
                                        <i class="fas fa-minus" style="font-size: 8px;"></i>
                                    </button>
                                    <span class="btn btn-primary disabled px-2 fw-bold" style="border-radius: 0; min-width: 30px; font-size: 12px; padding: 2px 4px;">${quantidade}</span>
                                    <button type="button" class="btn btn-outline-primary btn-aumentar" data-index="${index}" style="border-radius: 0 6px 6px 0; border-width: 1px; padding: 2px; font-size: 10px; line-height: 1; width: 40px; min-width: 10px;">
                                        <i class="fas fa-plus" style="font-size: 8px;"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-3 p-2">
                                    <small class="text-muted d-block mb-1 fw-medium">Total</small>
                                    <span class="text-success fw-bold">${quantidade} caixa(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.append(card);
        });
        
        // Atualizar contador de caixas (considerando quantidades)
        const totalCaixas = caixas.reduce((total, caixa) => total + (caixa.quantidade || 1), 0);
        $('#total-caixas').text(totalCaixas);
        
        // Adicionar eventos após renderizar
        $('.btn-remover-caixa').on('click', function() {
            const index = $(this).data('index');
            caixas.splice(index, 1);
            renderizarCaixas();
        });
        
        $('.btn-diminuir').on('click', function() {
            const index = $(this).data('index');
            if (caixas[index].quantidade > 1) {
                caixas[index].quantidade--;
                renderizarCaixas();
            }
        });
        
        $('.btn-aumentar').on('click', function() {
            const index = $(this).data('index');
            caixas[index].quantidade++;
            renderizarCaixas();
        });
    }
    
    // Evento de adicionar caixa
    $('#adicionar-caixa').on('click', function() {
        const altura = parseFloat($('#altura').val());
        const largura = parseFloat($('#largura').val());
        const comprimento = parseFloat($('#comprimento').val());
        const peso = parseFloat($('#peso').val());

        // Validação básica
        if (isNaN(altura) || isNaN(largura) || isNaN(comprimento) || isNaN(peso) ||
            altura <= 0 || largura <= 0 || comprimento <= 0 || peso <= 0) {
            alert('Por favor, preencha todas as dimensões da caixa com valores válidos.');
            return;
        }

        // Adicionar a caixa
        const caixa = {
            altura: altura,
            largura: largura,
            comprimento: comprimento,
            peso: peso,
            quantidade: 1
        };

        caixas.push(caixa);

        // Resetar os valores para adicionar nova caixa
        $('#altura').val(0);
        $('#largura').val(0);
        $('#comprimento').val(0);
        $('#peso').val(0);

        // Renderizar as caixas
        renderizarCaixas();
    });
    
    // Variáveis para debounce
    var timeoutOrigem, timeoutDestino;
    
    // Buscar informações do CEP de origem
    $('#origem').on('input', function() {
        clearTimeout(timeoutOrigem);
        var cep = $(this).val().replace(/\D/g, '');
        // Aceitar CEPs brasileiros (8 dígitos) e americanos (5 dígitos)
        if (cep.length >= 5) {
            timeoutOrigem = setTimeout(function() {
                buscarEnderecoPorCEP(cep, 'origem');
            }, 500);
        }
    });
    
    // Buscar informações do CEP de destino
    $('#destino').on('input', function() {
        clearTimeout(timeoutDestino);
        var cep = $(this).val().replace(/\D/g, '');
        // Aceitar CEPs brasileiros (8 dígitos) e americanos (5 dígitos)
        if (cep.length >= 5) {
            timeoutDestino = setTimeout(function() {
                buscarEnderecoPorCEP(cep, 'destino');
            }, 500);
        }
    });
    
    // Função para buscar endereço por CEP via Google Maps API
    function buscarEnderecoPorCEP(cep, tipo) {
        console.log('=== LOG CEP - INÍCIO ===');
        console.log('CEP inserido:', cep);
        console.log('Tipo (origem/destino):', tipo);
        console.log('Timestamp:', new Date().toISOString());
        
        // Mostrar indicador de carregamento
        $('#' + tipo + '_pais').val('Consultando...');
        $('#' + tipo + '_estado').val('Consultando...');
        $('#' + tipo + '_cidade').val('Consultando...');
        
        var requestData = {
            cep: cep
        };
        
        console.log('Dados enviados na requisição:', JSON.stringify(requestData, null, 2));
        
        $.ajax({
            url: '{{ env("APP_ENV") === "local" ? "http://localhost:5000/google-maps-cep-api.php" : "https://app.logiez.com.br/google-maps-cep-api.php" }}',
            type: 'POST',
            data: JSON.stringify(requestData),
            contentType: 'application/json',
            timeout: 10000,
            success: function(response) {
                console.log('=== LOG CEP - SUCESSO ===');
                console.log('Resposta completa da API:', JSON.stringify(response, null, 2));
                console.log('Status da resposta:', response.success);
                console.log('Dados recebidos:', response.data);
                
                if (response.success && response.data) {
                    var data = response.data;
                    console.log('Dados extraídos para preenchimento:');
                    console.log('- País:', data.pais);
                    console.log('- Estado:', data.estado);
                    console.log('- Cidade:', data.cidade);
                    
                    $('#' + tipo + '_pais').val(data.pais || '');
                    $('#' + tipo + '_estado').val(data.estado || '');
                    $('#' + tipo + '_cidade').val(data.cidade || '');
                    
                    console.log('Campos preenchidos com sucesso');
                } else {
                    console.log('=== LOG CEP - ERRO NA RESPOSTA ===');
                    console.log('Resposta não contém dados válidos:', response);
                    // Limpar campos se não encontrou
                    $('#' + tipo + '_pais').val('');
                    $('#' + tipo + '_estado').val('');
                    $('#' + tipo + '_cidade').val('');
                }
                console.log('=== LOG CEP - FIM ===');
            },
            error: function(xhr, status, error) {
                console.log('=== LOG CEP - ERRO HTTP ===');
                console.log('Status HTTP:', status);
                console.log('Erro:', error);
                console.log('Response Text:', xhr.responseText);
                console.log('Response Status:', xhr.status);
                console.log('Response Headers:', xhr.getAllResponseHeaders());
                
                // Tentar parsear JSON da resposta de erro
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    console.log('Resposta de erro parseada:', JSON.stringify(errorResponse, null, 2));
                } catch (e) {
                    console.log('Não foi possível parsear a resposta de erro como JSON');
                }
                
                // Limpar campos em caso de erro
                $('#' + tipo + '_pais').val('');
                $('#' + tipo + '_estado').val('');
                $('#' + tipo + '_cidade').val('');
                console.log('=== LOG CEP - FIM ===');
            }
        });
    }
    
    // Processar envio do formulário via AJAX
    $('#cotacao-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validar se há caixas
        if (caixas.length === 0) {
            alert('Por favor, adicione pelo menos uma caixa.');
            return;
        }
        
        // Mostrar o loader
        $('#cotacao-loader').show();
        
        // Esconder resultados anteriores
        $('#cotacao-resultado').hide();
        
        // Obter os dados do formulário
        var formData = $(this).serialize();
        
        // Adicionar dados das caixas
        formData += '&caixas=' + encodeURIComponent(JSON.stringify(caixas));
        
        // Se há caixas, usar os dados da primeira caixa para dimensões e calcular peso total
        if (caixas.length > 0) {
            var primeiraCaixa = caixas[0];
            formData += '&altura=' + primeiraCaixa.altura;
            formData += '&largura=' + primeiraCaixa.largura;
            formData += '&comprimento=' + primeiraCaixa.comprimento;
            
            // Calcular peso total: soma de (peso da caixa × quantidade) para todas as caixas
            var pesoTotal = 0;
            caixas.forEach(function(caixa) {
                pesoTotal += parseFloat(caixa.peso) * (caixa.quantidade || 1);
            });
            formData += '&peso=' + pesoTotal;
        }
        
        // Enviar para o endpoint de cotação
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
                // Processar a resposta usando a função compartilhada
                processarResposta(response);
            },
            error: function(xhr, status, error) {
                // Esconder o loader
                $('#cotacao-loader').hide();
                
                // Usar a função compartilhada para exibir erros
                exibirErro(xhr);
            }
        });
    });
    
    // Função para processar a resposta
    function processarResposta(response) {
        // Verificar se a resposta está dentro de um objeto 'data'
        if (response.data && response.data.success !== undefined) {
            response = response.data;
        }
        
        if (response.success === false) {
            // Mostrar mensagem de erro específica
            var errorMessage = response.mensagem || 'O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
            var errorTitle = 'Erro na Cotação';
            
            // Verificar se é erro de CEP inválido
            if (response.error_code === 'invalid_origin_postal_code' || response.error_code === 'invalid_destination_postal_code') {
                errorTitle = 'CEP Inválido';
            }
            
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">' + errorTitle + '</h4></div>';
            html += '<div class="card-body">';
            html += '<div class="alert alert-danger">';
            html += '<i class="fas fa-exclamation-circle me-2"></i> ' + errorMessage;
            html += '</div>';
            html += '</div></div>';
            
            $('#cotacao-resultado').html(html).fadeIn();
            return;
        }
        
        // Função para formatar datas em português brasileiro
        function formatarDataPortugues(dataString) {
            if (!dataString) return '';
            
            // Mapeamento de meses em inglês para português
            var mesesIngles = {
                'jan': 'jan', 'feb': 'fev', 'mar': 'mar', 'apr': 'abr',
                'may': 'mai', 'jun': 'jun', 'jul': 'jul', 'aug': 'ago',
                'sep': 'set', 'oct': 'out', 'nov': 'nov', 'dec': 'dez'
            };
            
            // Mapeamento de dias da semana em inglês para português
            var diasIngles = {
                'mon': 'seg', 'tue': 'ter', 'wed': 'qua', 'thu': 'qui',
                'fri': 'sex', 'sat': 'sáb', 'sun': 'dom'
            };
            
            var dataFormatada = dataString.toLowerCase();
            
            // Traduzir dias da semana
            Object.keys(diasIngles).forEach(function(diaIngles) {
                var regex = new RegExp(diaIngles + '\\.', 'gi');
                dataFormatada = dataFormatada.replace(regex, diasIngles[diaIngles] + '.');
            });
            
            // Traduzir meses
            Object.keys(mesesIngles).forEach(function(mesIngles) {
                var regex = new RegExp(mesIngles + '\\.', 'gi');
                dataFormatada = dataFormatada.replace(regex, mesesIngles[mesIngles] + '.');
            });
            
            return dataFormatada;
        }
        
        // Função para formatar o tempo de entrega conforme padrão FedEx
        function formatarTempoEntrega(tempoEntrega, dataChegada) {
            try {
                if (!tempoEntrega) return 'Consultar';
                
                var html = '<div class="tempo-entrega-info">';
                
                // Sempre mostrar uma data - se não vier da API, calcular baseado no serviço
                var dataFormatada = '';
                
                if (dataChegada && dataChegada !== null && dataChegada !== '') {
                    // Se a data vier como timestamp ou formato ISO
                    if (dataChegada.includes('-') || dataChegada.includes('/')) {
                        var data = new Date(dataChegada);
                        if (!isNaN(data.getTime())) {
                            var dia = data.getDate().toString().padStart(2, '0');
                            var mes = (data.getMonth() + 1).toString().padStart(2, '0');
                            var ano = data.getFullYear();
                            dataFormatada = dia + '/' + mes + '/' + ano;
                        }
                    } else {
                        // Se vier em outro formato, usar como está
                        dataFormatada = dataChegada;
                    }
                } else {
                    // Se não vier data da API, calcular baseado no serviço
                    var hoje = new Date();
                    var diasAdicionais = 0;
                    
                    // Determinar dias baseado no serviço
                    if (tempoEntrega.includes('First') || tempoEntrega.includes('8:30')) {
                        diasAdicionais = 2; // Mais rápido
                    } else if (tempoEntrega.includes('Priority Express') || tempoEntrega.includes('10:30')) {
                        diasAdicionais = 3;
                    } else if (tempoEntrega.includes('Priority') || tempoEntrega.includes('5:00 PM')) {
                        diasAdicionais = 4;
                    } else if (tempoEntrega.includes('Economy') || tempoEntrega.includes('Connect Plus') || tempoEntrega.includes('10:00 PM')) {
                        diasAdicionais = 5;
                    } else {
                        diasAdicionais = 4; // Padrão
                    }
                    
                    // Adicionar dias úteis (pular finais de semana)
                    var dataEntrega = new Date(hoje);
                    var diasAdicionados = 0;
                    while (diasAdicionados < diasAdicionais) {
                        dataEntrega.setDate(dataEntrega.getDate() + 1);
                        // Se não for fim de semana (0 = domingo, 6 = sábado)
                        if (dataEntrega.getDay() !== 0 && dataEntrega.getDay() !== 6) {
                            diasAdicionados++;
                        }
                    }
                    
                    var dia = dataEntrega.getDate().toString().padStart(2, '0');
                    var mes = (dataEntrega.getMonth() + 1).toString().padStart(2, '0');
                    var ano = dataEntrega.getFullYear();
                    dataFormatada = dia + '/' + mes + '/' + ano;
                }
                
                // Sempre mostrar a data
                html += '<div class="data-chegada text-muted small mb-1">';
                html += '<i class="fas fa-calendar-alt me-1"></i><strong>Chega dia ' + dataFormatada + '</strong>';
                html += '</div>';
                
                // Formatar o tempo de entrega traduzindo para português
                if (tempoEntrega.includes('ENTREGUE ATÉ') || tempoEntrega.includes('DELIVERED BY')) {
                    var horarioTraduzido = tempoEntrega
                        .replace('ENTREGUE ATÉ', 'Entregue até')
                        .replace('DELIVERED BY', 'Entregue até');
                    
                    html += '<div class="horario-entrega fw-bold">';
                    html += '<i class="fas fa-clock me-1"></i>' + horarioTraduzido;
                    html += '</div>';
                } else if (tempoEntrega.includes('A.M.') || tempoEntrega.includes('P.M.')) {
                    // Tratar formato específico da imagem: "8:30 A.M. IF NO CUSTOMS DELAY"
                    var tempoFormatado = tempoEntrega
                        .replace('A.M.', 'AM')
                        .replace('P.M.', 'PM')
                        .replace('IF NO CUSTOMS DELAY', 'SE NÃO HOUVER ATRASO NA ALFÂNDEGA');
                    
                    html += '<div class="horario-entrega fw-bold">';
                    html += '<i class="fas fa-clock me-1"></i>às ' + tempoFormatado;
                    html += '</div>';
                } else if (tempoEntrega.includes('dias') || tempoEntrega.includes('days')) {
                    var prazoTraduzido = tempoEntrega
                        .replace('days', 'dias')
                        .replace('day', 'dia');
                    
                    html += '<div class="prazo-entrega">';
                    html += '<i class="fas fa-shipping-fast me-1"></i>' + prazoTraduzido;
                    html += '</div>';
                } else if (tempoEntrega.includes('business days') || tempoEntrega.includes('dias úteis')) {
                    var prazoTraduzido = tempoEntrega
                        .replace('business days', 'dias úteis')
                        .replace('business day', 'dia útil');
                    
                    html += '<div class="prazo-entrega">';
                    html += '<i class="fas fa-shipping-fast me-1"></i>' + prazoTraduzido;
                    html += '</div>';
                } else if (tempoEntrega.includes('hours') || tempoEntrega.includes('horas')) {
                    var prazoTraduzido = tempoEntrega
                        .replace('hours', 'horas')
                        .replace('hour', 'hora');
                    
                    html += '<div class="prazo-entrega">';
                    html += '<i class="fas fa-shipping-fast me-1"></i>' + prazoTraduzido;
                    html += '</div>';
                } else {
                    // Traduções mais abrangentes para outros termos
                    var tempoTraduzido = tempoEntrega
                        // Serviços FedEx
                        .replace(/FedEx\s+International\s+First®/gi, 'FedEx International First®')
                        .replace(/FedEx\s+International\s+Priority®\s+Express/gi, 'FedEx International Priority® Express')
                        .replace(/FedEx\s+International\s+Priority®/gi, 'FedEx International Priority®')
                        .replace(/FedEx\s+International\s+Economy®/gi, 'FedEx International Economy®')
                        .replace(/FedEx\s+International\s+Connect\s+Plus/gi, 'FedEx International Connect Plus')
                        
                        // Termos de tempo
                        .replace(/Express/gi, 'Expresso')
                        .replace(/Priority/gi, 'Prioritário')
                        .replace(/Economy/gi, 'Econômico')
                        .replace(/Standard/gi, 'Padrão')
                        .replace(/Next\s+Day/gi, 'Próximo Dia')
                        .replace(/Same\s+Day/gi, 'Mesmo Dia')
                        .replace(/2\s+Day/gi, '2 Dias')
                        .replace(/3\s+Day/gi, '3 Dias')
                        .replace(/Ground/gi, 'Terrestre')
                        .replace(/Air/gi, 'Aéreo')
                        .replace(/International/gi, 'Internacional')
                        .replace(/Domestic/gi, 'Nacional')
                        .replace(/First/gi, 'Primeiro')
                        .replace(/Connect/gi, 'Conect')
                        .replace(/Plus/gi, 'Plus')
                        
                        // Horários específicos
                        .replace(/08:30/gi, '08:30')
                        .replace(/10:30/gi, '10:30')
                        .replace(/17:00/gi, '17:00')
                        .replace(/22:00/gi, '22:00')
                        
                        // Outros termos
                        .replace(/Delivery/gi, 'Entrega')
                        .replace(/Service/gi, 'Serviço')
                        .replace(/Shipping/gi, 'Envio')
                        .replace(/Freight/gi, 'Frete')
                        .replace(/Cargo/gi, 'Carga');
                    
                    html += '<div class="tempo-padrao">' + tempoTraduzido + '</div>';
                }
                
                html += '</div>';
                return html;
            } catch (error) {
                return tempoEntrega || 'Consultar';
            }
        }
        
        if (response.success) {
            // Montar HTML para exibir os resultados
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-success text-white"><h4 class="mb-0">Cotação calculada com sucesso!</h4></div>';
            html += '<div class="card-body">';
            
            // Detalhes do peso
            html += '<div class="row mb-4">';
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Cúbico</h5>';
            // Calcular peso cúbico: (Comprimento x Altura x Largura) / 5000
            var pesoCubico = 0;
            if (caixas.length > 0) {
                var primeiraCaixa = caixas[0];
                pesoCubico = (primeiraCaixa.comprimento * primeiraCaixa.altura * primeiraCaixa.largura) / 5000;
            }
            html += '<p class="fs-4">' + pesoCubico.toFixed(2) + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Real</h5>';
            // Calcular peso real total: soma de (peso da caixa × quantidade) para todas as caixas
            var pesoReal = 0;
            caixas.forEach(function(caixa) {
                pesoReal += parseFloat(caixa.peso) * (caixa.quantidade || 1);
            });
            html += '<p class="fs-4">' + pesoReal.toFixed(2) + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Utilizado</h5>';
            // Peso utilizado é o maior entre peso cúbico e peso real
            var pesoUtilizado = Math.max(pesoCubico, pesoReal);
            html += '<p class="fs-4 fw-bold">' + pesoUtilizado.toFixed(2) + ' kg</p>';
            html += '</div></div></div>';
            html += '</div>';
            
            // Opções de Envio
            if (response.cotacoesFedEx && response.cotacoesFedEx.length > 0) {
                // Filtrar cotações - remover FedEx International First®
                var cotacoesFiltradas = response.cotacoesFedEx.filter(function(cotacao) {
                    return cotacao.servico !== 'FedEx International First®';
                });
                
                html += '<h4 class="mb-3">Opções de Envio</h4>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped table-hover">';
                html += '<thead><tr>';
                html += '<th>Serviço</th>';
                html += '<th>Tempo de Entrega</th>';
                html += '<th>Valor (USD)</th>';
                html += '<th>Valor (BRL)</th>';
                html += '</tr></thead><tbody>';
                
                cotacoesFiltradas.forEach(function(cotacao) {
                    html += '<tr>';
                    html += '<td>' + cotacao.servico + '</td>';
                    html += '<td>';
                    try {
                        html += formatarTempoEntrega(cotacao.tempoEntrega, cotacao.dataEntrega);
                    } catch (error) {
                        html += cotacao.tempoEntrega || 'Consultar';
                    }
                    html += '</td>';
                    html += '<td>' + cotacao.valorTotal + ' ' + cotacao.moeda + '</td>';
                    html += '<td class="fw-bold text-success">R$ ' + (cotacao.valorTotalBRL || '-') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                
                // Exibir a cotação do dólar usada
                if (response.cotacaoDolar) {
                    html += '<div class="alert alert-info mt-3">';
                    html += '<i class="fas fa-dollar-sign me-2"></i> Cotação do dólar utilizada: <strong>R$ ' + 
                            response.cotacaoDolar.toFixed(2).replace('.', ',') + '</strong>';
                    html += '</div>';
                }
            } else {
                html += '<div class="alert alert-warning">Nenhuma opção de envio encontrada para os parâmetros fornecidos.</div>';
            }
            
            html += '<div class="d-flex justify-content-between align-items-center mt-4">';
            html += '<div class="text-muted">Cotação calculada em: ' + response.dataConsulta + '</div>';
            
            html += '<div class="d-flex gap-2">';
            html += '<button id="btn-realizar-envio" class="btn btn-success">';
            html += '<i class="fas fa-shipping-fast me-2"></i>Realizar Envio</button>';
            
            // Botão de download sempre visível
            var pdfUrl = '/exportar-cotacao-pdf?hash=' + (response.hash || '');
            
            // Adicionar parâmetros como fallback
            var formData = $('#cotacao-form').serialize();
            if (formData) {
                pdfUrl += '&' + formData;
            }
            
            html += '<a href="' + pdfUrl + '" ';
            html += 'class="btn btn-primary" download="COTACAO_LOGIEZ.pdf">';
            html += '<i class="fas fa-download me-2"></i>Baixar</a>';
            
            html += '</div></div>';
            
            html += '</div></div>';
            
            // Exibir resultados
            $('#cotacao-resultado').html(html).fadeIn();
            
            // Scroll suave até os resultados
            $('html, body').animate({
                scrollTop: $('#cotacao-resultado').offset().top - 100
            }, 500);
        }
    }
    
    // Função para exibir erro
    function exibirErro(xhr) {
        var errorMessage = 'O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
        var errorTitle = 'Serviço Indisponível';
        
        // Tentar extrair mensagem específica da resposta
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
            errorTitle = 'Erro na Cotação';
            
            // Verificar se é erro de CEP inválido
            if (xhr.responseJSON.error_code === 'invalid_origin_postal_code' || xhr.responseJSON.error_code === 'invalid_destination_postal_code') {
                errorTitle = 'CEP Inválido';
            }
        }
        
        var html = '<div class="card shadow">';
        html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">' + errorTitle + '</h4></div>';
        html += '<div class="card-body">';
        html += '<div class="alert alert-danger">';
        html += '<i class="fas fa-exclamation-circle me-2"></i> ' + errorMessage;
        html += '</div>';
        html += '</div></div>';
        
        $('#cotacao-resultado').html(html).fadeIn();
    }

    // Após exibir resultados, adicionar o evento ao botão Realizar Envio
    $(document).on('click', '#btn-realizar-envio', function() {
        if (typeof loadSection === 'function') {
            loadSection('envio');
        } else {
            window.location.href = '/sections/envio';
        }
    });
});
</script>
@endsection 