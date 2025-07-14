@extends('layouts.app')

@section('content')
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
                    <p class="text-muted">
                        Cliente desde {{ isset($usuario['data_cadastro']) ? $usuario['data_cadastro'] : 'Out/2023' }}
                    </p>
                    <div id="message-area" class="my-3"></div>
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
                                    <th>Serviço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($shipments) > 0)
                                    @foreach($shipments as $shipment)
                                        <tr>
                                            <td>{{ $shipment->tracking_number ?? 'N/A' }}</td>
                                            <td>{{ $shipment->created_at ? $shipment->created_at->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $shipment->service_name ?? $shipment->carrier }}</td>
                                            <td>
                                                @php
                                                    $statusClass = 'secondary';
                                                    
                                                    if($shipment->status === 'created') $statusClass = 'primary';
                                                    elseif($shipment->status === 'in_transit') $statusClass = 'info';
                                                    elseif($shipment->status === 'delivered') $statusClass = 'success';
                                                    elseif($shipment->status === 'exception') $statusClass = 'warning';
                                                    elseif($shipment->status === 'cancelled') $statusClass = 'danger';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ $shipment->status_description ?? ucfirst(str_replace('_', ' ', $shipment->status ?? 'pending')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary section-link" data-section="rastreamento" data-tracking="{{ $shipment->tracking_number }}">Rastrear</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum envio encontrado.</td>
                                    </tr>
                                @endif
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
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ $usuario['nome'] }}" required placeholder="Ex: João da Silva">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $usuario['email'] }}" required placeholder="Ex: seu.email@exemplo.com">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" value="{{ $usuario['cpf'] }}" required placeholder="Ex: 123.456.789-00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="{{ $usuario['telefone'] }}" required placeholder="Ex: (11) 98765-4321">
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
                                <input type="text" class="form-control" id="rua" name="rua" value="{{ $usuario['rua'] }}" required placeholder="Ex: Av. Paulista">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="{{ $usuario['numero'] }}" required placeholder="Ex: 1000">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="{{ $usuario['complemento'] }}" placeholder="Ex: Apto 101, Bloco B">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="{{ $usuario['cidade'] }}" required placeholder="Ex: São Paulo">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" class="form-control" id="estado" name="estado" value="{{ $usuario['estado'] }}" required placeholder="Ex: SP">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" class="form-control" id="cep" name="cep" value="{{ $usuario['cep'] }}" required placeholder="Ex: 01310-100">
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
            // Limpar possíveis mensagens de erro
            limparMensagens();
            
            // Resetar o formulário para os valores originais
            $('#perfil-form')[0].reset();
            
            // Recarregar os valores originais nos campos
            $('#nome').val('{{ $usuario['nome'] }}');
            $('#email').val('{{ $usuario['email'] }}');
            $('#cpf').val('{{ $usuario['cpf'] }}');
            $('#telefone').val('{{ $usuario['telefone'] }}');
            $('#rua').val('{{ $usuario['rua'] }}');
            $('#numero').val('{{ $usuario['numero'] }}');
            $('#complemento').val('{{ $usuario['complemento'] }}');
            $('#cidade').val('{{ $usuario['cidade'] }}');
            $('#estado').val('{{ $usuario['estado'] }}');
            $('#cep').val('{{ $usuario['cep'] }}');
            
            $('#perfil-edicao').hide();
            $('#perfil-visualizacao').show();
        });
        
        // Função para limpar mensagens de erro/sucesso
        function limparMensagens() {
            $('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }
        
        // Processar o formulário de edição via AJAX
        $('#perfil-form').on('submit', function(e) {
            e.preventDefault();
            
            // Mostrar indicador de carregamento
            showLoader();
            
            // Enviar o formulário via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    hideLoader();
                    
                    if (response.success) {
                        // Atualizar dados de visualização com os valores retornados
                        $('.perfil-nome').text(response.usuario.nome);
                        $('.perfil-email').text(response.usuario.email);
                        $('.perfil-cpf').text(response.usuario.cpf);
                        $('.perfil-telefone').text(response.usuario.telefone);
                        $('.perfil-rua').text(response.usuario.rua);
                        $('.perfil-numero').text(response.usuario.numero);
                        $('.perfil-complemento').text(response.usuario.complemento);
                        $('.perfil-cidade').text(response.usuario.cidade);
                        $('.perfil-estado').text(response.usuario.estado);
                        $('.perfil-cep').text(response.usuario.cep);
                        
                        // Esconder formulário e mostrar visualização
                        $('#perfil-edicao').hide();
                        $('#perfil-visualizacao').show();
                        
                        // Adicionar mensagem de sucesso diretamente abaixo do título do perfil
                        const successMessage = `
                            <div id="success-message" class="alert alert-success mb-4">
                                <i class="fas fa-check-circle me-2"></i> ${response.message || 'Perfil atualizado com sucesso!'}
                            </div>
                        `;
                        
                        // Inserir na área dedicada para mensagens
                        $('#message-area').html(successMessage);
                        
                        // Configurar um timeout para remover a mensagem de sucesso após 5 segundos
                        setTimeout(function() {
                            $('#success-message').fadeOut(500, function() {
                                $(this).remove();
                            });
                        }, 5000);
                        
                        // Mostrar mensagem de sucesso no topo também
                        showAlert('success', response.message || 'Perfil atualizado com sucesso!');
                    } else {
                        // Mostrar mensagem de erro
                        showAlert('danger', response.message || 'Erro ao atualizar o perfil. Por favor, tente novamente.');
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    
                    let errorMessage = 'Erro ao atualizar o perfil. Por favor, tente novamente.';
                    
                    // Tentar extrair mensagem de erro da resposta
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    showAlert('danger', errorMessage);
                }
            });
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
            // Remover completamente o loader em vez de apenas ocultá-lo
            $('#global-loader').remove();
        }
        
        function showAlert(type, message) {
            // Criar o elemento de alerta
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Adicionar no topo da card principal do perfil para maior visibilidade
            $('.card:first').prepend(alertHtml);
            
            // Fazer scroll para o topo para garantir que o usuário veja a mensagem
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            
            // Definir timeout para remover o alerta de forma segura
            setTimeout(function() {
                // Verificar se o alerta ainda existe antes de tentar fechá-lo
                const $alerts = $('.alert');
                if ($alerts.length > 0) {
                    // Remover suavemente
                    $alerts.fadeOut(500, function() {
                        $(this).remove();
                    });
                }
            }, 5000);
        }
        
        // Aplicar máscaras aos campos
        // Nota: Isso requer que o jQuery Mask Plugin esteja carregado
        // Se não estiver disponível, carregá-lo dinamicamente
        if (typeof $.fn.mask !== 'function') {
            // Carregar o script jQuery Mask Plugin dinamicamente
            $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', function() {
                aplicarMascaras();
            });
        } else {
            aplicarMascaras();
        }
        
        function aplicarMascaras() {
            $('#cpf').mask('000.000.000-00', {reverse: true});
            $('#telefone').mask('(00) 00000-0000');
            $('#cep').mask('00000-000');
        }
    });
</script> 

@section('scripts')
<script>
    $(document).ready(function() {
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="perfil"]').addClass('active');
        $('#content-container').show();
    });
</script>
@endsection

@endsection 