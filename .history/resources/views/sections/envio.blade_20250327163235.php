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
                                <label for="origem_cep" class="form-label">CEP / Código Postal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="origem_cep" name="origem_cep" required>
                                    <button class="btn btn-outline-secondary" type="button" id="origem_buscar_cep">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                                <small class="text-muted">Digite o CEP e clique em buscar para preencher o endereço</small>
                            </div>
                            <div class="mb-3">
                                <label for="origem_endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="origem_endereco" name="origem_endereco" required>
                            </div>
                            <div class="mb-3">
                                <label for="origem_pais" class="form-label">País</label>
                                <select class="form-select pais-select" id="origem_pais" name="origem_pais" required>
                                    <option value="" selected disabled>Selecione um país</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="origem_estado" class="form-label">Estado</label>
                                <select class="form-select estado-select" id="origem_estado" name="origem_estado" required disabled>
                                    <option value="" selected disabled>Selecione um país primeiro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="origem_cidade" class="form-label">Cidade</label>
                                <select class="form-select cidade-select" id="origem_cidade" name="origem_cidade" required disabled>
                                    <option value="" selected disabled>Selecione um estado primeiro</option>
                                </select>
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
                                <label for="destino_cep" class="form-label">CEP / Código Postal</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="destino_cep" name="destino_cep" required>
                                    <button class="btn btn-outline-secondary" type="button" id="destino_buscar_cep">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </div>
                                <small class="text-muted">Digite o CEP e clique em buscar para preencher o endereço</small>
                            </div>
                            <div class="mb-3">
                                <label for="destino_endereco" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="destino_endereco" name="destino_endereco" required>
                            </div>
                            <div class="mb-3">
                                <label for="destino_pais" class="form-label">País</label>
                                <select class="form-select pais-select" id="destino_pais" name="destino_pais" required>
                                    <option value="" selected disabled>Selecione um país</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="destino_estado" class="form-label">Estado</label>
                                <select class="form-select estado-select" id="destino_estado" name="destino_estado" required disabled>
                                    <option value="" selected disabled>Selecione um país primeiro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="destino_cidade" class="form-label">Cidade</label>
                                <select class="form-select cidade-select" id="destino_cidade" name="destino_cidade" required disabled>
                                    <option value="" selected disabled>Selecione um estado primeiro</option>
                                </select>
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

