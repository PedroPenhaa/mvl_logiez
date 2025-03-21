<div class="card">
    <div class="card-header">
        <i class="fas fa-credit-card me-2"></i> Pagamento
    </div>
    <div class="card-body">
        <div id="pagamento-resumo" class="mb-4">
            <h5 class="mb-3">Resumo do Envio</h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td><strong>Origem:</strong></td>
                                <td>São Paulo, Brasil</td>
                            </tr>
                            <tr>
                                <td><strong>Destino:</strong></td>
                                <td>Miami, Estados Unidos</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo de Envio:</strong></td>
                                <td>Documentos</td>
                            </tr>
                            <tr>
                                <td><strong>Dimensões:</strong></td>
                                <td>20cm x 30cm x 5cm</td>
                            </tr>
                            <tr>
                                <td><strong>Peso:</strong></td>
                                <td>1.2 kg</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Detalhes do Valor</h6>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>Valor do Frete:</td>
                                        <td class="text-end">R$ 250,00</td>
                                    </tr>
                                    <tr>
                                        <td>Taxa de Serviço:</td>
                                        <td class="text-end">R$ 35,00</td>
                                    </tr>
                                    <tr>
                                        <td>Seguro:</td>
                                        <td class="text-end">R$ 15,00</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end"><strong>R$ 300,00</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="metodos-pagamento">
            <h5 class="mb-3">Forma de Pagamento</h5>
            
            <ul class="nav nav-tabs mb-4" id="pagamentoTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cartao-tab" data-bs-toggle="tab" data-bs-target="#cartao" type="button" role="tab" aria-controls="cartao" aria-selected="true">Cartão de Crédito</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="boleto-tab" data-bs-toggle="tab" data-bs-target="#boleto" type="button" role="tab" aria-controls="boleto" aria-selected="false">Boleto Bancário</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pix-tab" data-bs-toggle="tab" data-bs-target="#pix" type="button" role="tab" aria-controls="pix" aria-selected="false">PIX</button>
                </li>
            </ul>
            
            <div class="tab-content" id="pagamentoTabContent">
                <!-- Cartão de Crédito -->
                <div class="tab-pane fade show active" id="cartao" role="tabpanel" aria-labelledby="cartao-tab">
                    <form id="cartao-form">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="numero-cartao" class="form-label">Número do Cartão</label>
                                <input type="text" class="form-control" id="numero-cartao" placeholder="0000 0000 0000 0000" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nome-cartao" class="form-label">Nome no Cartão</label>
                                <input type="text" class="form-control" id="nome-cartao" placeholder="Como aparece no cartão" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="validade" class="form-label">Validade</label>
                                <input type="text" class="form-control" id="validade" placeholder="MM/AA" required>
                            </div>
                            <div class="col-md-2">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" placeholder="123" required>
                            </div>
                            <div class="col-md-6">
                                <label for="parcelas" class="form-label">Parcelas</label>
                                <select class="form-select" id="parcelas" required>
                                    <option value="1">1x de R$ 300,00 (sem juros)</option>
                                    <option value="2">2x de R$ 150,00 (sem juros)</option>
                                    <option value="3">3x de R$ 100,00 (sem juros)</option>
                                    <option value="4">4x de R$ 78,75 (5% de juros)</option>
                                    <option value="5">5x de R$ 63,90 (6.5% de juros)</option>
                                    <option value="6">6x de R$ 54,00 (8% de juros)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="salvar-cartao">
                                <label class="form-check-label" for="salvar-cartao">
                                    Salvar cartão para futuras compras
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Boleto Bancário -->
                <div class="tab-pane fade" id="boleto" role="tabpanel" aria-labelledby="boleto-tab">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Um boleto bancário será gerado após a finalização do pedido. O prazo de vencimento é de 3 dias úteis.
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Seu envio só será processado após a confirmação do pagamento do boleto.
                    </div>
                    <div class="text-center my-4">
                        <i class="fas fa-barcode fa-5x text-muted"></i>
                    </div>
                </div>
                
                <!-- PIX -->
                <div class="tab-pane fade" id="pix" role="tabpanel" aria-labelledby="pix-tab">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Um QR Code PIX será gerado para pagamento imediato após finalizar o pedido.
                    </div>
                    <div class="row justify-content-center my-4">
                        <div class="col-md-6 text-center">
                            <div class="border p-3 mb-3">
                                <i class="fas fa-qrcode fa-5x text-muted my-3"></i>
                                <p class="text-muted">O QR Code será gerado após a confirmação do pedido</p>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-clock me-2"></i> Pagamento instantâneo
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="concordar-termos" required>
            <label class="form-check-label" for="concordar-termos">
                Concordo com os <a href="#" data-bs-toggle="modal" data-bs-target="#termosModal">termos e condições</a> de uso da Logiez
            </label>
        </div>
        
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary section-link" data-section="envio">
                <i class="fas fa-arrow-left me-2"></i> Voltar para Envio
            </button>
            <button type="button" id="finalizar-pagamento" class="btn btn-primary">
                Finalizar Pagamento <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Modal de Termos -->
