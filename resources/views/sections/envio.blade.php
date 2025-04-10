<div class="card">
    <div class="card-header">
        <i class="fas fa-shipping-fast me-2"></i> Dados do Envio
    </div>
    <div class="card-body">
        <form id="envio-form" action="{{ route('api.envio.processar') }}" method="POST">
            @csrf
            
            <!-- Adicionar CSS do Select2 -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            
            <style>
                /* Removendo estilos anteriores */
                #produto-select {
                    max-height: 300px;
                }
                .select2-container {
                    width: 100% !important;
                }
                .select2-selection {
                    height: 38px !important;
                    border-radius: 0.375rem !important;
                    border: 1px solid #dee2e6 !important;
                    padding: 0.375rem 0.75rem !important;
                }
                .select2-selection__arrow {
                    height: 38px !important;
                }
                .select2-search__field {
                    padding: 8px !important;
                }
                .select2-results__option {
                    padding: 8px;
                    border-bottom: 1px solid #eee;
                }
                .select2-results__option:hover {
                    background-color: #f0f7ff;
                }
                .select2-container--default .select2-results__option[aria-disabled=true] {
                    color: #999;
                    font-style: italic;
                    background-color: #f9f9f9;
                }
                
                /* Estilos específicos para a seção de envio */
                .origem-header {
                    background-color: #e3f2fd;
                }
                .destino-header {
                    background-color: #fff8e1;
                }
                
                /* Estilo para o indicador de carregamento */
                .loading {
                    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHJva2U9IiM2NjY2NjYiPiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMSAxKSIgc3Ryb2tlLXdpZHRoPSIyIj4gICAgICAgICAgICA8Y2lyY2xlIHN0cm9rZS1vcGFjaXR5PSIuNSIgY3g9IjE4IiBjeT0iMTgiIHI9IjE4Ii8+ICAgICAgICAgICAgPHBhdGggZD0iTTM2IDE4YzAtOS45NC04LjA2LTE4LTE4LTE4Ij4gICAgICAgICAgICAgICAgPGFuaW1hdGVUcmFuc2Zvcm0gICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgICAgICAgICAgICAgICAgICAgIHR5cGU9InJvdGF0ZSIgICAgICAgICAgICAgICAgICAgIGZyb209IjAgMTggMTgiICAgICAgICAgICAgICAgICAgICB0bz0iMzYwIDE4IDE4IiAgICAgICAgICAgICAgICAgICAgZHVyPSIxcyIgICAgICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+ICAgICAgICAgICAgPC9wYXRoPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+');
                    background-position: calc(100% - 10px) center;
                    background-repeat: no-repeat;
                    background-size: 20px 20px;
                    padding-right: 40px !important;
                }
                
                /* Aumentar o tamanho dos cards de produtos */
            </style>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Produtos para Envio</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-5 col-md-5">
                                    <div class="mb-2">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" id="busca-descricao" placeholder="Buscar por descrição...">
                                            <button class="btn btn-outline-secondary" type="button" id="limpar-busca">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                            <input type="text" class="form-control" id="busca-codigo" placeholder="Buscar por NCM...">
                                        </div>
                                    </div>
                                    <div class="position-relative">
                                        <select class="form-select produto-select-dropdown" id="produto-select">
                                            <option value="" selected disabled>Selecione um produto</option>
                                        </select>
                                        <small class="text-muted" id="select-status">Carregando produtos...</small>
                                        <button type="button" class="btn btn-sm btn-outline-secondary position-absolute" 
                                                style="top: 0; right: 40px; display: none;" 
                                                id="reload-produtos" 
                                                title="Recarregar produtos">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Quantidade</span>
                                        <input type="number" class="form-control" id="produto-quantidade" min="1" value="1">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">Valor R$</span>
                                        <input type="number" class="form-control" id="produto-valor" min="0.01" step="0.01" value="0.00">
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
            
            <!-- 2. Dimensões da Caixa (agora como cards) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Dimensões da Caixa</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-6 mb-3">
                                    <label for="altura" class="form-label">Altura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="altura" name="altura_temp" value="10">
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <label for="largura" class="form-label">Largura (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="largura" name="largura_temp" value="20">
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <label for="comprimento" class="form-label">Comprimento (cm)</label>
                                    <input type="number" step="0.1" min="1" class="form-control" id="comprimento" name="comprimento_temp" value="30">
                                </div>
                                <div class="col-md-2 col-6 mb-3">
                                    <label for="peso_caixa" class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.1" min="0.1" class="form-control" id="peso_caixa" name="peso_caixa_temp" value="0.5">
                                </div>
                                <div class="col-md-4 col-12 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" id="adicionar-caixa">
                                        <i class="fas fa-plus me-2"></i>Adicionar Caixa
                                    </button>
                                </div>
                            </div>
                            
                            <div id="caixas-container" class="mt-4">
                                <div class="alert alert-info" id="sem-caixas-alert">
                                    <i class="fas fa-info-circle me-2"></i> Adicione pelo menos uma caixa para o envio.
                                </div>
                                <div id="caixas-cards" class="row g-3">
                                    <!-- Os cards de caixas serão adicionados aqui dinamicamente -->
                                </div>
                            </div>
                            
                            <!-- Campos ocultos para enviar os dados das caixas -->
                            <input type="hidden" name="caixas_json" id="caixas-json">
                            
                            <!-- Campos ocultos para dimensões -->
                            <input type="hidden" name="altura" id="altura-hidden">
                            <input type="hidden" name="largura" id="largura-hidden">
                            <input type="hidden" name="comprimento" id="comprimento-hidden">
                            <input type="hidden" name="peso_caixa" id="peso-caixa-hidden">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 3. Informações de Origem e Destino -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 section-card">
                        <div class="card-header origem-header">
                            <h5 class="mb-0"><i class="fas fa-home me-2"></i> Origem</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="origem_nome" class="form-label required">Nome</label>
                                <input type="text" class="form-control" id="origem_nome" name="origem_nome" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="origem_telefone" class="form-label required">Telefone</label>
                                <input type="text" class="form-control" id="origem_telefone" name="origem_telefone" required placeholder="+55 11 98765-4321">
                            </div>
                            
                            <div class="mb-3">
                                <label for="origem_email" class="form-label required">E-mail</label>
                                <input type="email" class="form-control" id="origem_email" name="origem_email" required placeholder="email@exemplo.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="origem_endereco" class="form-label required">Endereço</label>
                                <input type="text" class="form-control" id="origem_endereco" name="origem_endereco" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="origem_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="origem_complemento" name="origem_complemento">
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="origem_cidade" class="form-label required">Cidade</label>
                                    <input type="text" class="form-control" id="origem_cidade" name="origem_cidade" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="origem_estado" class="form-label required">Estado</label>
                                    <input type="text" class="form-control" id="origem_estado" name="origem_estado" required maxlength="2">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="origem_cep" class="form-label required">CEP</label>
                                    <input type="text" class="form-control" id="origem_cep" name="origem_cep" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="origem_pais" class="form-label required">País</label>
                                    <select class="form-select" id="origem_pais" name="origem_pais" required>
                                        <option value="BR" selected>Brasil</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 section-card">
                        <div class="card-header destino-header">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Destino</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="destino_nome" class="form-label required">Nome</label>
                                <input type="text" class="form-control" id="destino_nome" name="destino_nome" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="destino_telefone" class="form-label required">Telefone</label>
                                <input type="text" class="form-control" id="destino_telefone" name="destino_telefone" required placeholder="+1 555 123-4567">
                            </div>
                            
                            <div class="mb-3">
                                <label for="destino_email" class="form-label required">E-mail</label>
                                <input type="email" class="form-control" id="destino_email" name="destino_email" required placeholder="email@exemplo.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="destino_endereco" class="form-label required">Endereço</label>
                                <input type="text" class="form-control" id="destino_endereco" name="destino_endereco" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="destino_complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="destino_complemento" name="destino_complemento">
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="destino_cidade" class="form-label required">Cidade</label>
                                    <input type="text" class="form-control" id="destino_cidade" name="destino_cidade" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="destino_estado" class="form-label required">Estado</label>
                                    <input type="text" class="form-control" id="destino_estado" name="destino_estado" required maxlength="2">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="destino_cep" class="form-label required">CEP</label>
                                    <input type="text" class="form-control" id="destino_cep" name="destino_cep" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="destino_pais" class="form-label required">País</label>
                                    <select class="form-select" id="destino_pais" name="destino_pais" required>
                                        <option value="US" selected>Estados Unidos</option>
                                        <option value="CA">Canadá</option>
                                        <option value="MX">México</option>
                                        <option value="PT">Portugal</option>
                                        <option value="ES">Espanha</option>
                                        <option value="IT">Itália</option>
                                        <option value="FR">França</option>
                                        <option value="DE">Alemanha</option>
                                        <option value="UK">Reino Unido</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 4. Serviço de Entrega -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-light">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i> Serviço de Entrega FedEx</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="servico_entrega" class="form-label required">Serviço de Entrega</label>
                                    <select class="form-select" id="servico_entrega" name="servico_entrega" required>
                                        <option value="" disabled selected>Selecione o serviço</option>
                                        <option value="FEDEX_INTERNATIONAL_PRIORITY">FedEx International Priority</option>
                                        <option value="FEDEX_INTERNATIONAL_ECONOMY">FedEx International Economy</option>
                                        <option value="INTERNATIONAL_PRIORITY_EXPRESS">International Priority Express</option>
                                        <option value="INTERNATIONAL_PRIORITY">International Priority</option>
                                        <option value="INTERNATIONAL_ECONOMY">International Economy</option>
                                    </select>
                                    <small class="text-muted">O serviço determina a velocidade de entrega e o preço</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-button">
                    <i class="fas fa-paper-plane me-2"></i> Processar Envio
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Adicionar o script do Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        console.log("Documento pronto, iniciando script");
        
        // Função para mostrar alertas
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
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
        
        // Verificar se o Select2 está disponível
        if (typeof $.fn.select2 === 'undefined') {
            console.error("Select2 não está carregado! Tentando carregar novamente...");
            // Tentar carregar o Select2 novamente
            $.getScript("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js")
                .done(function() {
                    console.log("Select2 carregado com sucesso");
                    inicializarSelect2();
                })
                .fail(function(jqxhr, settings, exception) {
                    console.error("Erro ao carregar Select2:", exception);
                    alert("Erro ao carregar o componente de seleção de produtos. Por favor, recarregue a página.");
                });
        } else {
            console.log("Select2 já está carregado, inicializando...");
            inicializarSelect2();
        }
        
        // Variável para armazenar a última resposta do Gemini
        let ultimaDescricaoGemini = '';
        
        // Array para armazenar os produtos adicionados
        let produtos = [];
        let valorTotal = 0;
        let pesoTotal = 0;
        
        // Array para armazenar as caixas adicionadas
        let caixas = [];
        
        // Variáveis para controle de paginação e busca
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;
        
        // Dados de países, estados e cidades
        const paises = [
            { id: "BR", nome: "Brasil" },
            { id: "US", nome: "Estados Unidos" },
            { id: "PT", nome: "Portugal" },
            { id: "ES", nome: "Espanha" },
            { id: "FR", nome: "França" },
            { id: "IT", nome: "Itália" },
            { id: "DE", nome: "Alemanha" },
            { id: "GB", nome: "Reino Unido" },
            { id: "JP", nome: "Japão" },
            { id: "CN", nome: "China" },
            { id: "AR", nome: "Argentina" },
            { id: "UY", nome: "Uruguai" },
            { id: "CL", nome: "Chile" },
            { id: "MX", nome: "México" },
            { id: "CA", nome: "Canadá" },
            { id: "AU", nome: "Austrália" }
        ];
        
        const estados = {
            "BR": [
                { id: "AC", nome: "Acre" },
                { id: "AL", nome: "Alagoas" },
                { id: "AM", nome: "Amazonas" },
                { id: "AP", nome: "Amapá" },
                { id: "BA", nome: "Bahia" },
                { id: "CE", nome: "Ceará" },
                { id: "DF", nome: "Distrito Federal" },
                { id: "ES", nome: "Espírito Santo" },
                { id: "GO", nome: "Goiás" },
                { id: "MA", nome: "Maranhão" },
                { id: "MG", nome: "Minas Gerais" },
                { id: "MS", nome: "Mato Grosso do Sul" },
                { id: "MT", nome: "Mato Grosso" },
                { id: "PA", nome: "Pará" },
                { id: "PB", nome: "Paraíba" },
                { id: "PE", nome: "Pernambuco" },
                { id: "PI", nome: "Piauí" },
                { id: "PR", nome: "Paraná" },
                { id: "RJ", nome: "Rio de Janeiro" },
                { id: "RN", nome: "Rio Grande do Norte" },
                { id: "RO", nome: "Rondônia" },
                { id: "RR", nome: "Roraima" },
                { id: "RS", nome: "Rio Grande do Sul" },
                { id: "SC", nome: "Santa Catarina" },
                { id: "SE", nome: "Sergipe" },
                { id: "SP", nome: "São Paulo" },
                { id: "TO", nome: "Tocantins" }
            ],
            "US": [
                { id: "AL", nome: "Alabama" },
                { id: "AK", nome: "Alaska" },
                { id: "AZ", nome: "Arizona" },
                { id: "AR", nome: "Arkansas" },
                { id: "CA", nome: "California" },
                { id: "CO", nome: "Colorado" },
                { id: "CT", nome: "Connecticut" },
                { id: "DE", nome: "Delaware" },
                { id: "FL", nome: "Florida" },
                { id: "GA", nome: "Georgia" },
                { id: "HI", nome: "Hawaii" },
                { id: "ID", nome: "Idaho" },
                { id: "IL", nome: "Illinois" },
                { id: "IN", nome: "Indiana" },
                { id: "IA", nome: "Iowa" },
                { id: "KS", nome: "Kansas" },
                { id: "KY", nome: "Kentucky" },
                { id: "LA", nome: "Louisiana" },
                { id: "ME", nome: "Maine" },
                { id: "MD", nome: "Maryland" },
                { id: "MA", nome: "Massachusetts" },
                { id: "MI", nome: "Michigan" },
                { id: "MN", nome: "Minnesota" },
                { id: "MS", nome: "Mississippi" },
                { id: "MO", nome: "Missouri" },
                { id: "MT", nome: "Montana" },
                { id: "NE", nome: "Nebraska" },
                { id: "NV", nome: "Nevada" },
                { id: "NH", nome: "New Hampshire" },
                { id: "NJ", nome: "New Jersey" },
                { id: "NM", nome: "New Mexico" },
                { id: "NY", nome: "New York" },
                { id: "NC", nome: "North Carolina" },
                { id: "ND", nome: "North Dakota" },
                { id: "OH", nome: "Ohio" },
                { id: "OK", nome: "Oklahoma" },
                { id: "OR", nome: "Oregon" },
                { id: "PA", nome: "Pennsylvania" },
                { id: "RI", nome: "Rhode Island" },
                { id: "SC", nome: "South Carolina" },
                { id: "SD", nome: "South Dakota" },
                { id: "TN", nome: "Tennessee" },
                { id: "TX", nome: "Texas" },
                { id: "UT", nome: "Utah" },
                { id: "VT", nome: "Vermont" },
                { id: "VA", nome: "Virginia" },
                { id: "WA", nome: "Washington" },
                { id: "WV", nome: "West Virginia" },
                { id: "WI", nome: "Wisconsin" },
                { id: "WY", nome: "Wyoming" }
            ],
            // Adicionar alguns estados básicos para outros países
            "PT": [
                { id: "LI", nome: "Lisboa" },
                { id: "PO", nome: "Porto" },
                { id: "FA", nome: "Faro" },
                { id: "CO", nome: "Coimbra" }
            ]
            // Demais países podem ser adicionados conforme necessário
        };
        
        const cidades = {
            "SP": [
                { id: "SAO", nome: "São Paulo" },
                { id: "CAM", nome: "Campinas" },
                { id: "RIB", nome: "Ribeirão Preto" },
                { id: "SJC", nome: "São José dos Campos" },
                { id: "SAN", nome: "Santos" }
            ],
            "RJ": [
                { id: "RIO", nome: "Rio de Janeiro" },
                { id: "NIT", nome: "Niterói" },
                { id: "PET", nome: "Petrópolis" },
                { id: "MAC", nome: "Macaé" }
            ],
            "MG": [
                { id: "BHZ", nome: "Belo Horizonte" },
                { id: "UBE", nome: "Uberlândia" },
                { id: "CON", nome: "Contagem" },
                { id: "JDF", nome: "Juiz de Fora" },
                { id: "MOC", nome: "Montes Claros" },
                { id: "IPA", nome: "Ipatinga" },
                { id: "DIV", nome: "Divinópolis" },
                { id: "POC", nome: "Poços de Caldas" },
                { id: "VAR", nome: "Varginha" },
                { id: "UBA", nome: "Uberaba" },
                { id: "GVR", nome: "Governador Valadares" },
                { id: "PSS", nome: "Pouso Alegre" },
                { id: "SJR", nome: "São João del-Rei" },
                { id: "ITA", nome: "Itajubá" },
                { id: "LAV", nome: "Lavras" },
                { id: "BAR", nome: "Barbacena" },
                { id: "ARA", nome: "Araxá" },
                { id: "ITU", nome: "Ituiutaba" },
                { id: "FOR", nome: "Formiga" },
                { id: "CAT", nome: "Cataguases" },
                { id: "TEO", nome: "Teófilo Otoni" },
                { id: "PSO", nome: "Passos" },
                { id: "MUR", nome: "Muriaé" },
                { id: "PAT", nome: "Patos de Minas" },
                { id: "IBI", nome: "Ibirité" },
                { id: "SAB", nome: "Sabará" },
                { id: "NLA", nome: "Nova Lima" },
                { id: "LFO", nome: "Lafaiete" },
                { id: "BTC", nome: "Betim" },
                { id: "SCL", nome: "Santa Luzia" },
                { id: "ITC", nome: "Itaúna" },
                { id: "COG", nome: "Congonhas" },
                { id: "AXE", nome: "Araguari" },
                { id: "PAR", nome: "Paracatu" },
                { id: "TPI", nome: "Três Pontas" },
                { id: "OPA", nome: "Ouro Preto" }
            ],
            "CA": [
                { id: "LA", nome: "Los Angeles" },
                { id: "SF", nome: "San Francisco" },
                { id: "SD", nome: "San Diego" },
                { id: "SJ", nome: "San Jose" }
            ],
            "NY": [
                { id: "NYC", nome: "New York City" },
                { id: "BUF", nome: "Buffalo" },
                { id: "ROC", nome: "Rochester" },
                { id: "SYR", nome: "Syracuse" }
            ],
            "TX": [
                { id: "HOU", nome: "Houston" },
                { id: "DAL", nome: "Dallas" },
                { id: "AUS", nome: "Austin" },
                { id: "SAT", nome: "San Antonio" }
            ],
            "LI": [
                { id: "LIS", nome: "Lisboa" },
                { id: "CAS", nome: "Cascais" },
                { id: "SIN", nome: "Sintra" },
                { id: "OEI", nome: "Oeiras" }
            ]
            // Outras cidades podem ser adicionadas conforme necessário
        };
        
        // Variável para controlar se o Select2 está sendo inicializado
        let inicializandoSelect2 = false;
        
        // Função para inicializar o Select2
        function inicializarSelect2() {
            // Evitar inicialização múltipla
            if (inicializandoSelect2) {
                console.log("Select2 já está sendo inicializado. Aguardando...");
                return;
            }
            
            inicializandoSelect2 = true;
            console.log("Destruindo instância anterior de Select2 caso exista");
            
            // Fechar qualquer dropdown aberto
            $('.select2-container').remove();
            
            // Destruir instância anterior caso exista
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }
            
            // Limpar lista de produtos e garantir que tenha a opção padrão
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));
            
            console.log("Inicializando novo Select2");
            $('#produto-select').select2({
                placeholder: 'Selecione ou busque um produto',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#produto-select').parent(),
                language: {
                    noResults: function() {
                        return "Nenhum produto encontrado";
                    },
                    searching: function() {
                        return "Buscando...";
                    },
                    inputTooShort: function() {
                        return "Digite pelo menos 3 caracteres para buscar";
                    }
                },
                minimumInputLength: 0 // Permitir carregar todos os produtos sem digitar
            });
            
            // Evento ao selecionar um produto
            $('#produto-select').on('select2:select', function(e) {
                const produtoSelecionado = e.params.data;
                console.log("Produto selecionado:", produtoSelecionado);
                
                // Garantir que temos as propriedades código e NCM
                const ncm = produtoSelecionado.codigo || produtoSelecionado.id;
                const descricao = produtoSelecionado.text.split(' (NCM:')[0]; // Extrair apenas a descrição
                
                // Atualizar também o campo de NCM para mostrar o código do produto selecionado
                $('#busca-codigo').val(ncm);
                
                // Mostrar uma mensagem informativa sobre o produto selecionado
                $('#select-status').html('<strong>Produto selecionado:</strong> ' + descricao + ' <span class="badge bg-info">NCM: ' + ncm + '</span>');
                
                // Sugerir valor inicial (pode ser editado pelo usuário)
                const valorSugerido = produtoSelecionado.valor || 10.00;
                $('#produto-valor').val(valorSugerido.toFixed(2));
                $('#produto-valor').select(); // Seleciona o texto para fácil edição
            });
            
            inicializandoSelect2 = false;
            
            // Não realizamos a busca automática aqui - a busca será feita por quem chamou a função
        }
        
        // Função para realizar a busca baseada nos campos de busca
        function realizarBusca() {
            $('#select-status').text('Buscando produtos...');
            
            const buscaDescricao = $('#busca-descricao').val();
            const buscaNCM = $('#busca-codigo').val();
            
            // Limpar a descrição anterior do Gemini
            ultimaDescricaoGemini = '';
            
            // Destruir a instância do Select2 para garantir que seja completamente reinicializado
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }
            
            // Limpar completamente o select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));
            
            // Se tiver uma descrição de produto e não tiver um NCM, consultar o Gemini
            if (buscaDescricao && !buscaNCM) {
                $('#select-status').text('Consultando IA para identificar NCM...');
                
                // Mostrar indicador de carregamento
                $('#busca-descricao').addClass('loading');
                
                // Chama o endpoint para consultar o Gemini
                $.ajax({
                    url: '{{ route("api.consulta.gemini") }}',
                    method: 'POST',
                    data: { produto: buscaDescricao },
                    dataType: 'json',
                    beforeSend: function() {
                        console.log("Enviando consulta para o Gemini para o produto:", buscaDescricao);
                    },
                    success: function(response) {
                        // Ocultar indicador de carregamento
                        $('#busca-descricao').removeClass('loading');
                        
                        console.log("Resposta completa da API:", response);
                        
                        if (response.success) {
                            console.log("Consulta bem-sucedida. Resposta do Gemini:", response.resultado);
                        
                            // Extrair o NCM da resposta
                            const ncmExtraido = extrairNCM(response.resultado);
                            
                            // Armazenar a descrição completa do Gemini
                            ultimaDescricaoGemini = response.resultado;
                            
                            if (ncmExtraido) {
                                console.log("NCM extraído:", ncmExtraido);
                                $('#select-status').text('NCM identificado: ' + ncmExtraido + '. Buscando produtos...');
                                
                                // Atualizar apenas o campo NCM, mantendo a descrição do produto
                                $('#busca-codigo').val(ncmExtraido);
                                
                                // Buscar produtos pelo NCM extraído
                                buscarProdutos({ codigo: ncmExtraido, descricao: buscaDescricao });
                            } else {
                                $('#select-status').text('Não foi possível identificar o NCM. Tentando busca direta...');
                                // Continuar com a busca normal
                                buscarProdutos({ descricao: buscaDescricao });
                            }
                        } else {
                            $('#select-status').text('Erro na consulta da IA. Tentando busca direta...');
                            console.error("Erro na consulta Gemini:", response.error);
                            // Continuar com a busca normal
                            buscarProdutos({ descricao: buscaDescricao });
                        }
                    },
                    error: function(error) {
                        // Ocultar indicador de carregamento
                        $('#busca-descricao').removeClass('loading');
                        
                        $('#select-status').text('Erro na consulta da IA. Tentando busca direta...');
                        console.error("Erro ao consultar a IA:", error);
                        // Continuar com a busca normal
                        buscarProdutos({ descricao: buscaDescricao });
                    }
                });
            } else {
                // Criar o objeto de busca normal
                const searchParams = {};
                if (buscaDescricao) searchParams.descricao = buscaDescricao;
                if (buscaNCM) searchParams.codigo = buscaNCM;
                
                // Executar a busca direta
                buscarProdutos(searchParams);
            }
        }
        
        // Função para extrair o NCM da resposta do Gemini
        function extrairNCM(texto) {
            console.log("Extraindo NCM do texto:", texto);
            
            // Caso específico para Havaianas
            if (texto.toLowerCase().includes('havaianas') && texto.includes('6402.20.00')) {
                console.log("Caso especial: Havaianas encontrado com NCM 6402.20.00");
                return '6402.20.00';
            }
            
            // Primeiro tenta encontrar o NCM entre asteriscos, uma convenção comum
            const boldMatch = texto.match(/\*\*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})\*\*/);
            if (boldMatch && boldMatch[1]) {
                console.log("NCM encontrado em destaque:", boldMatch[1]);
                return formatarNCM(boldMatch[1]);
            }
            
            // Padrões para encontrar o NCM na resposta
            const padroes = [
                /NCM[:\s]*(?:é|e|do produto)?[:\s]*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/i, // "NCM é: 6402.20.00" ou "NCM do produto: 6402.20.00"
                /código NCM[:\s]*([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/i, // "código NCM: 6402.20.00"
                /([0-9]{4}\.?[0-9]{2}\.?[0-9]{2})/, // Formato numérico simples com ou sem pontos
                /NCM[:\s]+([0-9]{4}\.?[0-9]{2})/i, // "NCM: 6402.20" (formato parcial)
            ];
            
            for (const padrao of padroes) {
                const match = texto.match(padrao);
                if (match && match[1]) {
                    console.log("NCM encontrado com padrão:", padrao.toString(), match[1]);
                    return formatarNCM(match[1]);
                }
            }
            
            console.log("Nenhum NCM encontrado no texto");
            return null;
        }
        
        // Função auxiliar para formatar o NCM encontrado
        function formatarNCM(ncm) {
            // Remover pontos se houver
            let ncmLimpo = ncm.replace(/\./g, '');
            
            // Padronizar para o formato que está no JSON (com pontos)
            if (ncmLimpo.length >= 8) {
                // Formatar como XXXX.XX.XX
                return ncmLimpo.slice(0, 4) + '.' + ncmLimpo.slice(4, 6) + '.' + ncmLimpo.slice(6, 8);
            } else if (ncmLimpo.length >= 6) {
                // Formatar como XXXX.XX
                return ncmLimpo.slice(0, 4) + '.' + ncmLimpo.slice(4, 6);
            } else if (ncmLimpo.length >= 4) {
                // Formatar como XXXX
                return ncmLimpo.slice(0, 4);
            }
            
            return ncm; // Retorna como está se não conseguir formatar
        }
        
        // Função para buscar produtos por NCM
        function buscarProdutosPorNCM(ncm) {
            console.log("Buscando produtos com NCM:", ncm);
            
            // Adicionar o NCM também no campo de busca por código para visualização
            $('#busca-codigo').val(ncm);
            
            // Manter o valor atual do campo de descrição
            const descricaoAtual = $('#busca-descricao').val();
            
            // Executar a busca usando o NCM e mantendo a descrição
            if (descricaoAtual) {
                buscarProdutos({ codigo: ncm, descricao: descricaoAtual });
            } else {
                buscarProdutos({ codigo: ncm });
            }
        }
        
        // Função para buscar produtos (extraída da busca original)
        function buscarProdutos(searchParams) {
            // Garantir que a interface limpe completamente os resultados anteriores
            $('#select-status').text('Buscando produtos...');
            
            // Destruir completamente a instância atual do Select2
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }
            
            // Limpar completamente o select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));
            
            // Inicializar o Select2 novamente
            inicializarSelect2();
            
            // Agora fazer a busca
            $.ajax({
                url: '{{ route("api.produtos.get") }}',
                data: { 
                    page: 1, 
                    limit: 100, 
                    search: JSON.stringify(searchParams)
                },
                dataType: 'json',
                success: function(data) {
                    console.log("Resultados da busca:", data);
                    
                    // Limpar o select novamente para garantir
                    $('#produto-select').empty();
                    $('#produto-select').append(new Option('Selecione um produto', '', true, true));
                    
                    if (data && data.produtos && data.produtos.length) {
                        // Criar e adicionar as opções
                        data.produtos.forEach(function(produto) {
                            // Adicionar o NCM na descrição do produto
                            const descricaoFormatada = produto.descricao + ' (NCM: ' + produto.codigo + ')';
                            
                            const option = new Option(descricaoFormatada, produto.codigo, false, false);
                            // Armazenar dados adicionais para uso posterior
                            $(option).data('codigo', produto.codigo);
                            $(option).data('descricao', produto.descricao);
                            $(option).data('peso', 0.5); // Valor padrão
                            $(option).data('valor', 10); // Valor padrão
                            
                            $('#produto-select').append(option);
                        });
                        
                        // Acionar o change para atualizar o Select2
                        $('#produto-select').trigger('change');
                        
                        console.log("Opções carregadas:", data.produtos.length);
                        
                        // Mostrar quantos produtos foram encontrados e o NCM identificado
                        if (searchParams.codigo) {
                            $('#select-status').html('<strong>' + data.produtos.length + ' produtos encontrados</strong> com NCM: ' + searchParams.codigo);
                        } else {
                            $('#select-status').text(data.produtos.length + ' produtos encontrados');
                        }
                        
                        // Esconder o botão de reload, pois os produtos foram carregados com sucesso
                        $('#reload-produtos').hide();
                    } else {
                        if (searchParams.codigo) {
                            // Se não encontrou produtos, mas temos uma descrição do Gemini, exibi-la
                            if (ultimaDescricaoGemini) {
                                // Extrair a descrição relevante do resultado do Gemini
                                let descricaoLimpa = '';
                                
                                // Extrair o texto de resposta útil
                                let respostaUtil = '';
                                
                                // Se tiver o marcador de resultado da consulta, usar apenas o que vem depois
                                if (ultimaDescricaoGemini.includes('Resultado da consulta:')) {
                                    respostaUtil = ultimaDescricaoGemini.split('Resultado da consulta:')[1];
                                } else if (ultimaDescricaoGemini.includes('Resposta recebida:')) {
                                    // Se tiver JSON na resposta, ignorá-lo
                                    const partes = ultimaDescricaoGemini.split('Resposta recebida:');
                                    if (partes.length > 1) {
                                        // Tenta encontrar onde termina o JSON e começa o texto real
                                        const textoRestante = partes[1];
                                        if (textoRestante.includes('Resultado da consulta:')) {
                                            respostaUtil = textoRestante.split('Resultado da consulta:')[1];
                                        } else {
                                            // Se não encontrar o marcador, usar tudo que vem depois do início do JSON
                                            const jsonFim = textoRestante.indexOf('}') + 1;
                                            if (jsonFim > 0) {
                                                respostaUtil = textoRestante.substring(jsonFim);
                                            } else {
                                                respostaUtil = textoRestante;
                                            }
                                        }
                                    }
                                } else {
                                    // Usar a resposta completa
                                    respostaUtil = ultimaDescricaoGemini;
                                }
                                
                                // Limpar a resposta
                                respostaUtil = respostaUtil.replace(/---+/g, '').trim();
                                
                                // Tentar encontrar a parte da descrição após o NCM
                                const linhasGemini = respostaUtil.split('\n');
                                for (const linha of linhasGemini) {
                                    // Procurar por uma linha que contenha o NCM e uma descrição
                                    if (linha.includes(searchParams.codigo)) {
                                        // Extrair a descrição após o NCM
                                        const partes = linha.split(' - ');
                                        if (partes.length > 1) {
                                            descricaoLimpa = partes[1].replace(/\*\*/g, '').trim();
                                            break;
                                        } else if (linha.includes(':')) {
                                            // Tentar com dois pontos
                                            const partesDoisPontos = linha.split(':');
                                            if (partesDoisPontos.length > 1) {
                                                descricaoLimpa = partesDoisPontos[1].replace(/\*\*/g, '').trim();
                                                break;
                                            }
                                        }
                                    }
                                }
                                
                                // Se não conseguir extrair a descrição específica, tentar encontrar 
                                // qualquer descrição útil no texto
                                if (!descricaoLimpa) {
                                    // Limpar asteriscos e outros marcadores
                                    respostaUtil = respostaUtil.replace(/\*\*/g, '').trim();
                                    
                                    // Procurar por descrições comuns em produtos
                                    const termos = ['produto', 'artigo', 'mercadoria', 'item', 'bem'];
                                    for (const termo of termos) {
                                        if (respostaUtil.toLowerCase().includes(termo)) {
                                            // Pegar a sentença ou o parágrafo que contém o termo
                                            const sentencas = respostaUtil.split(/[.!?]\s+/);
                                            for (const sentenca of sentencas) {
                                                if (sentenca.toLowerCase().includes(termo)) {
                                                    descricaoLimpa = sentenca.trim();
                                                    break;
                                                }
                                            }
                                            if (descricaoLimpa) break;
                                        }
                                    }
                                    
                                    if (!descricaoLimpa) {
                                        descricaoLimpa = respostaUtil;
                                    }
                                }
                                
                                // Garantir que a descrição não seja muito longa
                                if (descricaoLimpa.length > 100) {
                                    descricaoLimpa = descricaoLimpa.substring(0, 97) + '...';
                                }
                                
                                // Adicionar uma opção manual baseada na descrição do Gemini
                                const descricaoFormatada = descricaoLimpa + ' (NCM: ' + searchParams.codigo + ')';
                                const option = new Option(descricaoFormatada, searchParams.codigo, false, false);
                                
                                // Armazenar dados adicionais para uso posterior
                                $(option).data('codigo', searchParams.codigo);
                                $(option).data('descricao', descricaoLimpa);
                                $(option).data('peso', 0.5); // Valor padrão
                                $(option).data('valor', 10); // Valor padrão
                                
                                $('#produto-select').append(option);
                                $('#produto-select').trigger('change');
                                
                                // Mostrar mensagem informativa
                                $('#select-status').html('<strong>Produto criado com descrição do Gemini</strong> - NCM: ' + searchParams.codigo);
                            } else {
                                $('#select-status').html('<strong>Nenhum produto encontrado</strong> com NCM: ' + searchParams.codigo);
                            }
                        } else {
                            $('#select-status').text('Nenhum produto encontrado');
                        }
                        // Mostrar o botão de reload, pois não há produtos
                        $('#reload-produtos').show();
                    }
                },
                error: function(error) {
                    console.error("Erro ao buscar produtos:", error);
                    $('#select-status').text('Erro ao buscar produtos');
                    // Mostrar o botão de reload em caso de erro
                    $('#reload-produtos').show();
                }
            });
        }
        
        // Eventos para os campos de busca com debounce
        let timer;
        $('#busca-descricao, #busca-codigo').on('input', function() {
            clearTimeout(timer);
            
            // Se o campo de busca por descrição estiver vazio, limpar os resultados
            if ($('#busca-descricao').val() === '') {
                console.log("Campo de busca vazio, limpando resultados");
                $('#busca-codigo').val(''); // Limpar também o campo de NCM
                $('#select-status').text('Digite um produto para buscar');
                
                // Limpar a lista de produtos no select
                const defaultOption = $('#produto-select option[value=""]').clone();
                $('#produto-select').empty().append(defaultOption).trigger('change');
                
                // Esconder o resumo se existir
                if ($('#resumo-produtos').length) {
                    $('#resumo-produtos').addClass('d-none');
                }
                
                // Mostrar mensagem informativa
                $('#sem-produtos-alert').removeClass('d-none');
                
                return; // Não realizar busca se o campo estiver vazio
            }
            
            timer = setTimeout(realizarBusca, 500); // Debounce de 500ms
        });
        
        // Evento do botão de limpar busca
        $('#limpar-busca').on('click', function() {
            console.log("Limpando campo de busca");
            
            // Limpar os campos de entrada
            $('#busca-descricao').val('').focus();
            $('#busca-codigo').val('');
            $('#select-status').text('Digite um produto para buscar');
            
            // Limpar a descrição do Gemini
            ultimaDescricaoGemini = '';
            
            // Destruir instância anterior de Select2
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }
            
            // Limpar a lista de produtos no select
            $('#produto-select').empty();
            $('#produto-select').append(new Option('Selecione um produto', '', true, true));
            
            // Inicializar o Select2 novamente
            inicializarSelect2();
            
            // Mostrar mensagem informativa
            $('#sem-produtos-alert').removeClass('d-none');
            
            // Também limpar os valores dos campos relacionados
            $('#produto-valor').val('0.00');
            $('#produto-quantidade').val('1');
            
            // Buscar os 100 produtos iniciais
            buscarProdutos({});
        });
        
        // Evento do botão de recarregar produtos
        $('#reload-produtos').on('click', function() {
            $('#select-status').text('Recarregando produtos...');
            $(this).prop('disabled', true).addClass('disabled');
            
            // Limpar os campos de busca
            $('#busca-descricao').val('');
            $('#busca-codigo').val(''); // Campo para busca de NCM
            
            inicializarSelect2();
            
            setTimeout(() => {
                $(this).prop('disabled', false).removeClass('disabled');
            }, 2000);
        });
        
        // Função para atualizar o resumo de produtos
        function atualizarResumo() {
            valorTotal = 0;
            pesoTotal = 0;
            
            // Garantir que cada produto tenha valor_unitario
            produtos.forEach(function(produto) {
                // Adicionar valor_unitario se não existir
                if (!produto.valor_unitario && produto.valor !== undefined) {
                    produto.valor_unitario = produto.valor;
                }
                
                valorTotal += produto.valor * produto.quantidade;
                pesoTotal += produto.peso * produto.quantidade;
            });
            
            // Adicionar o peso das caixas
            caixas.forEach(function(caixa) {
                pesoTotal += parseFloat(caixa.peso);
            });
            
            $('#valor-total').text(valorTotal.toFixed(2));
            $('#peso-total').text(pesoTotal.toFixed(2));
            
            // Atualizando os campos ocultos para envio
            $('#produtos-json').val(JSON.stringify(produtos));
            $('#caixas-json').val(JSON.stringify(caixas));
            $('#valor-total-input').val(valorTotal.toFixed(2));
            $('#peso-total-input').val(pesoTotal.toFixed(2));
            
            // Atualizar os campos ocultos de dimensões com a primeira caixa (se existir)
            if (caixas.length > 0) {
                $('#altura-hidden').val(caixas[0].altura);
                $('#largura-hidden').val(caixas[0].largura);
                $('#comprimento-hidden').val(caixas[0].comprimento);
                $('#peso-caixa-hidden').val(caixas[0].peso);
            }
            
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
                                    <small class="text-muted">Ncm: ${produto.codigo || 'N/A'}</small><br>
                                    <small class="text-muted">Peso unitário: ${produto.peso} kg</small><br>
                                    <small class="text-muted">Valor unitário: R$ ${produto.valor.toFixed(2)} <span class="text-info">(informado pelo usuário)</span></small>
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
            const produtoSelecionado = $('#produto-select').select2('data')[0];
            
            if (produtoSelecionado && produtoSelecionado.id) {
                console.log("Produto selecionado:", produtoSelecionado);
                
                const id = produtoSelecionado.id;
                const codigo = produtoSelecionado.codigo || id;
                const nome = produtoSelecionado.text;
                const peso = produtoSelecionado.peso || 0.5;
                const valorInformado = parseFloat($('#produto-valor').val());
                
                // Validar valor
                if (isNaN(valorInformado) || valorInformado <= 0) {
                    alert('Por favor, informe um valor válido para o produto.');
                    $('#produto-valor').focus();
                    return;
                }
                
                const quantidade = parseInt($('#produto-quantidade').val());
                
                const produto = {
                    id: id,
                    codigo: codigo,
                    nome: nome,
                    peso: peso,
                    valor: valorInformado,
                    quantidade: quantidade
                };
                
                console.log("Produto a ser adicionado:", produto);
                
                // Verificar se o produto já existe
                const existingIndex = produtos.findIndex(p => p.id === id);
                
                if (existingIndex !== -1) {
                    // Se existir, atualiza a quantidade
                    produtos[existingIndex].quantidade += quantidade;
                    console.log("Atualizada quantidade do produto existente:", produtos[existingIndex]);
                } else {
                    // Se não existir, adiciona
                    produtos.push(produto);
                    console.log("Novo produto adicionado:", produto);
                }
                
                // Limpar a seleção e resetar a quantidade
                $('#produto-select').val(null).trigger('change');
                $('#produto-quantidade').val(1);
                $('#produto-valor').val(0.00);
                
                // Renderizar produtos e atualizar resumo
                renderizarProdutos();
                atualizarResumo();
            } else {
                // Se não houver produto selecionado
                alert('Por favor, selecione um produto antes de adicionar.');
            }
        });
        
        // Renderizar as caixas adicionadas
        function renderizarCaixas() {
            const container = $('#caixas-cards');
            container.empty();
            
            caixas.forEach(function(caixa, index) {
                const card = `
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Caixa #${index + 1}</h5>
                                <p class="card-text">
                                    <small class="text-muted">Dimensões: ${caixa.altura} × ${caixa.largura} × ${caixa.comprimento} cm</small><br>
                                    <small class="text-muted">Volume: ${(caixa.altura * caixa.largura * caixa.comprimento / 1000).toFixed(2)} litros</small><br>
                                    <small class="text-muted">Peso: ${caixa.peso} kg</small>
                                </p>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-danger btn-remover-caixa" data-index="${index}">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.append(card);
            });
            
            // Adicionar eventos após renderizar
            $('.btn-remover-caixa').on('click', function() {
                const index = $(this).data('index');
                caixas.splice(index, 1);
                renderizarCaixas();
                atualizarResumo();
                
                // Atualizar visualização de alertas
                if (caixas.length === 0) {
                    $('#sem-caixas-alert').removeClass('d-none');
                }
            });
        }
        
        // Evento de adicionar caixa
        $('#adicionar-caixa').on('click', function() {
            const altura = parseFloat($('#altura').val());
            const largura = parseFloat($('#largura').val());
            const comprimento = parseFloat($('#comprimento').val());
            const peso = parseFloat($('#peso_caixa').val());
            
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
                peso: peso
            };
            
            caixas.push(caixa);
            
            // Atualizar também os campos ocultos com os valores da primeira caixa
            if (caixas.length === 1) {
                $('#altura-hidden').val(altura);
                $('#largura-hidden').val(largura);
                $('#comprimento-hidden').val(comprimento);
                $('#peso-caixa-hidden').val(peso);
            }
            
            // Resetar os valores para adicionar nova caixa
            $('#altura').val(10);
            $('#largura').val(20);
            $('#comprimento').val(30);
            $('#peso_caixa').val(0.5);
            
            // Renderizar as caixas e atualizar o resumo
            renderizarCaixas();
            $('#sem-caixas-alert').addClass('d-none');
            atualizarResumo();
        });
        
        // Função para preencher o select de países
        function carregarPaises() {
            $('.pais-select').each(function() {
                const select = $(this);
                select.find('option:not(:first)').remove();
                
                paises.forEach(function(pais) {
                    select.append($('<option>', {
                        value: pais.id,
                        text: pais.nome
                    }));
                });
            });
        }
        
        // Função para preencher o select de estados com base no país selecionado
        function carregarEstados(paisId, estadoSelect) {
            const paisEstados = estados[paisId] || [];
            estadoSelect.find('option:not(:first)').remove();
            estadoSelect.prop('disabled', paisEstados.length === 0);
            
            if (paisEstados.length === 0) {
                estadoSelect.find('option:first').text('Nenhum estado disponível para este país');
                return;
            }
            
            estadoSelect.find('option:first').text('Selecione um estado');
            
            paisEstados.forEach(function(estado) {
                estadoSelect.append($('<option>', {
                    value: estado.id,
                    text: estado.nome
                }));
            });
        }
        
        // Função para preencher o select de cidades com base no estado selecionado
        function carregarCidades(estadoId, cidadeSelect) {
            // Mostrar indicador de carregamento
            cidadeSelect.prop('disabled', true);
            cidadeSelect.find('option:not(:first)').remove();
            cidadeSelect.find('option:first').text('Carregando cidades...');
            
            // Obter informações de contexto
            const formGroup = cidadeSelect.closest('.card-body');
            const paisSelect = formGroup.find('.pais-select');
            const paisId = paisSelect.val();
            const prefixo = paisSelect.attr('id').split('_')[0]; // 'origem' ou 'destino'
            
            // Nome do container que irá conter o select ou input
            const cidadeContainerId = `#${prefixo}_cidade_container`;
            
            // Verificar se já existe o container, caso contrário, criar
            if ($(cidadeContainerId).length === 0) {
                cidadeSelect.wrap(`<div id="${prefixo}_cidade_container"></div>`);
            }
            
            // Primeiro verificar se temos cidades cadastradas para este estado
            const estadoCidades = cidades[estadoId] || [];
            
            // Variável para controlar se devemos usar o IBGE
            const isBrasil = paisId === 'BR';
            const usarAPIIBGE = isBrasil && estadoId.length === 2; // Estados brasileiros têm UF com 2 caracteres
            
            // Se tem cidades no array estático ou vamos usar a API do IBGE, tentamos o select
            if (estadoCidades.length > 0 || usarAPIIBGE) {
                // Se temos cidades cadastradas para este estado, usar o select
                if (estadoCidades.length > 0) {
                    // Exibir o select para estados com cidades cadastradas
                    exibirSelectCidade(prefixo);
                    
                    cidadeSelect.find('option:not(:first)').remove();
                    cidadeSelect.find('option:first').text('Selecione uma cidade');
                    
                    estadoCidades.forEach(function(cidade) {
                        cidadeSelect.append($('<option>', {
                            value: cidade.id,
                            text: cidade.nome
                        }));
                    });
                    
                    cidadeSelect.prop('disabled', false);
                    console.log(`Carregadas ${estadoCidades.length} cidades para o estado ${estadoId} do array estático`);
                }
                // Se não temos cidades cadastradas, mas é um estado brasileiro, usar a API do IBGE
                else if (usarAPIIBGE) {
                    $.ajax({
                        url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Certificar-se de exibir o select para estados brasileiros
                            exibirSelectCidade(prefixo);
                            
                            cidadeSelect.find('option:not(:first)').remove();
                            cidadeSelect.find('option:first').text('Selecione uma cidade');
                            
                            // Ordenar cidades por nome
                            data.sort((a, b) => a.nome.localeCompare(b.nome));
                            
                            // Popular o select com todas as cidades retornadas
                            data.forEach(function(cidade) {
                                cidadeSelect.append($('<option>', {
                                    value: cidade.id,
                                    text: cidade.nome
                                }));
                            });
                            
                            cidadeSelect.prop('disabled', false);
                            
                            console.log(`Carregadas ${data.length} cidades para o estado ${estadoId} via API IBGE`);
                        },
                        error: function(error) {
                            console.error('Erro ao carregar cidades do IBGE:', error);
                            // Exibir campo de entrada de texto se a API falhar
                            exibirInputCidade(prefixo, `Erro ao carregar cidades. Digite o nome da cidade manualmente.`);
                        }
                    });
                }
            } else {
                // Se não temos cidades cadastradas e não é um estado brasileiro, exibir campo de entrada de texto
                exibirInputCidade(prefixo, `Não há lista de cidades disponível para ${obterNomeEstado(estadoId, paisId)}. Digite o nome da cidade manualmente.`);
            }
            
            // Função para obter o nome do estado (para mensagem informativa)
            function obterNomeEstado(estadoId, paisId) {
                const paisEstados = estados[paisId] || [];
                const estado = paisEstados.find(e => e.id === estadoId);
                return estado ? estado.nome : 'este estado';
            }
            
            // Função para exibir o select de cidades
            function exibirSelectCidade(prefixo) {
                const container = $(`#${prefixo}_cidade_container`);
                
                // Se já tem o select, não faz nada
                if (container.find(`select#${prefixo}_cidade`).length > 0) {
                    return;
                }
                
                // Remover mensagem informativa existente
                container.next('.text-muted').remove();
                
                // Remover o input se existir
                container.find(`input#${prefixo}_cidade_texto`).remove();
                
                // Recriar o select
                container.empty();
                container.append(`
                    <select class="form-select cidade-select" id="${prefixo}_cidade" name="${prefixo}_cidade" required>
                        <option value="" selected disabled>Selecione uma cidade</option>
                    </select>
                `);
                
                // Atualizar a referência
                cidadeSelect = $(`#${prefixo}_cidade`);
            }
            
            // Função para exibir o input de cidades
            function exibirInputCidade(prefixo, mensagem) {
                const container = $(`#${prefixo}_cidade_container`);
                
                // Se já tem o input, não faz nada
                if (container.find(`input#${prefixo}_cidade_texto`).length > 0) {
                    return;
                }
                
                // Remover mensagem informativa existente
                container.next('.text-muted').remove();
                
                // Remover o select
                container.empty();
                
                // Criar o input
                container.append(`
                    <input type="text" class="form-control" id="${prefixo}_cidade_texto" 
                           name="${prefixo}_cidade" placeholder="Digite o nome da cidade" required>
                `);
                
                // Exibir uma mensagem informativa
                if (mensagem) {
                    container.after(`<small class="text-muted">${mensagem}</small>`);
                }
            }
        }
        
        // Eventos para os selects de país, estado e cidade
        $('.pais-select').on('change', function() {
            const paisId = $(this).val();
            const formGroup = $(this).closest('.card-body');
            const estadoSelect = formGroup.find('.estado-select');
            const cidadeSelect = formGroup.find('.cidade-select');
            const prefixo = $(this).attr('id').split('_')[0]; // Obter prefixo (origem ou destino)
            
            // Limpar campo de texto da cidade, se existir
            const cidadeContainer = $(`#${prefixo}_cidade_container`);
            if (cidadeContainer.length) {
                cidadeContainer.find('.text-muted').remove();
                cidadeContainer.next('.text-muted').remove();
            }
            
            carregarEstados(paisId, estadoSelect);
            
            // Limpar e desabilitar o campo de cidade, seja select ou input
            if ($(`#${prefixo}_cidade`).length) {
                $(`#${prefixo}_cidade`).prop('disabled', true);
                $(`#${prefixo}_cidade`).val('');
            }
            
            if ($(`#${prefixo}_cidade_texto`).length) {
                $(`#${prefixo}_cidade_texto`).val('');
                
                // Se voltou para o Brasil, remover o input e voltar para select
                if (paisId === 'BR') {
                    // A função carregarCidades vai cuidar de criar o select
                    const cidadeContainer = $(`#${prefixo}_cidade_container`);
                    cidadeContainer.empty();
                    cidadeContainer.append(`
                        <select class="form-select cidade-select" id="${prefixo}_cidade" name="${prefixo}_cidade" required disabled>
                            <option value="" selected disabled>Selecione um estado primeiro</option>
                        </select>
                    `);
                }
            }
        });
        
        $('.estado-select').on('change', function() {
            const estadoId = $(this).val();
            const formGroup = $(this).closest('.card-body');
            const cidadeSelect = formGroup.find('.cidade-select');
            
            // Limpar mensagens informativas anteriores
            const cidadeContainer = $(`#${prefixo}_cidade_container`);
            if (cidadeContainer.length) {
                cidadeContainer.next('.text-muted').remove();
            }
            
            carregarCidades(estadoId, cidadeSelect);
        });
        
        // Função para buscar CEP via API ViaCEP (Brasil)
        function buscarCEP(cep, prefixo) {
            if (cep.length < 8) {
                alert('CEP inválido. Por favor, digite um CEP válido com 8 dígitos.');
                return;
            }
            
            // Remove caracteres não numéricos
            cep = cep.replace(/\D/g, '');
            
            // Mostrar indicador de carregamento
            $(`#${prefixo}_endereco`).val('Buscando...');
            
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/?callback=?`, function(data) {
                if (!data.erro) {
                    // Preencher o endereço
                    $(`#${prefixo}_endereco`).val(data.logradouro + (data.complemento ? ', ' + data.complemento : '') + ' - ' + data.bairro);
                    
                    // Encontrar o país (Brasil)
                    const paisBrasil = paises.find(pais => pais.id === 'BR');
                    if (paisBrasil) {
                        // Selecionar Brasil como país
                        $(`#${prefixo}_pais`).val(paisBrasil.id).trigger('change');
                        
                        // Aguardar o carregamento dos estados
                        setTimeout(function() {
                            // Encontrar o estado pelo código UF
                            const estado = estados['BR'].find(estado => 
                                estado.id === data.uf
                            );
                            
                            if (estado) {
                                // Selecionar o estado
                                $(`#${prefixo}_estado`).val(estado.id).trigger('change');
                                
                                // Aguardar o carregamento das cidades
                                setTimeout(function() {
                                    preencherCidade(prefixo, data.localidade);
                                }, 800);
                            }
                        }, 300);
                    }
                } else {
                    alert('CEP não encontrado. Por favor, digite o endereço manualmente.');
                    $(`#${prefixo}_endereco`).val('');
                }
            }).fail(function(jqxhr, textStatus, error) {
                console.error("Erro ao buscar CEP:", error);
                alert('Erro ao buscar o CEP. Por favor, digite o endereço manualmente.');
                $(`#${prefixo}_endereco`).val('');
            });
            
            // Função auxiliar para preencher o campo de cidade, independente do tipo
            function preencherCidade(prefixo, nomeCidade) {
                // Verificar se existe um campo de seleção ou um campo de texto
                const selectCidade = $(`#${prefixo}_cidade`);
                const inputCidade = $(`#${prefixo}_cidade_texto`);
                
                // Se temos um campo de entrada de texto, é simples
                if (inputCidade.length > 0) {
                    inputCidade.val(nomeCidade);
                    return;
                }
                
                // Se temos um select, precisamos verificar se a cidade está na lista
                if (selectCidade.length > 0) {
                    let cidadeEncontrada = false;
                    
                    // Verificar se o select já tem opções carregadas
                    if (selectCidade.find('option').length > 1) {
                        // Tentar encontrar a cidade pelo nome
                        selectCidade.find('option').each(function() {
                            if ($(this).text().toLowerCase() === nomeCidade.toLowerCase()) {
                                selectCidade.val($(this).val()).change();
                                cidadeEncontrada = true;
                                return false; // Break
                            }
                        });
                        
                        // Se não encontrou, adicionar a cidade como opção
                        if (!cidadeEncontrada) {
                            const novaOpcao = $('<option>', {
                                value: 'custom_' + nomeCidade.replace(/\s/g, '_').toLowerCase(),
                                text: nomeCidade
                            });
                            
                            selectCidade.append(novaOpcao);
                            selectCidade.val(novaOpcao.val()).change();
                        }
                    } else {
                        // O select ainda não tem opções, esperar mais ou transformar em input
                        console.log('Select de cidade ainda não carregou, esperando...');
                        
                        // Verificar novamente após um curto período
                        setTimeout(function() {
                            // Se ainda não carregou, tentar uma última vez
                            if (selectCidade.find('option').length <= 1) {
                                // Verificar se o container existe
                                const container = $(`#${prefixo}_cidade_container`);
                                if (container.length > 0) {
                                    // Transformar em input
                                    container.empty().html(`
                                        <input type="text" class="form-control" id="${prefixo}_cidade_texto" 
                                               name="${prefixo}_cidade" value="${nomeCidade}" required>
                                    `);
                                    container.after(`<small class="text-muted">Campo convertido para entrada de texto.</small>`);
                                }
                            } else {
                                // Tentamos novamente com o select agora preenchido
                                preencherCidade(prefixo, nomeCidade);
                            }
                        }, 500);
                    }
                }
            }
        }
        
        // Eventos para buscar endereço pelo CEP
        $('#origem_buscar_cep').on('click', function() {
            const cep = $('#origem_cep').val();
            buscarCEP(cep, 'origem');
        });
        
        $('#destino_buscar_cep').on('click', function() {
            const cep = $('#destino_cep').val();
            buscarCEP(cep, 'destino');
        });
        
        // Máscara para CEP
        $('#origem_cep, #destino_cep').on('input', function() {
            const value = $(this).val().replace(/\D/g, '');
            if (value.length <= 5) {
                $(this).val(value);
            } else {
                $(this).val(value.substring(0, 5) + '-' + value.substring(5, 8));
            }
        });
        
        // Inicializar os selects de países
        carregarPaises();
        
        // Evento de submissão do formulário
        $('#envio-form').on('submit', function(e) {
            e.preventDefault();
            
            // Validar se há produtos
            if (produtos.length === 0) {
                showAlert('Por favor, adicione pelo menos um produto para o envio.', 'warning');
                return false;
            }
            
            // Validar se há caixas
            if (caixas.length === 0) {
                showAlert('Por favor, adicione pelo menos uma caixa para o envio.', 'warning');
                return false;
            }
            
            // Verificar se os campos ocultos de dimensões estão preenchidos
            const altura = $('#altura-hidden').val();
            const largura = $('#largura-hidden').val();
            const comprimento = $('#comprimento-hidden').val();
            const pesoCaixa = $('#peso-caixa-hidden').val();
            
            if (!altura || !largura || !comprimento || !pesoCaixa) {
                console.error('Campos de dimensão não preenchidos:', {
                    altura: altura,
                    largura: largura,
                    comprimento: comprimento,
                    pesoCaixa: pesoCaixa
                });
                
                // Tentar preencher com os valores da primeira caixa
                if (caixas.length > 0) {
                    $('#altura-hidden').val(caixas[0].altura);
                    $('#largura-hidden').val(caixas[0].largura);
                    $('#comprimento-hidden').val(caixas[0].comprimento);
                    $('#peso-caixa-hidden').val(caixas[0].peso);
                    
                    console.log('Dimensões corrigidas com a primeira caixa:', {
                        altura: caixas[0].altura,
                        largura: caixas[0].largura,
                        comprimento: caixas[0].comprimento,
                        pesoCaixa: caixas[0].peso
                    });
                } else {
                    showAlert('Erro ao processar dimensões da caixa. Por favor, tente novamente.', 'danger');
                    return false;
                }
            }
            
            // Verificar o método de entrega
            if (!$('#servico_entrega').val()) {
                showAlert('Por favor, selecione um método de entrega.', 'warning');
                return false;
            }
            
            // Log para debug
            console.log('Enviando dados:', {
                produtos: JSON.parse($('#produtos-json').val() || '[]'),
                caixas: JSON.parse($('#caixas-json').val() || '[]'),
                valorTotal: $('#valor-total-input').val(),
                pesoTotal: $('#peso-total-input').val(),
                dimensoes: {
                    altura: $('#altura-hidden').val(),
                    largura: $('#largura-hidden').val(),
                    comprimento: $('#comprimento-hidden').val(),
                    pesoCaixa: $('#peso-caixa-hidden').val()
                },
                servicoEntrega: $('#servico_entrega').val()
            });
            
            // Se passou pela validação, enviar o formulário via AJAX
            $.ajax({
                url: "{{ route('api.envio.processar') }}",
                method: 'POST',
                data: function() {
                    // Atualizar produtos para garantir que tenham valor_unitario
                    try {
                        const produtosString = $('#produtos-json').val();
                        if (produtosString) {
                            const produtosObj = JSON.parse(produtosString);
                            // Adicionar valor_unitario onde falta
                            produtosObj.forEach(function(produto) {
                                if (!produto.valor_unitario && produto.valor) {
                                    produto.valor_unitario = produto.valor;
                                }
                            });
                            // Atualizar o campo
                            $('#produtos-json').val(JSON.stringify(produtosObj));
                        }
                    } catch (e) {
                        console.error('Erro ao processar produtos:', e);
                    }
                    
                    // Garantir que as dimensões e peso da caixa sejam definidos corretamente
                    try {
                        // Se temos caixas, use a primeira para as dimensões principais
                        const caixasString = $('#caixas-json').val();
                        if (caixasString) {
                            const caixasObj = JSON.parse(caixasString);
                            if (caixasObj && caixasObj.length > 0) {
                                // Use as dimensões da primeira caixa
                                const primeiraCaixa = caixasObj[0];
                                $('#altura-hidden').val(primeiraCaixa.altura);
                                $('#largura-hidden').val(primeiraCaixa.largura);
                                $('#comprimento-hidden').val(primeiraCaixa.comprimento);
                                $('#peso-caixa-hidden').val(primeiraCaixa.peso);
                                
                                console.log('Dimensões atualizadas:', {
                                    altura: primeiraCaixa.altura,
                                    largura: primeiraCaixa.largura,
                                    comprimento: primeiraCaixa.comprimento,
                                    peso: primeiraCaixa.peso
                                });
                            }
                        }
                    } catch (e) {
                        console.error('Erro ao processar dimensões da caixa:', e);
                    }
                    
                    // Retornar os dados serializados do form
                    return $('#envio-form').serialize();
                }(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Mostrar indicador de carregamento
                    $('#submit-button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
                },
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    showAlert('Envio registrado com sucesso!', 'success');
                    setTimeout(function() {
                        loadSection('etiqueta'); // Mudado para etiqueta em vez de pagamento
                    }, 1500);
                },
                error: function(xhr) {
                    console.error('Erro ao processar envio:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Status Text:', xhr.statusText);
                    console.error('Response Text:', xhr.responseText);
                    
                    let errorMsg = '<strong>Erro ao processar o envio.</strong><br>';
                    
                    // Se tivermos mensagens de validação, vamos exibi-las
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg += '<div class="mt-2"><strong>Erros de validação:</strong><ul>';
                        $.each(xhr.responseJSON.errors, function(campo, erros) {
                            errorMsg += '<li>' + campo + ': ' + erros[0] + '</li>';
                        });
                        errorMsg += '</ul></div>';
                    }
                    
                    // Se houver mensagem específica
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += '<div class="mt-2"><strong>Mensagem da API:</strong><br>' + xhr.responseJSON.message + '</div>';
                    }
                    
                    // Exibir resposta completa da API
                    errorMsg += '<div class="mt-3"><strong>Resposta completa da API:</strong><br>';
                    errorMsg += '<pre class="bg-light p-2 mt-2 small" style="max-height: 200px; overflow-y: auto;">' + 
                                JSON.stringify(xhr.responseJSON || JSON.parse(xhr.responseText || '{}'), null, 2) + 
                                '</pre></div>';
                    
                    showAlert(errorMsg, 'danger');
                },
                complete: function() {
                    // Restaurar botão de envio
                    $('#submit-button').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Processar Envio');
                }
            });
        });

        // Inicializar campos de telefone com máscara
        $('#origem_telefone, #destino_telefone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if ($(this).attr('id') === 'origem_telefone') {
                // Formato brasileiro: +55 11 98765-4321
                if (value.length <= 2) {
                    $(this).val('+' + value);
                } else if (value.length <= 4) {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2));
                } else if (value.length <= 8) {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2, 4) + ' ' + value.substring(4));
                } else {
                    $(this).val('+' + value.substring(0, 2) + ' ' + value.substring(2, 4) + ' ' + 
                        value.substring(4, 9) + '-' + value.substring(9, 13));
                }
            } else {
                // Formato americano: +1 555 123-4567
                if (value.length <= 1) {
                    $(this).val('+' + value);
                } else if (value.length <= 4) {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1));
                } else if (value.length <= 7) {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1, 4) + ' ' + value.substring(4));
                } else {
                    $(this).val('+' + value.substring(0, 1) + ' ' + value.substring(1, 4) + ' ' + 
                        value.substring(4, 7) + '-' + value.substring(7, 11));
                }
            }
        });
    });
</script> 