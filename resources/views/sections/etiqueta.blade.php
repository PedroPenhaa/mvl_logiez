<div class="card">
    <div class="card-header">
        <i class="fas fa-tag me-2"></i> Etiqueta de Envio
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i> Gere e imprima etiquetas para seus envios realizados ou busque por código de envio.
        </div>
        
        <!-- Formulário de busca de etiqueta -->
        <div id="busca-etiqueta" class="mb-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Buscar Etiqueta</h5>
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
            <h5 class="mb-3">Etiquetas Disponíveis</h5>
            
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
        
        <!-- Sem Etiquetas Encontradas (inicialmente oculto) -->
        <div id="sem-etiquetas" class="text-center py-5" style="display: none;">
            <i class="fas fa-tag fa-4x text-muted mb-3"></i>
            <h5>Nenhuma etiqueta encontrada</h5>
            <p>Não encontramos etiquetas com o código informado.</p>
            <button class="btn btn-outline-primary mt-2" id="voltar-etiquetas-btn">
                <i class="fas fa-arrow-left me-2"></i> Voltar para todas etiquetas
            </button>
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

<script>
    // Loader simples
    function showLoader() {
        if ($('#loader-modal').length === 0) {
            $('body').append('<div id="loader-modal" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;"><div class="spinner-border text-primary" style="width:4rem;height:4rem;" role="status"></div></div>');
        }
    }
    function hideLoader() {
        $('#loader-modal').remove();
    }

    // Alerta simples
    function showAlert(type, message) {
        let alertClass = 'alert-info';
        if (type === 'success') alertClass = 'alert-success';
        if (type === 'warning') alertClass = 'alert-warning';
        if (type === 'danger') alertClass = 'alert-danger';
        $('.alert').remove(); // Remove alertas antigos
        $('.card-body').first().prepend('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
    }

    // Função para atualizar a tabela com os dados da etiqueta
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

    $(document).ready(function() {
        // Buscar etiquetas do usuário logado ao carregar a página
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

        // Chamar ao carregar a página
        carregarEtiquetasUsuario();
        
        // Buscar etiqueta
        $('#buscar-etiqueta-btn').on('click', function() {
            const codigo = $('#codigo-envio').val().trim();
            
            if (!codigo) {
                showAlert('warning', 'Por favor, digite um código de envio válido.');
                return;
            }
            
            showLoader();
            
            // Fazer requisição para buscar a etiqueta pelo código na FedEx
            $.ajax({
                url: '/api/fedex/etiqueta',
                method: 'POST',
                data: { codigo: codigo },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        showAlert('success', 'Etiqueta encontrada!');
                        atualizarTabelaEtiqueta(response);
                        $('#sem-etiquetas').hide();
                        $('#etiquetas-disponiveis').show();
                    } else {
                        showAlert('warning', 'Etiqueta não encontrada: ' + (response.message || 'Verifique o código informado.'));
                        $('#sem-etiquetas').show();
                        $('#etiquetas-disponiveis').hide();
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    let errorMessage = 'Erro ao buscar a etiqueta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ' ' + xhr.responseJSON.message;
                    }
                    showAlert('danger', errorMessage);
                }
            });
        });
        
        // Função para exibir os dados da etiqueta no modal
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
        
        // Visualizar etiqueta da tabela
        $(document).on('click', '.visualizar-etiqueta', function() {
            const codigo = $(this).data('codigo');
            showLoader();

            $.ajax({
                url: '/api/fedex/etiqueta',
                method: 'POST',
                data: { codigo: codigo },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoader();
                    if (response.success && response.labelUrl) {
                        // Preencher os dados no modal
                        $('#etiqueta-codigo').text(response.trackingNumber);
                        $('#etiqueta-servico').text(response.serviceName);
                        $('#etiqueta-data').text(new Date(response.shipDate).toLocaleDateString('pt-BR'));
                        $('#etiqueta-destinatario').text(response.recipient.name);
                        $('#etiqueta-cidade-destinatario').text(response.recipient.city);
                        $('#etiqueta-pais-destinatario').text(response.recipient.country);
                        $('#etiqueta-tracking').text(response.trackingNumber);
                        
                        // Configurar o iframe para exibir o PDF da etiqueta
                        $('#etiqueta-iframe-container').html(`
                            <iframe src="${response.labelUrl}" 
                                    style="width: 100%; height: 500px; border: none;"
                                    title="Etiqueta FedEx">
                            </iframe>
                        `);
                        
                        // Configurar o botão de download
                        $('#download-etiqueta-btn').attr('href', response.labelUrl);
                        
                        // Configurar o link de rastreamento
                        $('#link-rastreamento').attr('href', `https://www.fedex.com/tracking?tracknumbers=${response.trackingNumber}`);
                        
                        // Mostrar o modal
                        $('#etiqueta-modal').modal('show');
                    } else {
                        showAlert('warning', 'Etiqueta não encontrada ou erro na FedEx.');
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    let errorMessage = 'Erro ao buscar etiqueta FedEx.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += ' ' + xhr.responseJSON.message;
                    }
                    showAlert('danger', errorMessage);
                }
            });
        });
        
        // Imprimir etiqueta
        $('#imprimir-modal-btn').on('click', function() {
            const iframe = document.querySelector('#etiqueta-iframe-container iframe');
            if (iframe) {
                iframe.contentWindow.print();
            }
        });
        
        // Mostrar todas as etiquetas
        $('#mostrar-todos-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
            $('#codigo-envio').val('');
        });
        
        // Voltar para todas as etiquetas
        $('#voltar-etiquetas-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
            $('#codigo-envio').val('');
        });

        // Evento para o botão Invoice
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