@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <h4 class="alert-heading">Sucesso!</h4>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('user_data'))
    <div class="alert alert-primary alert-dismissible fade show mb-4" role="alert">
        <h4 class="alert-heading">Informações do Usuário Importadas do Google!</h4>
        <p><strong>Email:</strong> {{ session('user_data')['email'] }}</p>
        <p><strong>Nome:</strong> {{ session('user_data')['name'] }}</p>
        <p><strong>CPF:</strong> {{ session('user_data')['cpf'] }}</p>
        <p><strong>Telefone:</strong> {{ session('user_data')['phone'] ?? 'Não informado' }}</p>
        <hr>
        <p class="mb-0">Seus dados foram importados com sucesso de sua conta Google e complementados com as informações adicionais fornecidas.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    <!-- Script direto para garantir que o alert seja exibido -->
    <script>
        // Mostrar alerta imediatamente
        alert("DADOS DO USUÁRIO IMPORTADOS DO GOOGLE:\n\nEmail: {{ session('user_data')['email'] }}\nNome: {{ session('user_data')['name'] }}\nCPF: {{ session('user_data')['cpf'] }}\nTelefone: {{ session('user_data')['phone'] ?? 'Não informado' }}");
    </script>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações Completas do Usuário</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações Pessoais</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nome:</th>
                            <td>{{ session('user_data')['name'] }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ session('user_data')['email'] }}</td>
                        </tr>
                        <tr>
                            <th>CPF:</th>
                            <td>{{ session('user_data')['cpf'] }}</td>
                        </tr>
                        <tr>
                            <th>Telefone:</th>
                            <td>{{ session('user_data')['phone'] ?? 'Não informado' }}</td>
                        </tr>
                        @if(session('user_data')['birth_date'])
                        <tr>
                            <th>Data de Nascimento:</th>
                            <td>{{ \Carbon\Carbon::parse(session('user_data')['birth_date'])->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        @if(session('user_data')['address'])
                        <tr>
                            <th>Endereço:</th>
                            <td>{{ session('user_data')['address'] }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Autenticação</h6>
                    <div class="alert alert-info">
                        <p><i class="fab fa-google me-2"></i> Você está conectado com sua conta do Google</p>
                        <p class="mb-0"><small>ID: {{ session('user_data')['id'] }}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {!! $dashboardContent ?? view('sections.dashboard')->render() !!}
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log("Dashboard inicializado diretamente!");
        
        // Forçar marcar dashboard como ativo no menu
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="dashboard"]').addClass('active');
        
        // Mostrar conteúdo diretamente
        $('#content-container').show();
        
        // Inicializar eventos para botões de navegação
        $('.nav-section').on('click', function() {
            const section = $(this).data('section');
            $('.menu-item').removeClass('active');
            $('.menu-item[data-section="' + section + '"]').addClass('active');
            loadSection(section);
        });
        
        // Verificar se existem dados do usuário na página
        if (document.querySelector('.alert-primary')) {
            setTimeout(function() {
                // Extrair informações do usuário da página
                const name = document.querySelector('.table-bordered tr:nth-child(1) td').textContent.trim();
                const email = document.querySelector('.table-bordered tr:nth-child(2) td').textContent.trim();
                const cpf = document.querySelector('.table-bordered tr:nth-child(3) td').textContent.trim();
                const phone = document.querySelector('.table-bordered tr:nth-child(4) td').textContent.trim();
                
                const alertMessage = 
                    "DADOS DO USUÁRIO IMPORTADOS COM SUCESSO!\n\n" +
                    "Email: " + email + "\n" +
                    "Nome: " + name + "\n" +
                    "CPF: " + cpf + "\n" +
                    "Telefone: " + phone;
                    
                alert(alertMessage);
            }, 800);
        }
    });
</script>
@endsection
