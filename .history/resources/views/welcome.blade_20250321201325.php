<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logiez - Simplifique suas Remessas Internacionais</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #63499E;
            --secondary-color: #2c3e50;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --accent-color: #e74c3c;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark-color);
            overflow-x: hidden;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #533c85;
            border-color: #533c85;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-secondary {
            color: var(--secondary-color) !important;
        }
        
        .bg-light {
            background-color: var(--light-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .navbar {
            background-color: transparent;
            transition: all 0.3s ease;
            padding: 1.5rem 0;
        }
        
        .navbar.scrolled {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            margin: 0 10px;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .hero {
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 6rem 0;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #63499E 0%, #9370DB 100%);
            z-index: -1;
        }
        
        .hero-content {
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
        }
        
        .features-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .feature-box {
            padding: 2rem;
            transition: all 0.3s ease;
            border-radius: 10px;
            height: 100%;
        }
        
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            font-weight: 700;
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 3rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--primary-color);
        }
        
        .how-it-works .step {
            position: relative;
            padding-bottom: 3rem;
        }
        
        .how-it-works .step:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 50px;
            background-color: var(--primary-color);
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .testimonial-card .quote {
            font-size: 1.2rem;
            font-style: italic;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .testimonial-card .quote::before,
        .testimonial-card .quote::after {
            content: '"';
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .testimonial-author {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 4rem 0 2rem;
        }
        
        .footer-link {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
            transition: color 0.3s ease;
        }
        
        .social-links a:hover {
            color: var(--primary-color);
        }
        
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 2.8rem;
            }
            
            .pricing-table {
                margin-bottom: 30px;
            }
            
            .navbar-collapse {
                background-color: white;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                margin-top: 10px;
            }
            
            .navbar.scrolled .navbar-collapse {
                background-color: white;
            }
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.3rem;
            }
            
            .hero-content p {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .feature-box {
                margin-bottom: 15px;
            }
            
            .how-it-works .step {
                padding: 15px;
            }
            
            .testimonial-item {
                padding: 15px;
            }
            
            .contact-info {
                margin-bottom: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            
            #hero {
                min-height: 80vh;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .footer-links, .footer-social {
                text-align: center;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <span class="text-secondary">Logi</span><span class="text-primary">ez</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#features">Vantagens</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">Como Funciona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Depoimentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contato</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i> Acessar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Simplifique seus Envios Internacionais</h1>
                    <p class="hero-subtitle">Cotação, envio e rastreamento em uma única plataforma, conectada com a DHL.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> Começar Agora
                        </a>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i> Saiba Mais
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container">
            <h2 class="section-title text-center">Vantagens da Logiez</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-calculator features-icon"></i>
                        <h3>Cotação Rápida</h3>
                        <p>Calculamos o melhor preço para seu envio em segundos, com base nas dimensões e peso do pacote.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-globe-americas features-icon"></i>
                        <h3>Envio Global</h3>
                        <p>Alcance mais de 150 países com nossa integração com a DHL, líder em logística internacional.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-search-location features-icon"></i>
                        <h3>Rastreamento em Tempo Real</h3>
                        <p>Acompanhe o status do seu envio a qualquer momento, com atualizações em tempo real.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-file-alt features-icon"></i>
                        <h3>Documentação Simplificada</h3>
                        <p>Geramos automaticamente toda a documentação necessária para seu envio internacional.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-tag features-icon"></i>
                        <h3>Etiquetas Personalizadas</h3>
                        <p>Crie e imprima etiquetas de envio diretamente da plataforma, sem complicações.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center bg-light h-100">
                        <i class="fas fa-headset features-icon"></i>
                        <h3>Suporte Especializado</h3>
                        <p>Nossa equipe está disponível para ajudar em todas as etapas do processo de envio.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-light" id="how-it-works">
        <div class="container">
            <h2 class="section-title text-center">Como Funciona</h2>
            <div class="how-it-works">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="step text-center">
                            <div class="step-number">1</div>
                            <h3>Faça sua cotação</h3>
                            <p>Informe as dimensões, peso e destino do seu pacote para obter o melhor preço instantaneamente.</p>
                        </div>
                        <div class="step text-center">
                            <div class="step-number">2</div>
                            <h3>Complete seus dados</h3>
                            <p>Preencha as informações do remetente, destinatário e detalhes do que está sendo enviado.</p>
                        </div>
                        <div class="step text-center">
                            <div class="step-number">3</div>
                            <h3>Realize o pagamento</h3>
                            <p>Escolha entre cartão de crédito, boleto bancário ou PIX para concluir o pagamento com segurança.</p>
                        </div>
                        <div class="step text-center">
                            <div class="step-number">4</div>
                            <h3>Imprima sua etiqueta</h3>
                            <p>Gere e imprima a etiqueta de envio para anexar ao seu pacote.</p>
                        </div>
                        <div class="step text-center">
                            <div class="step-number">5</div>
                            <h3>Acompanhe seu envio</h3>
                            <p>Rastreie o status da sua encomenda em tempo real, desde a coleta até a entrega.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket me-2"></i> Comece a Usar Agora
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5" id="testimonials">
        <div class="container">
            <h2 class="section-title text-center">O que Nossos Clientes Dizem</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card h-100">
                        <p class="quote">A Logiez simplificou completamente o processo de enviar produtos para meus clientes internacionais. Economizo tempo e dinheiro!</p>
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="testimonial-author">Ana Silva</p>
                                <p class="text-muted mb-0">Loja de Artesanato</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card h-100">
                        <p class="quote">O rastreamento em tempo real é fantástico. Consigo acompanhar exatamente onde está meu pacote e prever quando chegará ao destino.</p>
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="testimonial-author">João Oliveira</p>
                                <p class="text-muted mb-0">E-commerce</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card h-100">
                        <p class="quote">Nunca foi tão fácil enviar documentos para o exterior. A cotação é transparente, sem taxas escondidas, e o processo é super rápido.</p>
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="testimonial-author">Maria Santos</p>
                                <p class="text-muted mb-0">Escritório de Advocacia</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-5 bg-light" id="pricing">
        <div class="container">
            <h2 class="section-title text-center">Preços Transparentes</h2>
            <div class="row justify-content-center g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Documentos</h5>
                            <h2 class="card-subtitle mb-2 text-primary">A partir de R$ 150</h2>
                            <p class="card-text">Ideal para envio de documentos e pequenos itens com até 500g.</p>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item">Documentos</li>
                                <li class="list-group-item">Contratos</li>
                                <li class="list-group-item">Certidões</li>
                                <li class="list-group-item">Pequenos itens</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Cotar Agora</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white text-center">Mais Popular</div>
                        <div class="card-body text-center">
                            <h5 class="card-title">Pacotes</h5>
                            <h2 class="card-subtitle mb-2 text-primary">A partir de R$ 250</h2>
                            <p class="card-text">Perfeito para envio de pacotes com até 5kg para qualquer destino.</p>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item">Encomendas</li>
                                <li class="list-group-item">Presentes</li>
                                <li class="list-group-item">Produtos</li>
                                <li class="list-group-item">Amostras</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-primary">Cotar Agora</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Cargas</h5>
                            <h2 class="card-subtitle mb-2 text-primary">A partir de R$ 500</h2>
                            <p class="card-text">Para envios comerciais e cargas acima de 5kg com seguro incluso.</p>
                            <ul class="list-group list-group-flush mb-4">
                                <li class="list-group-item">Cargas comerciais</li>
                                <li class="list-group-item">Exportação</li>
                                <li class="list-group-item">Frete empresarial</li>
                                <li class="list-group-item">Seguro premium</li>
                            </ul>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Cotar Agora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5" id="contact">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Entre em Contato</h2>
                    <p class="mb-4">Tem dúvidas sobre nossos serviços? Entre em contato conosco e nossa equipe terá prazer em ajudar.</p>
                    <div class="mb-4">
                        <h5><i class="fas fa-envelope text-primary me-2"></i> Email</h5>
                        <p>contato@logiez.com.br</p>
                    </div>
                    <div class="mb-4">
                        <h5><i class="fas fa-phone text-primary me-2"></i> Telefone</h5>
                        <p>+55 (11) 4002-8922</p>
                    </div>
                    <div>
                        <h5><i class="fas fa-map-marker-alt text-primary me-2"></i> Endereço</h5>
                        <p>Av. Paulista, 1000, São Paulo - SP, Brasil</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" placeholder="Seu nome">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="seu@email.com">
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Assunto</label>
                                    <input type="text" class="form-control" id="subject" placeholder="Assunto da mensagem">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mensagem</label>
                                    <textarea class="form-control" id="message" rows="5" placeholder="Sua mensagem"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="mb-4">
                        <span class="text-white">Logi</span><span class="text-primary">ez</span>
                    </h3>
                    <p>Simplificando o processo de envios internacionais com tecnologia e integração com as melhores transportadoras.</p>
                    <div class="social-links mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="mb-4">Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="footer-link">Vantagens</a></li>
                        <li class="mb-2"><a href="#how-it-works" class="footer-link">Como Funciona</a></li>
                        <li class="mb-2"><a href="#testimonials" class="footer-link">Depoimentos</a></li>
                        <li class="mb-2"><a href="#pricing" class="footer-link">Preços</a></li>
                        <li><a href="#contact" class="footer-link">Contato</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="mb-4">Serviços</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Cotação</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Envio</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Rastreamento</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Etiquetas</a></li>
                        <li><a href="#" class="footer-link">Documentação</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="mb-4">Newsletter</h5>
                    <p>Receba novidades e dicas sobre envios internacionais.</p>
                    <form class="mt-4">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Seu email">
                            <button class="btn btn-primary" type="submit">Inscrever</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="mt-5 mb-4 border-top border-secondary">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-md-0">&copy; {{ date('Y') }} Logiez. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link me-3">Termos de Uso</a>
                    <a href="#" class="footer-link">Política de Privacidade</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS, Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Navbar efeito de scroll
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
            
            // Navegação suave para links de âncora
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        const navbarHeight = document.querySelector('.navbar').offsetHeight;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                        
                        // Fechar menu mobile se estiver aberto
                        const navbarCollapse = document.querySelector('.navbar-collapse');
                        if (navbarCollapse.classList.contains('show')) {
                            bootstrap.Collapse.getInstance(navbarCollapse).hide();
                        }
                    }
                });
            });
            
            // Ativar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html> 