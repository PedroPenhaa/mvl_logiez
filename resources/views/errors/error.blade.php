@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4><i class="fas fa-exclamation-triangle"></i> Erro</h4>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-1 text-danger">{{ $exception->getCode() ?: 'Erro' }}</h1>
                    <h3>Ocorreu um erro</h3>
                    <p class="lead">{{ $exception->getMessage() ?: 'Um erro inesperado ocorreu.' }}</p>
                    
                    @if(config('app.debug'))
                        <div class="alert alert-info">
                            <strong>Detalhes do erro (apenas em desenvolvimento):</strong><br>
                            <strong>Arquivo:</strong> {{ $exception->getFile() }}<br>
                            <strong>Linha:</strong> {{ $exception->getLine() }}
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Voltar ao Início
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
