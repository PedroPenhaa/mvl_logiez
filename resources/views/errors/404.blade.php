@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4><i class="fas fa-search"></i> Página Não Encontrada</h4>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-1 text-warning">404</h1>
                    <h3>Página não encontrada</h3>
                    <p class="lead">A página que você está procurando não existe ou foi movida.</p>
                    
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
