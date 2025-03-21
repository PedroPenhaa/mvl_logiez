@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-calculator me-2"></i> Cotação
            </div>
            <div class="card-body text-center">
                <i class="fas fa-calculator fa-4x mb-3 text-primary"></i>
                <h5 class="card-title">Calcule o Envio</h5>
                <p class="card-text">Calcule o valor e prazo de entrega para seus envios internacionais.</p>
                <button class="btn btn-primary nav-section" data-section="cotacao">Fazer Cotação</button>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-shipping-fast me-2"></i> Envio
            </div>
            <div class="card-body text-center">
                <i class="fas fa-shipping-fast fa-4x mb-3 text-primary"></i>
                <h5 class="card-title">Envie Produtos</h5>
                <p class="card-text">Envie seus produtos para qualquer lugar do mundo de forma rápida e segura.</p>
                <button class="btn btn-primary nav-section" data-section="envio">Fazer Envio</button>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-map-marker-alt me-2"></i> Rastreamento
            </div>
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt fa-4x mb-3 text-primary"></i>
                <h5 class="card-title">Acompanhe seu Envio</h5>
                <p class="card-text">Acompanhe o status de seus envios em tempo real.</p>
                <button class="btn btn-primary nav-section" data-section="rastreamento">Rastrear Envio</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i> Sobre a Logiez
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12 mb-lg-0 mb-4">
                        <h5>Envios Internacionais Simplificados</h5>
                        <p>A Logiez é uma plataforma inovadora que simplifica o processo de envios internacionais, eliminando a burocracia e reduzindo custos.</p>
                        <p>Nosso sistema integrado com a DHL oferece:</p>
                        <ul>
                            <li>Cálculo preciso de custos de envio</li>
                            <li>Comparação entre peso cubado e peso real</li>
                            <li>Geração automática de etiquetas</li>
                            <li>Rastreamento em tempo real</li>
                            <li>Suporte para documentação de exportação</li>
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-12 d-flex align-items-center justify-content-center">
                        <img src="https://via.placeholder.com/400x200?text=Logiez" alt="Logiez" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Adiciona evento aos botões de link de seção
        $('.nav-section').on('click', function() {
            const section = $(this).data('section');
            // Remover classe ativa de todos os itens do menu
            $('.menu-item').removeClass('active');
            // Adicionar classe ativa ao item do menu correspondente
            $('.menu-item[data-section="' + section + '"]').addClass('active');
            // Carregar a seção
            loadSection(section);
        });
    });
</script>
@endsection
