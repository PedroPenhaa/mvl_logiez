@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('sections.dashboard')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Marcar menu como ativo
        $('.menu-item').removeClass('active');
        $('.menu-item[data-section="dashboard"]').addClass('active');
        
        // Adiciona evento aos botões de link de seção
        $('.nav-section').on('click', function() {
            const section = $(this).data('section');
            $('.menu-item').removeClass('active');
            $('.menu-item[data-section="' + section + '"]').addClass('active');
            loadSection(section);
        });
    });
</script>
@endsection
