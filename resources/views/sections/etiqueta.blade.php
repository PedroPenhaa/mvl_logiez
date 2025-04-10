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
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DHL123456789</td>
                            <td>15/10/2023</td>
                            <td>John Smith</td>
                            <td>Miami, EUA</td>
                            <td><span class="badge bg-success">Etiqueta Pronta</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1 visualizar-etiqueta" data-codigo="DHL123456789">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success me-1 imprimir-etiqueta" data-codigo="DHL123456789">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-info me-1 email-etiqueta" data-codigo="DHL123456789">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>DHL987654321</td>
                            <td>05/10/2023</td>
                            <td>Maria Johnson</td>
                            <td>Londres, Reino Unido</td>
                            <td><span class="badge bg-success">Etiqueta Pronta</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1 visualizar-etiqueta" data-codigo="DHL987654321">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success me-1 imprimir-etiqueta" data-codigo="DHL987654321">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-info me-1 email-etiqueta" data-codigo="DHL987654321">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>DHL456789123</td>
                            <td>25/09/2023</td>
                            <td>Akira Tanaka</td>
                            <td>Tóquio, Japão</td>
                            <td><span class="badge bg-success">Etiqueta Pronta</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary me-1 visualizar-etiqueta" data-codigo="DHL456789123">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success me-1 imprimir-etiqueta" data-codigo="DHL456789123">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-info me-1 email-etiqueta" data-codigo="DHL456789123">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </td>
                        </tr>
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

<script>
    $(document).ready(function() {
        // Verificar se temos um envio na sessão (redirecionado do processamento de envio)
        const mostrarEtiquetaEnvio = function() {
            // Fazer requisição AJAX para verificar se há dados de envio na sessão
            $.get('/api/sections/etiqueta?check_session=1', function(response) {
                if (response.hasEnvio) {
                    // Carregar dados do último envio
                    exibirEtiqueta(response.envio);
                }
            });
        };
        
        // Chamar ao carregar a página
        mostrarEtiquetaEnvio();
        
        // Buscar etiqueta
        $('#buscar-etiqueta-btn').on('click', function() {
            const codigo = $('#codigo-envio').val().trim();
            
            if (!codigo) {
                showAlert('warning', 'Por favor, digite um código de envio válido.');
                return;
            }
            
            // Fazer requisição para buscar a etiqueta pelo código
            $.ajax({
                url: '{{ route("api.rastreamento.buscar") }}',
                method: 'POST',
                data: { codigo_rastreamento: codigo },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    
                    if (response.success) {
                        showAlert('success', 'Etiqueta encontrada!');
                        // Destacar a linha encontrada ou exibir modal
                        exibirEtiqueta({
                            trackingNumber: response.trackingNumber,
                            labelUrl: response.labelUrl || null,
                            servicoContratado: response.servicoContratado || 'FEDEX_INTERNATIONAL_PRIORITY',
                            simulado: response.simulado || true,
                            dataCriacao: response.dataCriacao || new Date().toISOString(),
                            dados: response.dados || {}
                        });
                    } else {
                        showAlert('warning', 'Etiqueta não encontrada: ' + (response.message || 'Verifique o código informado.'));
                        $('#sem-etiquetas').show();
                        $('#etiquetas-disponiveis').hide();
                    }
                },
                error: function() {
                    hideLoader();
                    showAlert('danger', 'Erro ao buscar a etiqueta. Tente novamente.');
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
        $('.visualizar-etiqueta').on('click', function() {
            const codigo = $(this).data('codigo');
            
            showLoader();
            
            // Em produção aqui seria uma chamada AJAX para obter os detalhes da etiqueta
            setTimeout(function() {
                hideLoader();
                
                // Simular dados para demonstração
                exibirEtiqueta({
                    trackingNumber: codigo,
                    labelUrl: 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + codigo,
                    servicoContratado: 'FEDEX_INTERNATIONAL_PRIORITY',
                    simulado: true,
                    dataCriacao: new Date().toISOString(),
                    dados: {
                        pesoTotal: '1.2',
                        remetente: {
                            nome: 'Pedro Oliveira',
                            endereco: 'Rua das Flores, 123',
                            complemento: 'Apto 42',
                            cidade: 'São Paulo',
                            estado: 'SP',
                            cep: '01234-567',
                            pais: 'Brasil',
                            telefone: '+55 11 99999-8888'
                        },
                        destinatario: {
                            nome: 'John Smith',
                            endereco: '233 Broadway Avenue',
                            complemento: '',
                            cidade: 'Miami',
                            estado: 'FL',
                            cep: '33101',
                            pais: 'Estados Unidos',
                            telefone: '+1 305-555-7890'
                        }
                    }
                });
            }, 500);
        });
        
        // Imprimir etiqueta
        $('#imprimir-modal-btn').on('click', function() {
            // Capturar o conteúdo da etiqueta
            const conteudoEtiqueta = document.getElementById('etiqueta-preview').innerHTML;
            
            // Criar uma nova janela para impressão
            const janelaImpressao = window.open('', '_blank');
            janelaImpressao.document.write(`
                <html>
                    <head>
                        <title>Etiqueta FedEx - ${$('#etiqueta-codigo').text()}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            body { padding: 20px; }
                            @media print {
                                .no-print { display: none; }
                                a[href]:after { content: none !important; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="no-print mb-4">
                                <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
                                <button class="btn btn-secondary ms-2" onclick="window.close()">Fechar</button>
                            </div>
                            ${conteudoEtiqueta}
                        </div>
                    </body>
                </html>
            `);
            janelaImpressao.document.close();
        });
        
        // Mostrar todos os envios
        $('#mostrar-todos-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
        });
        
        // Voltar para todas as etiquetas
        $('#voltar-etiquetas-btn').on('click', function() {
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
        });
    });
</script> 