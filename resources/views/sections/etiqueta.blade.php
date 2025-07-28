@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cotacao.css') }}">
<style>
    /* Estilos específicos para a tela de etiqueta */
    body {
        background: #f6f7fb;
        font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
        color: #3d246c;
    }
    
    /* Cards estilo cotação */
    .card.border-light {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        background: #fff !important;
        margin-bottom: 1.5rem !important;
    }
    
    .card.border-light:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }
    
    .card.border-light .card-header {
        background: linear-gradient(135deg, #6f42c1 0%, #8e44ad 100%) !important;
        color: white !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 0.75rem 1rem !important;
        border: none !important;
    }
    
    .card.border-light .card-header h5 {
        font-size: 1rem !important;
        margin: 0 !important;
        font-weight: 600 !important;
    }
    
    .card.border-light .card-body {
        padding: 1rem !important;
        border-radius: 0 0 12px 12px !important;
    }
    
    /* Inputs/selects padrão cotação */
    .form-control, .form-select {
        border-radius: 14px !important;
        border: 1.5px solid #e0e0e0 !important;
        background: #fff !important;
        color: #3d246c !important;
        font-size: 1.08rem !important;
        padding: 5px 20px !important;
        height: 50px !important;
        box-shadow: 0 2px 8px 0 rgba(111,66,193,0.04) !important;
        transition: border 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: left;
        font-size: 14px !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #8f5be8 !important;
        box-shadow: 0 0 0 2px #e9d6ff !important;
        background: #fff !important;
        color: #3d246c !important;
    }
    .form-control::placeholder {
        color: #6f42c1 !important;
        opacity: 0.7;
        font-weight: 400;
    }
    
    /* Botões padrão cotação */
    .btn-primary, .btn-success, .btn-outline-primary {
        background: linear-gradient(90deg, #8f5be8 0%, #6f42c1 100%) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 600 !important;
        border-radius: 14px !important;
        box-shadow: 0 2px 8px 0 rgba(111, 66, 193, 0.10) !important;
        transition: background 0.2s, box-shadow 0.2s;
        min-height: 56px;
        font-size: 1.08rem;
        padding: 0 2.5rem;
    }
    .btn-primary:hover, .btn-success:hover, .btn-outline-primary:hover {
        background: linear-gradient(90deg, #6f42c1 0%, #8f5be8 100%) !important;
        color: #fff !important;
        box-shadow: 0 4px 16px 0 rgba(111, 66, 193, 0.18) !important;
    }
    .btn-outline-secondary {
        border-color: #8f5be8 !important;
        color: #6f42c1 !important;
        background: #f3e7ff !important;
        font-weight: 500 !important;
        border-radius: 14px !important;
        min-height: 56px;
        font-size: 1.08rem;
    }
    .btn-outline-secondary:hover {
        background: #8f5be8 !important;
        color: #fff !important;
    }
    
    /* Tabela de etiquetas */
    .table {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px 0 rgba(111, 66, 193, 0.04);
    }
    .table th {
        background: #f3e7ff;
        color: #6f42c1;
        font-weight: 700;
        border: none;
    }
    .table td {
        border: none;
        color: #3d246c;
        vertical-align: middle;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
        background: #faf7ff;
    }
    .table-hover > tbody > tr:hover {
        background: #e9d6ff;
        color: #6f42c1;
    }
    
    /* Badges e status */
    .badge.bg-success {
        background: #28a745 !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 0.3em 0.7em;
    }
    
    /* Alertas */
    .alert-info {
        background: #e9d6ff;
        color: #6f42c1;
        border: none;
        font-weight: 500;
    }
    .alert-success {
        background: #d1ffe7;
        color: #1b7c4b;
        border: none;
    }
    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: none;
    }
    .alert-danger {
        background: #ffe1e1;
        color: #c82333;
        border: none;
    }
    
    /* Modais */
    .modal-content {
        border-radius: 16px;
        border: 2px solid #a084e8;
        box-shadow: 0 4px 24px 0 rgba(111, 66, 193, 0.10);
    }
    .modal-header {
        background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%) !important;
        color: #fff !important;
        border-radius: 16px 16px 0 0;
    }
    .modal-footer .btn {
        min-width: 120px;
    }
    
    /* Paginação */
    .pagination .page-link {
        color: #6f42c1;
        border-color: #e9d6ff;
        background: #fff;
    }
    .pagination .page-link:hover {
        color: #fff;
        background: #6f42c1;
        border-color: #6f42c1;
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%);
        border-color: #6f42c1;
        color: #fff;
    }
    
    /* Responsividade */
    @media (max-width: 767px) {
        .card-body {
            padding: 1.2rem 0.7rem !important;
        }
        .form-control, .form-select {
            font-size: 1rem !important;
            padding: 0.7rem 0.8rem !important;
            height: 44px !important;
        }
        .btn-primary, .btn-success, .btn-outline-primary, .btn-outline-secondary {
            min-height: 44px !important;
            font-size: 1rem !important;
            padding: 0 1.2rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Adicionar meta tag CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Header Section -->
<div class="page-header-wrapper">
    <div class="page-header-content">
        <div class="header-content">
            <div class="title-section">
                <div class="title-area">
                    <i class="fas fa-tag me-2"></i>
                    <h1>Etiqueta de Envio</h1>
                </div>
                <p class="description">Gere e imprima etiquetas para seus envios realizados ou busque por código de envio</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i> Gere e imprima etiquetas para seus envios realizados ou busque por código de envio.
        </div>
        
        <!-- Formulário de busca de etiqueta -->
        <div id="busca-etiqueta" class="mb-4">
            <div class="card border-light shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0" style="color: white;">
                        <i class="fas fa-search me-2"></i>
                        Buscar Etiqueta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="codigo-envio" placeholder="Digite o código de envio ou rastreamento">
                                <button class="btn btn-primary" type="button" id="buscar-etiqueta-btn">
                                    <i class="fas fa-search me-2"></i> Buscar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-outline-primary" id="mostrar-todos-btn">
                                <i class="fas fa-list me-2"></i> Mostrar Todos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Etiquetas disponíveis -->
        <div id="etiquetas-disponiveis">
            <div class="card border-light shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0" style="color: white;">
                        <i class="fas fa-tags me-2"></i>
                        Etiquetas Disponíveis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Código de Envio</th>
                                    <th>Data</th>
                                    <th>Destinatário</th>
                                    <th>Destino</th>
                                    <th>Status</th>
                                    <th>Visualizar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Nenhuma etiqueta fixa. Use o campo de busca acima para consultar etiquetas reais da FedEx. -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <nav aria-label="Paginação de etiquetas">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Próximo</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sem Etiquetas Encontradas (inicialmente oculto) -->
        <div id="sem-etiquetas" class="text-center py-5" style="display: none;">
            <div class="card border-light shadow-sm">
                <div class="card-body">
                    <i class="fas fa-tag fa-4x text-muted mb-3"></i>
                    <h5>Nenhuma etiqueta encontrada</h5>
                    <p>Não encontramos etiquetas com o código informado.</p>
                    <button class="btn btn-outline-primary mt-2" id="voltar-etiquetas-btn">
                        <i class="fas fa-arrow-left me-2"></i> Voltar para todas etiquetas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização de Etiqueta -->
<div class="modal fade" id="etiqueta-modal" tabindex="-1" aria-labelledby="etiquetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="etiquetaModalLabel">Etiqueta de Envio FedEx</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="etiqueta-preview" class="border p-3">
                    <!-- Conteúdo da Etiqueta com embed da etiqueta FedEx -->
                    <div class="row">
                        <div class="col-6">
                            <img src="https://www.fedex.com/content/dam/fedex-com/logos/logo.png" alt="FedEx Logo" class="img-fluid mb-3" style="max-height: 60px;">
                        </div>
                        <div class="col-6 text-end">
                            <h6 class="fw-bold mb-0">AIRWAY BILL</h6>
                            <div id="etiqueta-codigo" class="fs-4 fw-bold"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-3" id="simulacao-aviso" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i> Esta é uma simulação de etiqueta. Em produção, você receberá uma etiqueta real da FedEx.
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">REMETENTE:</div>
                                <div id="etiqueta-remetente" class="fw-bold"></div>
                                <div id="etiqueta-endereco-remetente"></div>
                                <div id="etiqueta-cidade-remetente"></div>
                                <div id="etiqueta-pais-remetente"></div>
                                <div id="etiqueta-telefone-remetente"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">DESTINATÁRIO:</div>
                                <div id="etiqueta-destinatario" class="fw-bold"></div>
                                <div id="etiqueta-endereco-destinatario"></div>
                                <div id="etiqueta-cidade-destinatario"></div>
                                <div id="etiqueta-pais-destinatario"></div>
                                <div id="etiqueta-telefone-destinatario"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">SERVIÇO:</div>
                                <div id="etiqueta-servico" class="fw-bold"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">PESO:</div>
                                <div id="etiqueta-peso" class="fw-bold"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">DATA:</div>
                                <div id="etiqueta-data" class="fw-bold"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center my-4">
                        <h5 class="mb-3">Etiqueta FedEx</h5>
                        <div id="etiqueta-iframe-container">
                            <!-- A etiqueta real ou o QR code serão exibidos aqui -->
                        </div>
                    </div>
                    
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-info-circle me-2"></i> Número de rastreamento: <strong id="etiqueta-tracking"></strong>
                        <div class="mt-2">
                            <a href="#" id="link-rastreamento" class="btn btn-sm btn-primary">
                                <i class="fas fa-search me-2"></i> Rastrear Envio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" class="btn btn-info" id="download-etiqueta-btn" target="_blank">
                    <i class="fas fa-download me-2"></i> Baixar Etiqueta
                </a>
                <button type="button" class="btn btn-success" id="imprimir-modal-btn">
                    <i class="fas fa-print me-2"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Invoice -->
<div class="modal fade" id="invoice-modal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Commercial Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="invoice-content"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-info" id="download-invoice-pdf-btn" target="_blank">
                    <i class="fas fa-download me-2"></i> Baixar PDF
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Garantir que o código só execute após o jQuery estar carregado
    $(function() {
        console.log('Documento pronto - Iniciando script');

        // ========== FUNÇÕES AUXILIARES ==========
        // Loader
        function showLoader() {
            if ($('#loader-modal').length === 0) {
                $('body').append('<div id="loader-modal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;"><div class="spinner-border text-primary" style="width:4rem;height:4rem;" role="status"></div></div>');
            }
        }

        function hideLoader() {
            $('#loader-modal').remove();
        }

        // Alertas
        function showAlert(type, message) {
            let alertClass = 'alert-info';
            if (type === 'success') alertClass = 'alert-success';
            if (type === 'warning') alertClass = 'alert-warning';
            if (type === 'danger') alertClass = 'alert-danger';
            $('.alert').remove();
            $('.card-body').first().prepend('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }

        // Atualizar tabela
        function atualizarTabelaEtiqueta(dados) {
            const tbody = $('table tbody');
            tbody.empty();

            const row = `
                <tr>
                    <td>${dados.trackingNumber}</td>
                    <td>${new Date(dados.shipDate).toLocaleDateString('pt-BR')}</td>
                    <td>${dados.recipient.name}</td>
                    <td>${dados.recipient.city}, ${dados.recipient.country}</td>
                    <td><span class="badge bg-success">Ativo</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary visualizar-etiqueta" data-codigo="${dados.trackingNumber}">
                            <i class="fas fa-eye me-1"></i> Visualizar
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        }

        // Carregar etiquetas
        function carregarEtiquetasUsuario() {
            showLoader();
            $.ajax({
                url: '/api/sections/etiquetas-usuario',
                method: 'GET',
                success: function(response) {
                    hideLoader();
                    if (response.success && response.etiquetas.length > 0) {
                        const tbody = $('table tbody');
                        tbody.empty();
                        response.etiquetas.forEach(function(etiqueta) {
                            const row = `
                                <tr>
                                    <td>${etiqueta.tracking_number}</td>
                                    <td>${etiqueta.ship_date ? new Date(etiqueta.ship_date).toLocaleDateString('pt-BR') : ''}</td>
                                    <td>${etiqueta.recipient_name}</td>
                                    <td>${etiqueta.recipient_city}, ${etiqueta.recipient_country}</td>
                                    <td><span class="badge bg-success">Ativo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary visualizar-etiqueta" data-codigo="${etiqueta.tracking_number}">
                                            <i class="fas fa-eye me-1"></i> Etiqueta
                                        </button>
                                        <button class="btn btn-sm btn-secondary btn-invoice ms-1" type="button" data-id="${etiqueta.id}">
                                            <i class="fas fa-file-invoice me-1"></i> Invoice
                                        </button>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                        $('#sem-etiquetas').hide();
                        $('#etiquetas-disponiveis').show();
                    } else {
                        $('table tbody').empty();
                        $('#sem-etiquetas').show();
                        $('#etiquetas-disponiveis').hide();
                    }
                },
                error: function() {
                    hideLoader();
                    showAlert('danger', 'Erro ao buscar etiquetas do usuário.');
                }
            });
        }

        // ========== INICIALIZAÇÃO ==========
        // Verificar se o botão existe
        const buscarBtn = document.querySelector('#buscar-etiqueta-btn');
        if (!buscarBtn) {
            console.error('Botão de busca não encontrado na página!');
            return;
        }
        console.log('Botão de busca encontrado:', buscarBtn);

        // Verificar se o input existe
        const codigoInput = document.querySelector('#codigo-envio');
        if (!codigoInput) {
            console.error('Input de código não encontrado na página!');
            return;
        }
        console.log('Input de código encontrado:', codigoInput);

        // Função para exibir etiqueta no modal
        function exibirEtiqueta(envio) {
            console.log("Exibindo etiqueta com dados:", envio);
            
            // Preencher os dados da etiqueta no modal
            $('#etiqueta-codigo').text(envio.trackingNumber);
            $('#etiqueta-tracking').text(envio.trackingNumber);
            $('#link-rastreamento').attr('href', `/rastreamento?codigo=${envio.trackingNumber}`);
            
            // Exibir ou esconder aviso de simulação
            $('#simulacao-aviso').toggle(envio.simulado === true);
            
            // Mostrar data de criação formatada
            const dataCriacao = new Date(envio.dataCriacao);
            $('#etiqueta-data').text(
                dataCriacao.toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                })
            );
            
            // Mostrar serviço contratado
            const servicosNomes = {
                'FEDEX_INTERNATIONAL_PRIORITY': 'FedEx International Priority',
                'FEDEX_INTERNATIONAL_ECONOMY': 'FedEx International Economy',
                'INTERNATIONAL_PRIORITY_EXPRESS': 'International Priority Express',
                'INTERNATIONAL_PRIORITY': 'International Priority',
                'INTERNATIONAL_ECONOMY': 'International Economy'
            };
            
            $('#etiqueta-servico').text(servicosNomes[envio.servicoContratado] || envio.servicoContratado);
            
            // Verificar se temos dados completos do envio
            if (envio.dados) {
                const pesoTotal = envio.dados.pesoTotal || '0.00';
                $('#etiqueta-peso').text(pesoTotal + ' kg');
                
                // Preencher dados do remetente, se disponíveis
                if (envio.dados.remetente) {
                    const remetente = envio.dados.remetente;
                    $('#etiqueta-remetente').text(remetente.nome);
                    $('#etiqueta-endereco-remetente').text(remetente.endereco + (remetente.complemento ? ', ' + remetente.complemento : ''));
                    $('#etiqueta-cidade-remetente').text(remetente.cidade + ', ' + remetente.estado + ' - ' + remetente.cep);
                    $('#etiqueta-pais-remetente').text(remetente.pais);
                    $('#etiqueta-telefone-remetente').text('Tel: ' + remetente.telefone);
                }
                
                // Preencher dados do destinatário, se disponíveis
                if (envio.dados.destinatario) {
                    const destinatario = envio.dados.destinatario;
                    $('#etiqueta-destinatario').text(destinatario.nome);
                    $('#etiqueta-endereco-destinatario').text(destinatario.endereco + (destinatario.complemento ? ', ' + destinatario.complemento : ''));
                    $('#etiqueta-cidade-destinatario').text(destinatario.cidade + ', ' + destinatario.estado + ' - ' + destinatario.cep);
                    $('#etiqueta-pais-destinatario').text(destinatario.pais);
                    $('#etiqueta-telefone-destinatario').text('Tel: ' + destinatario.telefone);
                }
            }
            
            // Exibir a etiqueta (iframe ou QR code)
            const iframeContainer = $('#etiqueta-iframe-container');
            iframeContainer.empty();
            
            if (envio.labelUrl) {
                if (envio.labelUrl.includes('api.qrserver.com')) {
                    // É um QR code (simulação)
                    iframeContainer.html(`
                        <img src="${envio.labelUrl}" class="img-fluid" style="max-width: 300px;">
                        <p class="mt-2 text-muted">QR Code para o número de rastreamento</p>
                    `);
                } else {
                    // É uma etiqueta real
                    iframeContainer.html(`
                        <iframe src="${envio.labelUrl}" width="100%" height="500px" frameborder="0"></iframe>
                    `);
                }
                
                // Atualizar link de download
                $('#download-etiqueta-btn').attr('href', envio.labelUrl);
            } else {
                // Sem URL de etiqueta, mostrar mensagem
                iframeContainer.html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        Etiqueta não disponível para visualização. Use o número de rastreamento para acompanhar seu envio.
                    </div>
                `);
                
                // Desabilitar botão de download
                $('#download-etiqueta-btn').addClass('disabled').removeAttr('href');
            }
            
            // Abrir o modal
            const etiquetaModal = new bootstrap.Modal(document.getElementById('etiqueta-modal'));
            etiquetaModal.show();
        }

        // ========== HANDLERS DE EVENTOS ==========
        // Handler do botão de busca
        function handleBuscarClick(e) {
            e.preventDefault();
            console.log('Botão clicado - handleBuscarClick executado');
            
            const codigo = codigoInput.value.trim();
            console.log('Código digitado:', codigo);
            
            if (!codigo) {
                showAlert('warning', 'Por favor, digite um código de envio válido.');
                return;
            }
            
            // Verificar token CSRF
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                console.error('Token CSRF não encontrado!');
                showAlert('danger', 'Erro de segurança: Token CSRF não encontrado.');
                return;
            }
            
            console.log('Iniciando busca para o código:', codigo);
            showLoader();
            
            // Fazer requisição para buscar a etiqueta pelo código na FedEx
            fetch('/api/fedex/etiqueta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ codigo: codigo })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Resposta recebida:', data);
                hideLoader();
                if (data.success) {
                    // ABRIR ETIQUETA EM NOVA ABA
                    window.open(data.labelUrl, '_blank');
                    
                    showAlert('success', 'Etiqueta encontrada!');
                    atualizarTabelaEtiqueta(data);
                    $('#sem-etiquetas').hide();
                    $('#etiquetas-disponiveis').show();
                } else {
                    showAlert('warning', 'Etiqueta não encontrada: ' + (data.message || 'Verifique o código informado.'));
                    $('#sem-etiquetas').show();
                    $('#etiquetas-disponiveis').hide();
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                hideLoader();
                showAlert('danger', 'Erro ao buscar a etiqueta.');
            });
        }

        // Adicionar eventos
        buscarBtn.addEventListener('click', handleBuscarClick);
        
        codigoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('Tecla Enter pressionada');
                e.preventDefault();
                handleBuscarClick(e);
            }
        });

        // Handler para visualizar etiqueta
        $(document).on('click', '.visualizar-etiqueta', function() {
            const codigo = $(this).data('codigo');
            showLoader();

            // Fazer requisição direta para a etiqueta
            $.ajax({
                url: '/api/fedex/etiqueta',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    codigo: codigo
                }),
                success: function(response) {
                    hideLoader();
                    if (response.success && response.labelUrl) {
                        // ABRIR ETIQUETA EM NOVA ABA
                        window.open(response.labelUrl, '_blank');
                        
                        // Salvar os dados da etiqueta no banco de dados
                        $.ajax({
                            url: '/api/fedex/save-label',
                            method: 'POST',
                            data: {
                                tracking_number: response.trackingNumber,
                                label_url: response.labelUrl,
                                status: 'active',
                                api_response: JSON.stringify(response),
                                service_type: response.serviceName,
                                recipient_name: response.recipient.name,
                                recipient_address: response.recipient.address,
                                recipient_city: response.recipient.city,
                                recipient_state: response.recipient.state,
                                recipient_country: response.recipient.country,
                                recipient_postal_code: response.recipient.postalCode
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function(xhr) {
                                console.error('Erro ao salvar etiqueta:', xhr);
                            }
                        });

                        exibirEtiqueta(response);
                    } else {
                        showAlert('warning', 'Etiqueta não encontrada ou erro na FedEx.');
                    }
                },
                error: function(xhr) {
                    console.error('Erro na requisição de etiqueta:', xhr);
                    hideLoader();
                    let errorMessage = 'Erro ao buscar etiqueta FedEx.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ' ' + xhr.responseJSON.message;
                    }
                    showAlert('danger', errorMessage);
                }
            });
        });

        // Handler para o botão Invoice
        $(document).on('click', '.btn-invoice', function() {
            const shipmentId = $(this).data('id');
            showLoader();
            $.ajax({
                url: `/api/sections/invoice/${shipmentId}`,
                method: 'GET',
                success: function(response) {
                    hideLoader();
                    if (response.success && response.invoice) {
                        renderInvoiceModal(response.invoice);
                        const invoiceModal = new bootstrap.Modal(document.getElementById('invoice-modal'));
                        invoiceModal.show();
                    } else {
                        showAlert('warning', 'Não foi possível gerar o invoice.');
                    }
                },
                error: function() {
                    hideLoader();
                    showAlert('danger', 'Erro ao buscar invoice.');
                }
            });
        });

        // Handler para imprimir etiqueta
        $('#imprimir-modal-btn').on('click', function() {
            const iframe = document.querySelector('#etiqueta-iframe-container iframe');
            if (iframe) {
                iframe.contentWindow.print();
            }
        });

        // Handler para mostrar todas as etiquetas
        $('#mostrar-todos-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
            $('#codigo-envio').val('');
            carregarEtiquetasUsuario();
        });

        // Handler para voltar para todas as etiquetas
        $('#voltar-etiquetas-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
            $('#codigo-envio').val('');
            carregarEtiquetasUsuario();
        });

        // Carregar dados iniciais
        carregarEtiquetasUsuario();
    });

    function renderInvoiceModal(invoice) {
        // Monta o HTML do invoice conforme o exemplo enviado
        let html = `
        <div class="border p-3 bg-white">
            <div class="row mb-2">
                <div class="col-9 text-center">
                    <strong>LS COMÉRCIO ATACADISTA E VAREJISTA LTDA</strong><br>
                    Endereço: Rua 4, Pq Res. Dona Chiquinha, Cosmópolis - SP - Brazil<br>
                    Contato: +55(19) 98116-6445 / envios@logiez.com.br<br>
                    CNPJ: 48.103.206/0001-73
                </div>
                <div class="col-3 text-end">
                    <h5>COMMERCIAL INVOICE</h5>
                </div>
            </div>
            <table class="table table-bordered align-middle small">
                <tr>
                    <td><b>INVOICE#</b><br>${invoice.invoice_number}</td>
                    <td><b>Costumer<br>(Cliente)</b><br>${invoice.recipient.name}</td>
                    <td rowspan="2" colspan="2" class="text-center align-middle"><b>${invoice.recipient.address}<br>${invoice.recipient.city} ${invoice.recipient.state} ${invoice.recipient.country}</b></td>
                </tr>
                <tr>
                    <td><b>Date<br>(Fecha)</b><br>${invoice.date}</td>
                    <td><b>Purchase Order<br>(Su pedido)</b><br>${invoice.purchase_order}</td>
                </tr>
                <tr>
                    <td><b>Terms of Payment:<br>(Condiciones pago)</b><br>${invoice.terms_of_payment}</td>
                    <td><b>Shipment<br>(Embarque)</b><br>${invoice.shipment}</td>
                    <td><b>Marks (Marcas):</b><br>${invoice.marks}</td>
                    <td><b>Pages (Hojas)</b><br>${invoice.pages}</td>
                </tr>
                <tr>
                    <td><b>Loading Airport<br>(Aeropuerto Embarque)</b><br>${invoice.loading_airport}</td>
                    <td><b>Airport of Discharge<br>(Aeropuerto Destino)</b><br>${invoice.airport_of_discharge}</td>
                    <td><b>Selling Conditions</b><br>${invoice.selling_conditions}</td>
                    <td><b>Notify</b><br>THE SAME</td>
                </tr>
            </table>
            <table class="table table-bordered align-middle small">
                <thead>
                    <tr>
                        <th>Cartons<br>(Boxes)</th>
                        <th>Goods (Mercadoria)</th>
                        <th>NCM</th>
                        <th>Qty. (Utd.)</th>
                        <th>Qty. (Unidade)</th>
                        <th>Unit Price US$<br>(Preço Unitário)</th>
                        <th>Amount US$<br>(Total US$)</th>
                    </tr>
                </thead>
                <tbody>
                    ${invoice.cartoons.map(item => `
                        <tr>
                            <td>Cardboard</td>
                            <td>${item.goods}</td>
                            <td>${item.ncm}</td>
                            <td>${item.qty_utd}</td>
                            <td>${item.qty_unidade}</td>
                            <td>U$${parseFloat(item.unit_price_usd).toFixed(2)}</td>
                            <td>${parseFloat(item.amount_usd).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                    <tr>
                        <td colspan="3"><b>Total</b></td>
                        <td><b>${invoice.total_qty}</b></td>
                        <td></td>
                        <td></td>
                        <td><b>${invoice.total_amount.toFixed(2)}</b></td>
                    </tr>
                    <tr>
                        <td colspan="6"><b>Freight</b></td>
                        <td><b>${invoice.freight.toFixed(2)}</b></td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-3"><b>Volumes:</b> ${invoice.volumes}</div>
                <div class="col-3"><b>Net Weight(Neto):</b> ${invoice.net_weight} LBS</div>
                <div class="col-3"><b>Container:</b> ${invoice.container}</div>
                <div class="col-3"><b>Gross Weight (Bruto):</b> ${invoice.gross_weight} LBS</div>
            </div>
        </div>`;
        $('#invoice-content').html(html);
        // Atualiza o link do botão de download do PDF
        $('#download-invoice-pdf-btn').attr('href', `/api/sections/invoice/${invoice.invoice_number.replace('#','')}/pdf`);
    }
</script>
@endsection 