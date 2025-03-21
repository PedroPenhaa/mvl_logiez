<div class="card">
    <div class="card-header">
        <i class="fas fa-shipping-fast me-2"></i> Dados do Envio
    </div>
    <div class="card-body">
        <form id="envio-form" action="{{ route('api.envio.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Dados do Remetente</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nome_remetente" class="form-label">Nome do Remetente</label>
                                <input type="text" class="form-control" id="nome_remetente" name="nome_remetente" required>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="email_remetente" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_remetente" name="email_remetente" required>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="telefone_remetente" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone_remetente" name="telefone_remetente" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="tipo_pessoa" class="form-label">Tipo de Pessoa</label>
                                <select class="form-select" id="tipo_pessoa" name="tipo_pessoa" required>
                                    <option value="fisica">Pessoa Física</option>
                                    <option value="juridica">Pessoa Jurídica</option>
                                </select>
                            </div>
                            <div class="mb-3" id="cpf_container">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf">
                            </div>
                            <div class="mb-3 d-none" id="cnpj_container">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control" id="cnpj" name="cnpj">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card h-100 border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Dados do Destinatário</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="nome_destinatario" class="form-label">Nome do Destinatário</label>
                                <input type="text" class="form-control" id="nome_destinatario" name="nome_destinatario" required>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="email_destinatario" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_destinatario" name="email_destinatario" required>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="telefone_destinatario" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone_destinatario" name="telefone_destinatario" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="endereco_destinatario" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="endereco_destinatario" name="endereco_destinatario" required>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="cidade_destinatario" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="cidade_destinatario" name="cidade_destinatario" required>
                                </div>
                                <div class="col-sm-6 mb-3">
                                    <label for="pais_destinatario" class="form-label">País</label>
                                    <input type="text" class="form-control" id="pais_destinatario" name="pais_destinatario" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="codigo_postal_destinatario" class="form-label">Código Postal / CEP</label>
                                <input type="text" class="form-control" id="codigo_postal_destinatario" name="codigo_postal_destinatario" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Dados da Mercadoria</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="tipo_envio" class="form-label">Tipo de Envio</label>
                                    <select class="form-select" id="tipo_envio" name="tipo_envio" required>
                                        <option value="documento">Documento</option>
                                        <option value="encomenda">Encomenda</option>
                                        <option value="amostra">Amostra</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <label for="valor_mercadoria" class="form-label">Valor da Mercadoria (R$)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="valor_mercadoria" name="valor_mercadoria" required>
                                </div>
                                <div class="col-lg-4 col-md-12 col-sm-12 mb-3">
                                    <label for="descricao_mercadoria" class="form-label">Descrição da Mercadoria</label>
                                    <input type="text" class="form-control" id="descricao_mercadoria" name="descricao_mercadoria" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                    <label for="altura" class="form-label">Altura (cm)</label>
                                    <input type="number" step="0.01" min="0.1" class="form-control" id="altura" name="altura" required>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                    <label for="largura" class="form-label">Largura (cm)</label>
                                    <input type="number" step="0.01" min="0.1" class="form-control" id="largura" name="largura" required>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                    <input type="number" step="0.01" min="0.1" class="form-control" id="comprimento" name="comprimento" required>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                    <label for="peso" class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.01" min="0.1" class="form-control" id="peso" name="peso" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="contem_liquido" name="contem_liquido">
                                <label class="form-check-label" for="contem_liquido">Este envio contém itens líquidos</label>
                            </div>
                            
                            <div class="alert alert-info d-none" id="info_liquido">
                                <i class="fas fa-info-circle me-2"></i> Envios contendo itens líquidos requerem declaração especial e podem estar sujeitos a restrições de acordo com a legislação do país de destino.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check me-2"></i> Prosseguir para Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle CPF/CNPJ com base no tipo de pessoa
        $('#tipo_pessoa').on('change', function() {
            if ($(this).val() === 'fisica') {
                $('#cpf_container').removeClass('d-none');
                $('#cnpj_container').addClass('d-none');
                $('#cnpj').val('');
            } else {
                $('#cpf_container').addClass('d-none');
                $('#cnpj_container').removeClass('d-none');
                $('#cpf').val('');
            }
        });
        
        // Mostrar alerta para itens líquidos
        $('#contem_liquido').on('change', function() {
            $('#info_liquido').toggleClass('d-none', !this.checked);
        });
        
        // Processar o envio do formulário via AJAX
        $('#envio-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    showAlert('Dados do envio processados com sucesso!', 'success');
                    loadSection('pagamento');
                },
                error: function() {
                    showAlert('Erro ao processar os dados do envio. Verifique os campos e tente novamente.', 'danger');
                }
            });
        });
    });
</script> 