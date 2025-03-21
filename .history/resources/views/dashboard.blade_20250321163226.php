@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Inserindo conteúdo do dashboard diretamente -->
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
    });
</script>
@endsection
