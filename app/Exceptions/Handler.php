<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * Render an exception into an HTTP response.
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

        // Para outras requisições, usar o renderizador padrão do Laravel
        return parent::render($request, $e);
    }
}
