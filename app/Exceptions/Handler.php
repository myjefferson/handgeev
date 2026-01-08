<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

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
        $response = parent::render($request, $e);
        $status = method_exists($response, 'status') ? $response->status() : 500;

        // Só processa se não for JSON e for um status que queremos customizar
        if (!$request->expectsJson() && 
            in_array($status, [403, 404, 419, 500, 503])) {
            
            $messages = [
                403 => 'Acesso negado.',
                404 => 'Página não encontrada.',
                419 => 'Sua sessão expirou. Por favor, atualize a página.',
                500 => 'Erro interno do servidor.',
                503 => 'Serviço temporariamente indisponível.',
            ];

            // Renderiza com Inertia
            return Inertia::render("Errors/{$status}", [
                'status' => $status,
                'message' => $messages[$status] ?? 'Ocorreu um erro.',
            ])
            ->toResponse($request)
            ->setStatusCode($status);
        }

        return $response;
    }
}