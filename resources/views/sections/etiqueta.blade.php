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
                <h5 class="modal-title" id="etiquetaModalLabel">Etiqueta de Envio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="etiqueta-preview" class="border p-3">
                    <!-- Conteúdo da Etiqueta -->
                    <div class="row">
                        <div class="col-6">
                            <img src="https://via.placeholder.com/200x60?text=DHL+Logo" alt="DHL Logo" class="img-fluid mb-3">
                        </div>
                        <div class="col-6 text-end">
                            <h6 class="fw-bold mb-0">AIRWAY BILL</h6>
                            <div id="etiqueta-codigo" class="fs-4 fw-bold">DHL123456789</div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">REMETENTE:</div>
                                <div class="fw-bold">Pedro Oliveira</div>
                                <div>Rua das Flores, 123</div>
                                <div>São Paulo, SP - 01234-567</div>
                                <div>Brasil</div>
                                <div>Tel: (11) 99999-8888</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">DESTINATÁRIO:</div>
                                <div id="etiqueta-destinatario" class="fw-bold">John Smith</div>
                                <div id="etiqueta-endereco">233 Broadway Avenue</div>
                                <div id="etiqueta-cidade-pais">Miami, FL - 33101</div>
                                <div id="etiqueta-pais">Estados Unidos</div>
                                <div>Tel: +1 305-555-7890</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">CONTEÚDO:</div>
                                <div class="fw-bold">Documentos</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">PESO:</div>
                                <div class="fw-bold">1.2 kg</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border p-2">
                                <div class="small text-muted mb-1">DATA:</div>
                                <div id="etiqueta-data" class="fw-bold">15/10/2023</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="mb-2">
                            <img src="https://via.placeholder.com/350x100?text=Barcode" alt="Código de Barras" class="img-fluid">
                        </div>
                        <div class="small text-muted">Este documento deve ser anexado à embalagem do envio.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-info" id="email-modal-btn">
                    <i class="fas fa-envelope me-2"></i> Enviar por Email
                </button>
                <button type="button" class="btn btn-success" id="imprimir-modal-btn">
                    <i class="fas fa-print me-2"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Buscar etiqueta
        $('#buscar-etiqueta-btn').on('click', function() {
            const codigo = $('#codigo-envio').val().trim();
            
            if (!codigo) {
                showAlert('warning', 'Por favor, digite um código de envio válido.');
                return;
            }
            
            // Simular busca (em produção seria uma chamada AJAX)
            showLoader();
            
            setTimeout(function() {
                hideLoader();
                
                // Verificar se o código existe na tabela
                const found = $('tr').filter(function() {
                    return $(this).find('td:first').text() === codigo;
                }).length > 0;
                
                if (found) {
                    showAlert('success', 'Etiqueta encontrada!');
                    // Destacar a linha encontrada
                    $('tr').removeClass('table-primary');
                    $('tr').filter(function() {
                        return $(this).find('td:first').text() === codigo;
                    }).addClass('table-primary');
                } else {
                    $('#etiquetas-disponiveis').hide();
                    $('#sem-etiquetas').show();
                }
            }, 1000);
        });
        
        // Mostrar todos
        $('#mostrar-todos-btn, #voltar-etiquetas-btn').on('click', function() {
            $('#codigo-envio').val('');
            $('tr').removeClass('table-primary');
            $('#sem-etiquetas').hide();
            $('#etiquetas-disponiveis').show();
        });
        
        // Visualizar etiqueta
        $('.visualizar-etiqueta').on('click', function() {
            const codigo = $(this).data('codigo');
            const row = $(this).closest('tr');
            const destinatario = row.find('td:eq(2)').text();
            const destino = row.find('td:eq(3)').text();
            const data = row.find('td:eq(1)').text();
            
            // Preencher dados na etiqueta
            $('#etiqueta-codigo').text(codigo);
            $('#etiqueta-destinatario').text(destinatario);
            $('#etiqueta-cidade-pais').text(destino);
            $('#etiqueta-data').text(data);
            
            // Exibir modal
            const etiquetaModal = new bootstrap.Modal(document.getElementById('etiqueta-modal'));
            etiquetaModal.show();
        });
        
        // Imprimir etiqueta (direto da lista)
        $('.imprimir-etiqueta').on('click', function() {
            const codigo = $(this).data('codigo');
            
            showLoader();
            
            setTimeout(function() {
                hideLoader();
                showAlert('success', `Etiqueta ${codigo} enviada para impressão!`);
            }, 1500);
        });
        
        // Enviar etiqueta por email
        $('.email-etiqueta').on('click', function() {
            const codigo = $(this).data('codigo');
            
            showLoader();
            
            setTimeout(function() {
                hideLoader();
                showAlert('success', `Etiqueta ${codigo} enviada para seu email cadastrado!`);
            }, 1500);
        });
        
        // Botões do modal
        $('#imprimir-modal-btn').on('click', function() {
            const codigo = $('#etiqueta-codigo').text();
            
            // Fechar modal
            bootstrap.Modal.getInstance(document.getElementById('etiqueta-modal')).hide();
            
            // Simular impressão
            showLoader();
            
            setTimeout(function() {
                hideLoader();
                showAlert('success', `Etiqueta ${codigo} enviada para impressão!`);
            }, 1500);
        });
        
        $('#email-modal-btn').on('click', function() {
            const codigo = $('#etiqueta-codigo').text();
            
            // Fechar modal
            bootstrap.Modal.getInstance(document.getElementById('etiqueta-modal')).hide();
            
            // Simular envio de email
            showLoader();
            
            setTimeout(function() {
                hideLoader();
                showAlert('success', `Etiqueta ${codigo} enviada para seu email cadastrado!`);
            }, 1500);
        });
        
        // Funções auxiliares
        function showLoader() {
            // Verificar se o loader existe, senão criar
            if ($('#global-loader').length === 0) {
                $('body').append('<div id="global-loader" class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center" style="top: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 9999;"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
            } else {
                $('#global-loader').show();
            }
        }
        
        function hideLoader() {
            $('#global-loader').hide();
        }
        
        function showAlert(type, message) {
            // Criar o elemento de alerta
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Adicionar ao topo do conteúdo principal
            $('#main-content').prepend(alertHtml);
            
            // Definir timeout para remover o alerta
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
    });
</script> 