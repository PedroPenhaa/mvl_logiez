<div class="card">
    <div class="card-header">
        <i class="fas fa-user me-2"></i> Meu Perfil
    </div>
    <div class="card-body">
        <!-- Visualização do Perfil -->
        <div id="perfil-visualizacao">
            <div class="row mb-4">
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <div class="mx-auto" style="width: 150px; height: 150px; background-color: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-5x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="perfil-nome">{{ $usuario['nome'] }}</h4>
                    <p class="text-muted">Cliente desde Out/2023</p>
                    <button id="editar-perfil-btn" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Editar Perfil
                    </button>
                </div>
                
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>Informações Pessoais</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>Nome Completo:</strong></div>
                                <div class="col-md-8 perfil-nome">{{ $usuario['nome'] }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>Email:</strong></div>
                                <div class="col-md-8 perfil-email">{{ $usuario['email'] }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>CPF:</strong></div>
                                <div class="col-md-8 perfil-cpf">{{ $usuario['cpf'] }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Telefone:</strong></div>
                                <div class="col-md-8 perfil-telefone">{{ $usuario['telefone'] }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-light">
                            <strong>Endereço</strong>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>Endereço:</strong></div>
                                <div class="col-md-8">
                                    <span class="perfil-rua">{{ $usuario['rua'] }}</span>, 
                                    <span class="perfil-numero">{{ $usuario['numero'] }}</span>
                                    @if($usuario['complemento'])
                                        - <span class="perfil-complemento">{{ $usuario['complemento'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4"><strong>Cidade/Estado:</strong></div>
                                <div class="col-md-8">
                                    <span class="perfil-cidade">{{ $usuario['cidade'] }}</span> - 
                                    <span class="perfil-estado">{{ $usuario['estado'] }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>CEP:</strong></div>
                                <div class="col-md-8 perfil-cep">{{ $usuario['cep'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Histórico de Envios</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Data</th>
                                    <th>Destino</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DHL123456789</td>
                                    <td>15/10/2023</td>
                                    <td>Miami, EUA</td>
                                    <td><span class="badge bg-success">Entregue</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary section-link" data-section="rastreamento">Rastrear</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DHL987654321</td>
                                    <td>05/10/2023</td>
                                    <td>Londres, Reino Unido</td>
                                    <td><span class="badge bg-primary">Em Trânsito</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary section-link" data-section="rastreamento">Rastrear</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DHL456789123</td>
                                    <td>25/09/2023</td>
                                    <td>Tóquio, Japão</td>
                                    <td><span class="badge bg-success">Entregue</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary section-link" data-section="rastreamento">Rastrear</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulário de Edição do Perfil -->
        <div id="perfil-edicao" style="display: none;">
            <h4 class="mb-4">Editar Perfil</h4>
            
            <form id="perfil-form" action="{{ route('api.perfil.atualizar') }}" method="POST">
                @csrf
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Informações Pessoais</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ $usuario['nome'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $usuario['email'] }}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="{{ $usuario['cpf'] }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="{{ $usuario['telefone'] }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Endereço</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="rua" class="form-label">Rua</label>
                                <input type="text" class="form-control" id="rua" name="rua" value="{{ $usuario['rua'] }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="{{ $usuario['numero'] }}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="{{ $usuario['complemento'] }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="{{ $usuario['cidade'] }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" class="form-control" id="estado" name="estado" value="{{ $usuario['estado'] }}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="{{ $usuario['cep'] }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <button type="button" id="cancelar-edicao-btn" class="btn btn-outline-secondary ms-2">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Adicionar evento aos botões de link de seção
        $('.section-link').on('click', function() {
            const section = $(this).data('section');
            $('.menu-item[data-section="' + section + '"]').click();
        });
        
        // Alternar entre visualização e edição do perfil
        $('#editar-perfil-btn').on('click', function() {
            $('#perfil-visualizacao').hide();
            $('#perfil-edicao').show();
        });
        
        $('#cancelar-edicao-btn').on('click', function() {
            $('#perfil-edicao').hide();
            $('#perfil-visualizacao').show();
        });
        
        // Processar o formulário de edição via AJAX
        $('#perfil-form').on('submit', function(e) {
            e.preventDefault();
            
            // Mostrar indicador de carregamento
            showLoader();
            
            // Simular envio do formulário (substituir por AJAX real em produção)
            setTimeout(function() {
                // Atualizar dados de visualização com os valores do formulário
                $('.perfil-nome').text($('#nome').val());
                $('.perfil-email').text($('#email').val());
                $('.perfil-cpf').text($('#cpf').val());
                $('.perfil-telefone').text($('#telefone').val());
                $('.perfil-rua').text($('#rua').val());
                $('.perfil-numero').text($('#numero').val());
                $('.perfil-complemento').text($('#complemento').val());
                $('.perfil-cidade').text($('#cidade').val());
                $('.perfil-estado').text($('#estado').val());
                $('.perfil-cep').text($('#cep').val());
                
                // Esconder formulário e mostrar visualização
                $('#perfil-edicao').hide();
                $('#perfil-visualizacao').show();
                
                // Esconder loader e mostrar mensagem de sucesso
                hideLoader();
                showAlert('success', 'Perfil atualizado com sucesso!');
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