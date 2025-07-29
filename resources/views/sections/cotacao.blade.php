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
                                           placeholder="00000-000" required pattern="[0-9]{5}-[0-9]{3}">
                                    <label for="origem">CEP de Origem (Brasil)</label>
                                </div>
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP no formato: 00000-000</small>
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
                                <div class="cep-helper">
                                    <small><i class="fas fa-info-circle"></i> Digite o CEP do país de destino</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card feature-card dimensoes-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-box-open me-2"></i>
                            Dimensões do Pacote
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="altura" name="altura" required>
                                    <label for="altura">
                                        <i class="fas fa-arrows-alt-v me-1"></i> Altura (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="largura" name="largura" required>
                                    <label for="largura">
                                        <i class="fas fa-arrows-alt-h me-1"></i> Largura (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="1" class="form-control" 
                                           id="comprimento" name="comprimento" required>
                                    <label for="comprimento">
                                        <i class="fas fa-arrows-alt me-1"></i> Comprimento (cm)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" step="0.01" min="0.1" class="form-control" 
                                           id="peso" name="peso" required>
                                    <label for="peso">
                                        <i class="fas fa-weight-hanging me-1"></i> Peso (kg)
                                    </label>
                                </div>
                            </div>
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
    
    // Limpar formulário
    $('#limpar-form').on('click', function() {
        $('#cotacao-form')[0].reset();
        $('#cotacao-resultado').hide();
    });
    
    // Processar envio do formulário via AJAX
    $('#cotacao-form').on('submit', function(e) {
        e.preventDefault();
        
        // Mostrar o loader
        $('#cotacao-loader').show();
        
        // Esconder resultados anteriores
        $('#cotacao-resultado').hide();
        
        // Obter os dados do formulário
        var formData = $(this).serialize();
        
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
            // Mostrar mensagem de erro
            var html = '<div class="card shadow">';
            html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">Serviço Indisponível</h4></div>';
            html += '<div class="card-body">';
            html += '<div class="alert alert-danger">';
            html += '<i class="fas fa-exclamation-circle me-2"></i> O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
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
                //console.error('Erro na função formatarTempoEntrega:', error);
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
            html += '<p class="fs-4">' + response.pesoCubico + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Real</h5>';
            html += '<p class="fs-4">' + response.pesoReal + ' kg</p>';
            html += '</div></div></div>';
            
            html += '<div class="col-md-4">';
            html += '<div class="card bg-light">';
            html += '<div class="card-body text-center">';
            html += '<h5>Peso Utilizado</h5>';
            html += '<p class="fs-4 fw-bold">' + response.pesoUtilizado + ' kg</p>';
            html += '</div></div></div>';
            html += '</div>';
            
            // Opções de Envio
            if (response.cotacoesFedEx && response.cotacoesFedEx.length > 0) {
                html += '<h4 class="mb-3">Opções de Envio</h4>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-striped table-hover">';
                html += '<thead><tr>';
                html += '<th>Serviço</th>';
                html += '<th>Tempo de Entrega</th>';
                html += '<th>Valor (USD)</th>';
                html += '<th>Valor (BRL)</th>';
                html += '</tr></thead><tbody>';
                
                response.cotacoesFedEx.forEach(function(cotacao) {
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
            html += '<button onclick="window.print();" class="btn btn-outline-secondary">';
            html += '<i class="fas fa-print me-2"></i>Imprimir</button>';
            
            if (response.hash) {
                html += '<a href="/exportar-cotacao-pdf?hash=' + response.hash + '" ';
                html += 'class="btn btn-danger" target="_blank">';
                html += '<i class="fas fa-file-pdf me-2"></i>Baixar PDF</a>';
            }
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
        var html = '<div class="card shadow">';
        html += '<div class="card-header bg-danger text-white"><h4 class="mb-0">Serviço Indisponível</h4></div>';
        html += '<div class="card-body">';
        html += '<div class="alert alert-danger">';
        html += '<i class="fas fa-exclamation-circle me-2"></i> O serviço da FedEx está temporariamente indisponível. Por favor, tente novamente mais tarde.';
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