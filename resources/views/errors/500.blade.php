@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4><i class="fas fa-exclamation-triangle"></i> Erro Interno do Servidor</h4>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-1 text-danger">500</h1>
                    <h3>Ops! Algo deu errado.</h3>
                    <p class="lead">Ocorreu um erro interno no servidor. Nossa equipe foi notificada e está trabalhando para resolver o problema.</p>
                    
                    @if(config('app.debug'))
                        <div class="alert alert-info">
                            <strong>Detalhes do erro (apenas em desenvolvimento):</strong><br>
                            {{ $exception->getMessage() }}
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
