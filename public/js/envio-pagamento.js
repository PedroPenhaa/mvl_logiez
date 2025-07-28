// Script para funcionalidades de pagamento do sistema Logiez
$(document).ready(function() {
    console.log('Script de pagamento carregado!');
    
    // Função para mostrar alertas
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'danger' ? 'times-circle' : 'info-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        // Verificar se já existe um container de alerta
        if ($('#alert-container').length === 0) {
            // Criar container de alerta antes do formulário
            $('.card-body').prepend('<div id="alert-container"></div>');
        }

        // Adicionar o alerta e rolar até ele
        $('#alert-container').html(alertHtml);
        $('html, body').animate({
            scrollTop: $('#alert-container').offset().top - 100
        }, 500);

        // Auto-fechamento após 5 segundos para alertas de sucesso
        if (type === 'success') {
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
    }
    
    // Sobrescrever o evento do botão Continuar da etapa 5
    $(document).off('click', '#btn-step-5-next').on('click', '#btn-step-5-next', function() {
        console.log('Botão Continuar clicado!');
        
        // Validar se um serviço foi selecionado
        if (!window.servicoSelecionado) {
            showAlert('Por favor, selecione um serviço de entrega.', 'warning');
            return;
        }
        
        console.log('Serviço selecionado:', window.servicoSelecionado);
        
        // Armazenar o serviço selecionado no formulário
        $('#servico_entrega').val(window.servicoSelecionado.tipo);
        
        // Adicionar informações do serviço selecionado ao resumo
        const servicoInfo = window.cotacoesFedEx.find(c => c.servicoTipo === window.servicoSelecionado.tipo);
        if (servicoInfo) {
            window.servicoInfo = servicoInfo;
            console.log('Informações do serviço:', servicoInfo);
            
            // Converter valorTotalBRL de string para número (substituir vírgula por ponto)
            const valorTotalBRL = parseFloat(servicoInfo.valorTotalBRL.replace(',', '.'));
            
            // Armazenar informações do serviço na sessão via AJAX
            $.ajax({
                url: '/armazenar-servico-sessao',
                type: 'POST',
                data: {
                    servico: servicoInfo.servico,
                    servicoTipo: servicoInfo.servicoTipo,
                    valorTotalBRL: valorTotalBRL,
                    tempoEntrega: servicoInfo.tempoEntrega,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Atualizar informações na etapa 6
                        $('#payment-service-name').text(servicoInfo.servico);
                        $('#payment-service-value').text('R$ ' + servicoInfo.valorTotalBRL);
                        
                        console.log('Indo para etapa 6...');
                        
                        // Navegar para a etapa 6 diretamente
                        $('[id^="step-"]').addClass('d-none');
                        $('#step-6').removeClass('d-none');
                        
                        // Atualizar progresso do wizard se a função existir
                        if (typeof atualizarProgressoWizard === 'function') {
                            atualizarProgressoWizard(6);
                        }
                        
                        // Atualizar variável global se existir
                        if (typeof window.etapaAtual !== 'undefined') {
                            window.etapaAtual = 6;
                        }
                        
                        // Scroll suave para o topo
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                        
                        showAlert('Serviço selecionado com sucesso! Redirecionando para pagamento...', 'success');
                    } else {
                        showAlert('Erro ao armazenar informações do serviço.', 'danger');
                    }
                },
                error: function() {
                    showAlert('Erro ao armazenar informações do serviço.', 'danger');
                }
            });
        } else {
            showAlert('Erro ao obter informações do serviço selecionado.', 'danger');
        }
    });
    
    // Sobrescrever o evento do botão Finalizar Pagamento
    $(document).off('click', '#finalizar-pagamento').on('click', '#finalizar-pagamento', function(e) {
        e.preventDefault();
        console.log('Botão Finalizar Pagamento clicado!');
        
        // Validar se um método foi selecionado
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        if (!paymentMethod) {
            showAlert('Por favor, selecione um método de pagamento.', 'warning');
            return;
        }
        
        // Validar campos do cartão se selecionado
        if (paymentMethod === 'credit_card') {
            const cardNumber = $('#card_number').val().trim();
            const cardExpiry = $('#card_expiry').val().trim();
            const cardCvv = $('#card_cvv').val().trim();
            const cardName = $('#card_name').val().trim();
            
            if (!cardNumber || !cardExpiry || !cardCvv || !cardName) {
                showAlert('Por favor, preencha todos os campos do cartão de crédito.', 'warning');
                return;
            }
            
            // Validar formato do número do cartão (pelo menos 13 dígitos)
            if (cardNumber.replace(/\s/g, '').length < 13) {
                showAlert('Número do cartão inválido.', 'warning');
                return;
            }
            
            // Validar formato da validade (MM/AA)
            if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
                showAlert('Formato de validade inválido. Use MM/AA.', 'warning');
                return;
            }
            
            // Validar CVV (3 ou 4 dígitos)
            if (!/^\d{3,4}$/.test(cardCvv)) {
                showAlert('CVV inválido.', 'warning');
                return;
            }
            
            // Mostrar modal de confirmação para cartão
            mostrarModalConfirmacao(paymentMethod);
        } else if (paymentMethod === 'pix') {
            // Processar PIX diretamente
            processarPagamento(paymentMethod);
        }
    });
    
    // Eventos para mostrar/esconder campos de pagamento
    $(document).on('change', 'input[name="payment_method"]', function() {
        const selectedMethod = $(this).val();
        
        // Esconder todos os formulários primeiro
        $('#credit-card-form, #pix-info').hide();
        
        // Mostrar o formulário correspondente
        if (selectedMethod === 'credit_card') {
            $('#credit-card-form').show();
        } else if (selectedMethod === 'pix') {
            $('#pix-info').show();
        }
    });
    
    // Máscaras para os campos do cartão
    $(document).on('input', '#card_number', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        $(this).val(value);
    });
    
    $(document).on('input', '#card_expiry', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        $(this).val(value);
    });
    
    $(document).on('input', '#card_cvv', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });
    
    // Função para mostrar modal de confirmação
    function mostrarModalConfirmacao(paymentMethod) {
        const cardNumber = $('#card_number').val();
        const lastFour = cardNumber.replace(/\s/g, '').slice(-4);
        
        // Criar modal de confirmação dinamicamente
        const modalHtml = `
            <div class="modal fade" id="modal-confirmacao-pagamento" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Confirmação de Pagamento</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-credit-card me-2"></i>Informações do Pagamento</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Método:</strong> Cartão de Crédito</li>
                                        <li><strong>Valor:</strong> ${$('#payment-service-value').text()}</li>
                                        <li><strong>Serviço:</strong> ${$('#payment-service-name').text()}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-credit-card me-2"></i>Dados do Cartão</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Nome:</strong> ${$('#card_name').val()}</li>
                                        <li><strong>Número:</strong> **** **** **** ${lastFour}</li>
                                        <li><strong>Validade:</strong> ${$('#card_expiry').val()}</li>
                                        <li><strong>Parcelas:</strong> ${$('#installments option:selected').text()}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="btn-confirmar-pagamento">
                                <i class="fas fa-check-circle me-2"></i>Realizar Pagamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior se existir
        $('#modal-confirmacao-pagamento').remove();
        
        // Adicionar novo modal
        $('body').append(modalHtml);
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modal-confirmacao-pagamento'));
        modal.show();
        
        // Evento para confirmar pagamento
        $('#btn-confirmar-pagamento').off('click').on('click', function() {
            modal.hide();
            processarPagamento(paymentMethod);
        });
    }
    
    // Função para processar pagamento
    function processarPagamento(paymentMethod) {
        console.log('Processando pagamento:', paymentMethod);
        
        // Mostrar loading
        $('#finalizar-pagamento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processando...');
        
        // Preparar dados para envio
        const formData = new FormData($('#envio-form')[0]);
        formData.append('payment_method', paymentMethod);
        
        // Adicionar dados do cartão se for cartão de crédito
        if (paymentMethod === 'credit_card') {
            formData.append('card_number', $('#card_number').val());
            formData.append('card_expiry', $('#card_expiry').val());
            formData.append('card_cvv', $('#card_cvv').val());
            formData.append('card_name', $('#card_name').val());
            formData.append('installments', $('#installments').val());
        }
        
        // Enviar para processamento
        $.ajax({
            url: '/processar-pagamento',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Resposta do pagamento:', response);
                
                if (response.success) {
                    // Redirecionar para página de sucesso ou mostrar QR Code do PIX
                    if (paymentMethod === 'pix') {
                        // Mostrar QR Code do PIX
                        $('#pagamento-section').html(`
                            <div class="card-body text-center">
                                <h5 class="mb-4">Pagamento PIX Gerado</h5>
                                <div class="qr-code-container mb-4">
                                    <img src="${response.qr_code_url}" alt="QR Code PIX" class="img-fluid">
                                </div>
                                <p class="mb-2">Valor: <strong>R$ ${response.valor}</strong></p>
                                <p class="text-muted mb-4">Escaneie o QR Code acima com seu aplicativo de pagamento PIX</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                                        <i class="fas fa-redo me-2"></i>Gerar Novo PIX
                                    </button>
                                </div>
                            </div>
                        `);
                    } else {
                        // Redirecionar para página de sucesso
                        window.location.href = '/pagamento-sucesso?id=' + response.payment_id;
                    }
                } else {
                    showAlert(response.message || 'Erro ao processar pagamento. Tente novamente.', 'danger');
                    $('#finalizar-pagamento').prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Finalizar Pagamento');
                }
            },
            error: function(xhr) {
                console.error('Erro no pagamento:', xhr);
                showAlert('Erro ao processar pagamento. Tente novamente.', 'danger');
                $('#finalizar-pagamento').prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Finalizar Pagamento');
            }
        });
    }
}); 