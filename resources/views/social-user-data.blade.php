<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Informações do Usuário - Logiez</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #63499E;
            --primary-light: #8a6cc9;
            --primary-dark: #4e3978;
            --secondary-color: #f8f9fa;
            --text-light: #ffffff;
            --text-dark: #333333;
            --border-radius: 8px;
            --box-shadow: 0 8px 24px rgba(99, 73, 158, 0.15);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #f5f7fc;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }
        
        .user-data-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .user-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 1.5rem;
            object-fit: cover;
            border: 3px solid var(--primary-light);
        }
        
        .user-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin: 0;
        }
        
        .user-email {
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .data-card {
            background-color: #f9fafc;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        
        .data-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .data-item {
            display: flex;
            margin-bottom: 0.75rem;
        }
        
        .data-label {
            font-weight: 500;
            width: 150px;
            color: #475569;
        }
        
        .data-value {
            flex: 1;
            word-break: break-all;
        }
        
        .provider-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .provider-google {
            background-color: #f8fafc;
            color: #DB4437;
            border: 1px solid #DB4437;
        }
        
        .provider-apple {
            background-color: #f8fafc;
            color: #000000;
            border: 1px solid #000000;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(99, 73, 158, 0.3);
        }
        
        /* Estilos para a seção de destaque */
        .highlight-section {
            background-color: #effbff;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #0ea5e9;
        }
        
        .highlight-title {
            color: #0369a1;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .highlight-box {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .highlight-item {
            flex: 1;
            min-width: 200px;
            background-color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .highlight-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f0f9ff;
            color: #0ea5e9;
            margin-bottom: 0.75rem;
        }
        
        .highlight-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .highlight-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #334155;
        }
        
        .highlight-empty {
            color: #94a3b8;
            font-style: italic;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Alert para mostrar as informações do usuário -->
        <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
            <h4 class="alert-heading">Informações do usuário</h4>
            <p><strong>Login:</strong> {{ $userData['email'] }}</p>
            <p><strong>Nome:</strong> {{ $userData['name'] }}</p>
            <p><strong>Telefone:</strong> {{ isset($userData['phone']) && $userData['phone'] ? $userData['phone'] : 'Não disponível' }}</p>
            <p><strong>CPF:</strong> <span class="text-danger">Não disponível pelo Google</span></p>
            <hr>
            <p class="mb-0">O Google não fornece o CPF do usuário. Você precisará solicitar essa informação manualmente.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Script direto para garantir que o alert seja exibido -->
        <script>
            // Mostrar alerta imediatamente
            alert("DADOS DO USUÁRIO GOOGLE:\n\nEmail: {{ $userData['email'] }}\nNome: {{ $userData['name'] }}\nTelefone: {{ isset($userData['phone']) && $userData['phone'] ? $userData['phone'] : 'Não disponível' }}\nCPF: Não disponível pelo Google - Informe abaixo");
        </script>

        <div class="user-data-container">
            <div class="user-header">
                @if($userData['avatar'])
                    <img src="{{ $userData['avatar'] }}" alt="{{ $userData['name'] }}" class="user-avatar">
                @else
                    <div class="user-avatar d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-user fa-2x text-secondary"></i>
                    </div>
                @endif
                <div>
                    <h1 class="user-name">{{ $userData['name'] }}</h1>
                    <p class="user-email">{{ $userData['email'] }}</p>
                    <span class="provider-badge provider-{{ $userData['provider'] }}">
                        <i class="fab fa-{{ $userData['provider'] }} me-1"></i>
                        {{ ucfirst($userData['provider']) }}
                    </span>
                </div>
            </div>
            
            <!-- Seção de destaque para as informações principais -->
            <div class="highlight-section">
                <h3 class="highlight-title">Informações obtidas da conta Google</h3>
                <div class="highlight-box">
                    <div class="highlight-item">
                        <div class="highlight-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="highlight-label">Nome</div>
                        <div class="highlight-value">{{ $userData['name'] }}</div>
                    </div>
                    
                    <div class="highlight-item">
                        <div class="highlight-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="highlight-label">Email</div>
                        <div class="highlight-value">{{ $userData['email'] }}</div>
                    </div>
                    
                    <div class="highlight-item">
                        <div class="highlight-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="highlight-label">Telefone</div>
                        @if(isset($userData['phone']) && $userData['phone'])
                            <div class="highlight-value">{{ $userData['phone'] }}</div>
                        @else
                            <div class="highlight-empty">Não disponível</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="data-card">
                <div class="data-title">Informações pessoais</div>
                <div class="data-item">
                    <div class="data-label">Nome</div>
                    <div class="data-value">{{ $userData['name'] }}</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Email</div>
                    <div class="data-value">{{ $userData['email'] }}</div>
                </div>
                @if(isset($userData['phone']))
                <div class="data-item">
                    <div class="data-label">Telefone</div>
                    <div class="data-value">
                        @if($userData['phone'])
                            {{ $userData['phone'] }}
                        @else
                            <span class="text-muted fst-italic">Não disponível</span>
                        @endif
                    </div>
                </div>
                @endif
                <div class="data-item">
                    <div class="data-label">ID no provedor</div>
                    <div class="data-value">{{ $userData['id'] }}</div>
                </div>
                <div class="data-item">
                    <div class="data-label">Provedor</div>
                    <div class="data-value">{{ ucfirst($userData['provider']) }}</div>
                </div>
            </div>
            
            <!-- Formulário para informações adicionais -->
            <div class="data-card">
                <div class="data-title">Informações adicionais necessárias</div>
                <p class="text-muted mb-3">O Google não fornece algumas informações que são necessárias para o cadastro completo. Por favor, forneça os dados abaixo:</p>
                
                <form action="{{ route('social.completeProfile') }}" method="POST" id="completeProfileForm">
                    @csrf
                    <input type="hidden" name="google_id" value="{{ $userData['id'] }}">
                    <input type="hidden" name="google_email" value="{{ $userData['email'] }}">
                    <input type="hidden" name="google_name" value="{{ $userData['name'] }}">
                    
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Digite seu CPF" required>
                        <div class="form-text">Digite apenas os números, sem pontos ou traços.</div>
                    </div>
                    
                    @if(!isset($userData['phone']) || !$userData['phone'])
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Digite seu telefone" required>
                        <div class="form-text">Digite seu telefone com DDD, apenas números.</div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Digite seu endereço completo">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Completar cadastro e continuar</button>
                    </div>
                </form>
            </div>
            
            <div class="data-card">
                <div class="data-title">Detalhes técnicos</div>
                <div class="data-item">
                    <div class="data-label">Token</div>
                    <div class="data-value text-muted">
                        <small>{{ substr($userData['token'], 0, 30) }}...</small>
                    </div>
                </div>
                @if(isset($userData['refreshToken']) && $userData['refreshToken'])
                <div class="data-item">
                    <div class="data-label">Refresh Token</div>
                    <div class="data-value text-muted">
                        <small>{{ substr($userData['refreshToken'], 0, 30) }}...</small>
                    </div>
                </div>
                @endif
                @if(isset($userData['expiresIn']) && $userData['expiresIn'])
                <div class="data-item">
                    <div class="data-label">Expira em</div>
                    <div class="data-value">{{ $userData['expiresIn'] }} segundos</div>
                </div>
                @endif
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>Ir para o Dashboard
                </a>
                <a href="{{ route('logout') }}" 
                   class="btn btn-outline-secondary ms-2"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap & Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para mostrar o alerta ao carregar a página -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validação e máscara para CPF
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) {
                cpfInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 11) {
                        value = value.slice(0, 11);
                    }
                    
                    // Aplicar máscara de CPF
                    if (value.length > 9) {
                        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4");
                    } else if (value.length > 6) {
                        value = value.replace(/^(\d{3})(\d{3})(\d{1,3})$/, "$1.$2.$3");
                    } else if (value.length > 3) {
                        value = value.replace(/^(\d{3})(\d{1,3})$/, "$1.$2");
                    }
                    
                    e.target.value = value;
                });
                
                // Validar CPF antes de enviar o formulário
                document.getElementById('completeProfileForm').addEventListener('submit', function(e) {
                    const cpf = cpfInput.value.replace(/\D/g, '');
                    if (cpf.length !== 11 || !validarCPF(cpf)) {
                        e.preventDefault();
                        alert('Por favor, insira um CPF válido.');
                        cpfInput.focus();
                    }
                });
            }
            
            // Validação e máscara para telefone
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 11) {
                        value = value.slice(0, 11);
                    }
                    
                    // Aplicar máscara de telefone
                    if (value.length > 6) {
                        value = value.replace(/^(\d{2})(\d{5})(\d{0,4})$/, "($1) $2-$3");
                    } else if (value.length > 2) {
                        value = value.replace(/^(\d{2})(\d{0,5})$/, "($1) $2");
                    }
                    
                    e.target.value = value;
                });
            }
            
            // Função para validar CPF
            function validarCPF(cpf) {
                // Elimina CPFs inválidos conhecidos
                if (cpf.length !== 11 || 
                    cpf === "00000000000" || 
                    cpf === "11111111111" || 
                    cpf === "22222222222" || 
                    cpf === "33333333333" || 
                    cpf === "44444444444" || 
                    cpf === "55555555555" || 
                    cpf === "66666666666" || 
                    cpf === "77777777777" || 
                    cpf === "88888888888" || 
                    cpf === "99999999999") {
                    return false;
                }
                
                // Valida 1o dígito
                let add = 0;
                for (let i = 0; i < 9; i++) {
                    add += parseInt(cpf.charAt(i)) * (10 - i);
                }
                let rev = 11 - (add % 11);
                if (rev === 10 || rev === 11) {
                    rev = 0;
                }
                if (rev !== parseInt(cpf.charAt(9))) {
                    return false;
                }
                
                // Valida 2o dígito
                add = 0;
                for (let i = 0; i < 10; i++) {
                    add += parseInt(cpf.charAt(i)) * (11 - i);
                }
                rev = 11 - (add % 11);
                if (rev === 10 || rev === 11) {
                    rev = 0;
                }
                if (rev !== parseInt(cpf.charAt(10))) {
                    return false;
                }
                
                return true;
            }
        });
    </script>
</body>
</html> 