<div class="modal fade" id="termosModal" tabindex="-1" aria-labelledby="termosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termosModalLabel">Termos e Condições</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Aceitação dos Termos</h6>
                <p>Ao utilizar os serviços de envio internacional da Logiez, você concorda com os termos e condições aqui estabelecidos.</p>
                
                <h6>2. Serviços Oferecidos</h6>
                <p>A Logiez oferece serviços de envio internacional em parceria com a DHL, fornecendo cotações, rastreamento, e emissão de documentos.</p>
                
                <h6>3. Pagamentos</h6>
                <p>Os pagamentos são processados por plataformas seguras. A Logiez não armazena informações completas de cartão de crédito.</p>
                
                <h6>4. Envios</h6>
                <p>O cliente é responsável por fornecer informações precisas sobre o conteúdo, dimensões e peso do envio. Declarações falsas podem resultar em apreensão aduaneira e multas.</p>
                
                <h6>5. Restrições</h6>
                <p>Alguns itens são proibidos para envio internacional. Consulte a lista completa em nossa página de FAQ.</p>
                
                <h6>6. Responsabilidade</h6>
                <p>A Logiez atua como intermediária entre o cliente e a transportadora. Atrasos ou danos causados durante o transporte são de responsabilidade da transportadora, conforme seus próprios termos.</p>
                
                <h6>7. Privacidade</h6>
                <p>As informações fornecidas são utilizadas apenas para processamento do envio e estão sujeitas à nossa Política de Privacidade.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="aceitar-termos">Aceitar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Evento para botões de navegação entre seções
        $('.section-link').on('click', function() {
            const section = $(this).data('section');
            $('.menu-item[data-section="' + section + '"]').click();
        });
        
        // Formatar campos do cartão
        if (typeof IMask !== 'undefined') {
            // Formatar número do cartão
            IMask(document.getElementById('numero-cartao'), {
                mask: '0000 0000 0000 0000'
            });
            
            // Formatar validade
            IMask(document.getElementById('validade'), {
                mask: 'MM/YY',
                blocks: {
                    MM: {
                        mask: IMask.MaskedRange,
                        from: 1,
                        to: 12
                    },
                    YY: {
                        mask: IMask.MaskedRange,
                        from: 23,
                        to: 33
                    }
                }
            });
            
            // Formatar CVV
            IMask(document.getElementById('cvv'), {
                mask: '000'
            });
        }
        
        // Aceitar termos via modal
        $('#aceitar-termos').on('click', function() {
            $('#concordar-termos').prop('checked', true);
        });
        
        // Finalizar pagamento
        $('#finalizar-pagamento').on('click', function() {
            if (!$('#concordar-termos').is(':checked')) {
                showAlert('warning', 'Você precisa concordar com os termos e condições para continuar.');
                return;
            }
            
            // Verificar qual método de pagamento está ativo
            let metodo = "";
            if ($('#cartao-tab').hasClass('active')) {
                metodo = "cartão de crédito";
                // Validar formulário de cartão
                if (!validarFormularioCartao()) {
                    return;
                }
            } else if ($('#boleto-tab').hasClass('active')) {
                metodo = "boleto bancário";
            } else if ($('#pix-tab').hasClass('active')) {
                metodo = "PIX";
            }
            
            // Mostrar loader
            showLoader();
            
            // Simular processamento do pagamento
            setTimeout(function() {
                hideLoader();
                
                // Exibir modal de confirmação
                const modalHtml = `
                <div class="modal fade" id="confirmacaoModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">Pagamento Confirmado!</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                                <h4>Tudo certo!</h4>
                                <p class="mb-1">Seu pagamento via ${metodo} foi processado com sucesso.</p>
                                <p>O código de rastreamento será enviado para seu email.</p>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i> Você pode acompanhar seu envio na seção "Rastreamento".
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-success section-link" data-section="rastreamento" data-bs-dismiss="modal">
                                    Ir para Rastreamento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
                
                // Adicionar modal ao DOM
                $('body').append(modalHtml);
                
                // Exibir modal
                const confirmacaoModal = new bootstrap.Modal(document.getElementById('confirmacaoModal'));
                confirmacaoModal.show();
                
                // Adicionar evento após modal ser fechado
                $('#confirmacaoModal').on('hidden.bs.modal', function() {
                    $(this).remove();
                });
                
                // Reconfigurar botões de seção no modal
                $('.section-link').on('click', function() {
                    const section = $(this).data('section');
                    $('.menu-item[data-section="' + section + '"]').click();
                });
            }, 2000);
        });
        
        // Validar formulário de cartão
        function validarFormularioCartao() {
            const numeroCartao = $('#numero-cartao').val();
            const nomeCartao = $('#nome-cartao').val();
            const validade = $('#validade').val();
            const cvv = $('#cvv').val();
            
            if (numeroCartao.replace(/\D/g, '').length < 16) {
                showAlert('danger', 'Número de cartão inválido');
                return false;
            }
            
            if (nomeCartao.trim().length < 3) {
                showAlert('danger', 'Nome no cartão inválido');
                return false;
            }
            
            if (validade.length < 5) {
                showAlert('danger', 'Data de validade inválida');
                return false;
            }
            
            if (cvv.length < 3) {
                showAlert('danger', 'Código de segurança inválido');
                return false;
            }
            
            return true;
        }
        
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