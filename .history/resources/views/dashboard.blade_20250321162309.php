@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('sections.dashboard')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log("Dashboard inicializado");
        
        // Marcar dashboard como ativo no menu
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="dashboard"]').addClass('active');
        
        // Inicializar eventos para botões de navegação
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
