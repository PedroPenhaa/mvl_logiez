<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Sobrescrever o método render para desabilitar o renderizador customizado
     * que está causando o erro do laravel-exceptions-renderer
     */
    public function render($request, Throwable $e)
    {
        // Se for uma requisição API, retornar JSON
        if ($request->is('api/*')) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }

        // Para outras requisições, usar o render padrão do Laravel
        // mas sem o renderizador customizado problemático
        return parent::render($request, $e);
    }

    /**
     * Desabilitar o renderizador de exceções customizado
     */
    protected function renderExceptionWithCustomRenderer($e)
    {
        // Retornar null para forçar o uso do renderizador padrão
        return null;
    }
}
