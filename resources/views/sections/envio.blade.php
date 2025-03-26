<div class="card">
    <div class="card-header">
        <i class="fas fa-shipping-fast me-2"></i> Dados do Envio
    </div>
    <div class="card-body">
        <form id="envio-form" action="{{ route('api.envio.processar') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produtos para Envio</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-5 col-md-5">
                                    <select class="form-select" id="produto-select">
                                        <option value="" selected disabled>Selecione um produto</option>
                                        <option value="1" data-nome="Smartphone" data-peso="0.3" data-valor="1200.00">Smartphone</option>
                                        <option value="2" data-nome="Notebook" data-peso="2.5" data-valor="3500.00">Notebook</option>
                                        <option value="3" data-nome="Headphone" data-peso="0.5" data-valor="350.00">Headphone</option>
                                        <option value="4" data-nome="Câmera Digital" data-peso="0.7" data-valor="1800.00">Câmera Digital</option>
                                        <option value="5" data-nome="Relógio Inteligente" data-peso="0.2" data-valor="650.00">Relógio Inteligente</option>
                                        <option value="6" data-nome="Tablet" data-peso="0.8" data-valor="1500.00">Tablet</option>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Quantidade</span>
                                        <input type="number" class="form-control" id="produto-quantidade" min="1" value="1">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <button type="button" class="btn btn-primary w-100" id="adicionar-produto">
                                        <i class="fas fa-plus me-2"></i>Adicionar
                                    </button>
                                </div>
                            </div>
                            
                            <div id="produtos-container" class="mt-4">
                                <div class="alert alert-info" id="sem-produtos-alert">
                                    <i class="fas fa-info-circle me-2"></i> Adicione produtos à sua lista de envio.
                                </div>
                                <div id="produtos-cards" class="row g-3">
                                    <!-- Os cards de produtos serão adicionados aqui dinamicamente -->
                                </div>
                            </div>
                            
                            <div class="mt-3 p-3 bg-light rounded d-none" id="resumo-produtos">
                                <div class="d-flex justify-content-between">
                                    <h5>Resumo do Envio</h5>
                                    <h5>Total: R$ <span id="valor-total">0.00</span></h5>
                                </div>
                                <p class="mb-0">Peso total estimado: <span id="peso-total">0.00</span> kg</p>
                                <input type="hidden" name="produtos_json" id="produtos-json">
                                <input type="hidden" name="valor_total" id="valor-total-input">
                                <input type="hidden" name="peso_total" id="peso-total-input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Origem</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="origem_nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="origem_nome" name="origem_nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="origem_endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="origem_endereco" name="origem_endereco" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="origem_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="origem_cidade" name="origem_cidade" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="origem_estado" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="origem_estado" name="origem_estado" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="origem_cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="origem_cep" name="origem_cep" required>
                            </div>
                            <div class="mb-3">
                                <label for="origem_pais" class="form-label">País</label>
                                <input type="text" class="form-control" id="origem_pais" name="origem_pais" value="Brasil" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Destino</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="destino_nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="destino_nome" name="destino_nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="destino_endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="destino_endereco" name="destino_endereco" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="destino_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="destino_cidade" name="destino_cidade" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="destino_estado" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="destino_estado" name="destino_estado" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="destino_cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="destino_cep" name="destino_cep" required>
                            </div>
                            <div class="mb-3">
                                <label for="destino_pais" class="form-label">País</label>
                                <input type="text" class="form-control" id="destino_pais" name="destino_pais" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Dimensões da Caixa</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-6 mb-3">
                                    <label for="altura" class="form-label">Altura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="altura" name="altura" required>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label for="largura" class="form-label">Largura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="largura" name="largura" required>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="comprimento" name="comprimento" required>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label for="peso_caixa" class="form-label">Peso da Caixa (kg)</label>
                                    <input type="number" step="0.1" min="0.1" class="form-control" id="peso_caixa" name="peso_caixa" required>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> O peso total do envio será calculado como a soma do peso da caixa com o peso dos produtos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i> Enviar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Array para armazenar os produtos adicionados
        let produtos = [];
        let valorTotal = 0;
        let pesoTotal = 0;
        
        // Função para atualizar o resumo de produtos
        function atualizarResumo() {
            valorTotal = 0;
            pesoTotal = 0;
            
            produtos.forEach(function(produto) {
                valorTotal += produto.valor * produto.quantidade;
                pesoTotal += produto.peso * produto.quantidade;
            });
            
            $('#valor-total').text(valorTotal.toFixed(2));
            $('#peso-total').text(pesoTotal.toFixed(2));
            
            // Atualizando os campos ocultos para envio
            $('#produtos-json').val(JSON.stringify(produtos));
            $('#valor-total-input').val(valorTotal.toFixed(2));
            $('#peso-total-input').val(pesoTotal.toFixed(2));
            
            // Mostrar ou esconder o resumo
            if (produtos.length > 0) {
                $('#resumo-produtos').removeClass('d-none');
                $('#sem-produtos-alert').addClass('d-none');
            } else {
                $('#resumo-produtos').addClass('d-none');
                $('#sem-produtos-alert').removeClass('d-none');
            }
        }
        
        // Função para renderizar os cards de produtos
        function renderizarProdutos() {
            const container = $('#produtos-cards');
            container.empty();
            
            produtos.forEach(function(produto, index) {
                const card = `
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">${produto.nome}</h5>
                                <p class="card-text">
                                    <small class="text-muted">Peso unitário: ${produto.peso} kg</small><br>
                                    <small class="text-muted">Valor unitário: R$ ${produto.valor.toFixed(2)}</small>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary btn-diminuir" data-index="${index}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="btn btn-outline-secondary disabled">${produto.quantidade}</span>
                                        <button type="button" class="btn btn-outline-secondary btn-aumentar" data-index="${index}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-remover" data-index="${index}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="mt-2 text-end">
                                    <strong>Subtotal: R$ ${(produto.valor * produto.quantidade).toFixed(2)}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.append(card);
            });
            
            // Adicionar eventos após renderizar
            $('.btn-diminuir').on('click', function() {
                const index = $(this).data('index');
                if (produtos[index].quantidade > 1) {
                    produtos[index].quantidade--;
                    renderizarProdutos();
                    atualizarResumo();
                }
            });
            
            $('.btn-aumentar').on('click', function() {
                const index = $(this).data('index');
                produtos[index].quantidade++;
                renderizarProdutos();
                atualizarResumo();
            });
            
            $('.btn-remover').on('click', function() {
                const index = $(this).data('index');
                produtos.splice(index, 1);
                renderizarProdutos();
                atualizarResumo();
            });
        }
        
        // Evento de adicionar produto
        $('#adicionar-produto').on('click', function() {
            const select = $('#produto-select');
            const option = select.find('option:selected');
            
            if (option.val()) {
                const id = option.val();
                const nome = option.data('nome');
                const peso = parseFloat(option.data('peso'));
                const valor = parseFloat(option.data('valor'));
                const quantidade = parseInt($('#produto-quantidade').val());
                
                const produto = {
                    id: id,
                    nome: nome,
                    peso: peso,
                    valor: valor,
                    quantidade: quantidade
                };
                
                // Verificar se o produto já existe
                const existingIndex = produtos.findIndex(p => p.id === id);
                
                if (existingIndex !== -1) {
                    // Se existir, atualiza a quantidade
                    produtos[existingIndex].quantidade += quantidade;
                } else {
                    // Se não existir, adiciona
                    produtos.push(produto);
                }
                
                // Resetar o formulário de adicionar
                select.val('');
                $('#produto-quantidade').val(1);
                
                // Renderizar produtos e atualizar resumo
                renderizarProdutos();
                atualizarResumo();
            }
        });
        
        // Processar o envio do formulário
        $('#envio-form').on('submit', function(e) {
            e.preventDefault();
            
            // Verificar se há produtos adicionados
            if (produtos.length === 0) {
                showAlert('Adicione pelo menos um produto para envio.', 'warning');
                return false;
            }
            
            // Submeter o formulário via AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    showAlert('Envio registrado com sucesso!', 'success');
                    setTimeout(function() {
                        loadSection('pagamento');
                    }, 1500);
                },
                error: function(xhr) {
                    showAlert('Erro ao processar o envio. Verifique os campos e tente novamente.', 'danger');
                }
            });
        });
    });
</script> 
</script> 