<!-- Adicionar o script do Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        console.log("Documento pronto, iniciando script");
        
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
        
        // Função para inicializar o Select2
        function inicializarSelect2() {
            console.log("Destruindo instância anterior de Select2 caso exista");
            // Destruir instância anterior caso exista
            if ($('#produto-select').hasClass('select2-hidden-accessible')) {
                $('#produto-select').select2('destroy');
            }
            
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
                ajax: {
                    url: '{{ route("api.produtos.get") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        // Obter os valores dos campos de busca
                        const buscaDescricao = $('#busca-descricao').val();
                        const buscaNCM = $('#busca-codigo').val();
                        
                        // Criar o objeto de busca
                        const searchParams = {};
                        
                        // Usar a busca das caixas de texto se estiverem preenchidas
                        if (buscaDescricao) searchParams.descricao = buscaDescricao;
                        if (buscaNCM) searchParams.codigo = buscaNCM;
                        
                        // Se não há busca nas caixas, usar o termo do dropdown
                        if (!buscaDescricao && !buscaNCM && params.term) {
                            searchParams.descricao = params.term;
                        }
                        
                        console.log("Enviando requisição com parâmetros:", { 
                            termo: searchParams, 
                            page: params.page 
                        });
                        
                        return {
                            search: JSON.stringify(searchParams),
                            page: params.page || 1,
                            limit: 100
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        console.log("Dados recebidos:", data);
                        
                        // Verificar se os dados recebidos são válidos
                        if (!data || !data.produtos || !Array.isArray(data.produtos)) {
                            console.error("Dados inválidos recebidos da API:", data);
                            return { results: [] };
                        }
                        
                        // Formatar os resultados para o formato esperado pelo Select2
                        const results = data.produtos.map(function(produto) {
                            return {
                                id: produto.codigo,
                                text: produto.descricao,
                                codigo: produto.codigo, // mantém o nome da propriedade para compatibilidade
                                peso: 0.5, // Valores padrão
                                valor: 10.00
                            };
                        });
                        
                        console.log("Resultados processados:", results.length);
                        
                        return {
                            results: results,
                            pagination: {
                                more: params.page < data.totalPages
                            }
                        };
                    },
                    error: function(error) {
                        console.error("Erro na requisição AJAX:", error);
                    },
                    cache: true
                },
                minimumInputLength: 0 // Permitir carregar todos os produtos sem digitar
            });
            
            // Eventos adicionais
            $('#produto-select').on('select2:open', function() {
                console.log("Select2 aberto");
                
                // Forçar a primeira consulta ao abrir
                setTimeout(function() {
                    var searchField = $('.select2-search__field');
                    if (searchField.length) {
                        searchField.val('');
                        searchField.trigger('input');
                        console.log("Campo de busca ativado automaticamente");
                    }
                }, 100);
            });
            
            // Evento ao selecionar um produto
            $('#produto-select').on('select2:select', function(e) {
                const produtoSelecionado = e.params.data;
                console.log("Produto selecionado:", produtoSelecionado);
                
                // Sugerir valor inicial (pode ser editado pelo usuário)
                const valorSugerido = produtoSelecionado.valor || 10.00;
                $('#produto-valor').val(valorSugerido.toFixed(2));
                $('#produto-valor').select(); // Seleciona o texto para fácil edição
            });
            
            // Carregar manualmente a primeira página de resultados
            realizarBusca();
        }
        
        // Função para realizar a busca baseada nos campos de busca
        function realizarBusca() {
            $('#select-status').text('Buscando produtos...');
            
            const buscaDescricao = $('#busca-descricao').val();
            const buscaNCM = $('#busca-codigo').val();
            
            // Criar o objeto de busca
            const searchParams = {};
            if (buscaDescricao) searchParams.descricao = buscaDescricao;
            if (buscaNCM) searchParams.codigo = buscaNCM;
            
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
                    $('#select-status').text('');
                    
                    // Limpar opções existentes, preservando a opção padrão
                    const defaultOption = $('#produto-select option[value=""]').clone();
                    $('#produto-select').empty().append(defaultOption);
                    
                    if (data && data.produtos && data.produtos.length) {
                        var options = data.produtos.map(function(produto) {
                            return new Option(produto.descricao, produto.codigo, false, false);
                        });
                        
                        $('#produto-select').append(options).trigger('change');
                        console.log("Opções carregadas:", options.length);
                        $('#select-status').text(options.length + ' produtos encontrados');
                        
                        // Esconder o botão de reload, pois os produtos foram carregados com sucesso
                        $('#reload-produtos').hide();
                    } else {
                        $('#select-status').text('Nenhum produto encontrado');
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
            timer = setTimeout(realizarBusca, 500); // Debounce de 500ms
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
            
            produtos.forEach(function(produto) {
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
            
            // Verifica se é um estado brasileiro
            const isBrasil = $('.pais-select').val() === 'BR';
            
            if (isBrasil) {
                // Para estados brasileiros, buscar as cidades da API do IBGE
                $.ajax({
                    url: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${estadoId}/municipios`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
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
                        
                        console.log(`Carregadas ${data.length} cidades para o estado ${estadoId}`);
                    },
                    error: function(error) {
                        console.error('Erro ao carregar cidades:', error);
                        cidadeSelect.find('option:first').text('Erro ao carregar cidades');
                    }
                });
            } else {
                // Para outros países, usar a lista estática (se disponível)
                const estadoCidades = cidades[estadoId] || [];
                cidadeSelect.find('option:not(:first)').remove();
                
                if (estadoCidades.length === 0) {
                    cidadeSelect.find('option:first').text('Nenhuma cidade disponível para este estado');
                } else {
                    cidadeSelect.find('option:first').text('Selecione uma cidade');
                    
                    estadoCidades.forEach(function(cidade) {
                        cidadeSelect.append($('<option>', {
                            value: cidade.id,
                            text: cidade.nome
                        }));
                    });
                    
                    cidadeSelect.prop('disabled', false);
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
            
            carregarEstados(paisId, estadoSelect);
            cidadeSelect.prop('disabled', true);
            cidadeSelect.find('option:first').text('Selecione um estado primeiro');
            cidadeSelect.val('');
        });
        
        $('.estado-select').on('change', function() {
            const estadoId = $(this).val();
            const formGroup = $(this).closest('.card-body');
            const cidadeSelect = formGroup.find('.cidade-select');
            
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
                    $(`#${prefixo}_endereco`).val(data.logradouro + (data.complemento ? ', ' + data.complemento : '') + ' - ' + data.bairro);
                    
                    // Encontrar o país (Brasil)
                    const paisBrasil = paises.find(pais => pais.id === 'BR');
                    if (paisBrasil) {
                        $(`#${prefixo}_pais`).val(paisBrasil.id).trigger('change');
                        
                        // Aguardar o carregamento dos estados
                        setTimeout(function() {
                            // Encontrar o estado pelo nome ou sigla
                            const estado = estados['BR'].find(estado => 
                                estado.id === data.uf || 
                                estado.nome.toLowerCase() === data.uf.toLowerCase()
                            );
                            
                            if (estado) {
                                $(`#${prefixo}_estado`).val(estado.id).trigger('change');
                                
                                // Aguardar o carregamento das cidades da API do IBGE
                                setTimeout(function() {
                                    // Função para encontrar e selecionar a cidade
                                    function selecionarCidade() {
                                        // Verificar se as cidades já foram carregadas
                                        if ($(`#${prefixo}_cidade option`).length > 1) {
                                            // Tentar encontrar a cidade pelo nome
                                            let cidadeEncontrada = false;
                                            
                                            $(`#${prefixo}_cidade option`).each(function() {
                                                if ($(this).text().toLowerCase() === data.localidade.toLowerCase()) {
                                                    $(`#${prefixo}_cidade`).val($(this).val());
                                                    cidadeEncontrada = true;
                                                    return false; // Break the loop
                                                }
                                            });
                                            
                                            if (!cidadeEncontrada) {
                                                // Se não encontrou, verificar se o placeholder foi atualizado para tentar novamente
                                                if ($(`#${prefixo}_cidade option:first`).text() === 'Selecione uma cidade') {
                                                    console.log('Cidade não encontrada: ' + data.localidade);
                                                    // Adicionar a cidade manualmente como opção
                                                    $(`#${prefixo}_cidade`).append($('<option>', {
                                                        value: 'custom_' + data.localidade.replace(/\s/g, '_').toLowerCase(),
                                                        text: data.localidade
                                                    }));
                                                    
                                                    // Selecionar a cidade adicionada
                                                    $(`#${prefixo}_cidade option:last`).prop('selected', true);
                                                } else {
                                                    // Se o placeholder ainda não foi atualizado, esperar um pouco mais
                                                    setTimeout(selecionarCidade, 300);
                                                }
                                            }
                                        } else {
                                            // As cidades ainda não foram carregadas, esperar mais
                                            setTimeout(selecionarCidade, 300);
                                        }
                                    }
                                    
                                    // Iniciar a verificação
                                    selecionarCidade();
                                }, 500);
                            }
                        }, 300);
                    }
                } else {
                    alert('CEP não encontrado. Por favor, digite o endereço manualmente.');
                    $(`#${prefixo}_endereco`).val('');
                }
            }).fail(function() {
                alert('Erro ao buscar o CEP. Por favor, digite o endereço manualmente.');
                $(`#${prefixo}_endereco`).val('');
            });
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
        
        // Processar o envio do formulário
        $('#envio-form').on('submit', function(e) {
            e.preventDefault();
            
            // Verificar se há produtos adicionados
            if (produtos.length === 0) {
                showAlert('Adicione pelo menos um produto para envio.', 'warning');
                return false;
            }
            
            // Verificar se há caixas adicionadas
            if (caixas.length === 0) {
                showAlert('Adicione pelo menos uma caixa para envio.', 'warning');
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