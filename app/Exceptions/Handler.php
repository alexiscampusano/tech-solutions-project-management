<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        // Manejar todas las excepciones de API
        $this->renderable(function (Throwable $e, Request $request) {
            // Solo manejar rutas de API
            if (!$request->is('api/*')) {
                return null; // Dejar que Laravel maneje las rutas web normalmente
            }

            return $this->handleApiException($e, $request);
        });
    }

    /**
     * Manejar excepciones específicamente para rutas de API
     */
    protected function handleApiException(Throwable $exception, Request $request): JsonResponse
    {
        // Si ya es una ApiException personalizada, usar su render()
        if ($exception instanceof ApiException) {
            return $exception->render();
        }

        // Manejar excepciones específicas de Laravel
        if ($exception instanceof LaravelValidationException) {
            return $this->handleValidationException($exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException();
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($exception);
        }

        // Para cualquier otra excepción no manejada
        return $this->handleGenericException($exception);
    }

    /**
     * Manejar errores de validación de Laravel
     */
    protected function handleValidationException(LaravelValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Los datos proporcionados no son válidos',
                'details' => [
                    'validation_errors' => $exception->errors()
                ]
            ]
        ], 422);
    }

    /**
     * Manejar cuando un modelo no es encontrado
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception): JsonResponse
    {
        $model = class_basename($exception->getModel());
        
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'RESOURCE_NOT_FOUND',
                'message' => "{$model} no encontrado",
                'details' => [
                    'model' => $model
                ]
            ]
        ], 404);
    }

    /**
     * Manejar rutas no encontradas (404)
     */
    protected function handleNotFoundHttpException(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'ROUTE_NOT_FOUND',
                'message' => 'La ruta solicitada no existe'
            ]
        ], 404);
    }

    /**
     * Manejar métodos HTTP no permitidos
     */
    protected function handleMethodNotAllowedException(MethodNotAllowedHttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'METHOD_NOT_ALLOWED',
                'message' => 'Método HTTP no permitido para esta ruta',
                'details' => [
                    'allowed_methods' => $exception->getHeaders()['Allow'] ?? 'Unknown'
                ]
            ]
        ], 405);
    }

    /**
     * Manejar cualquier otra excepción no prevista
     */
    protected function handleGenericException(Throwable $exception): JsonResponse
    {
        // Para excepciones genéricas, usar código 500
        $statusCode = 500;

        $response = [
            'success' => false,
            'error' => [
                'code' => 'INTERNAL_ERROR',
                'message' => 'Ha ocurrido un error interno del servidor'
            ]
        ];

        // En desarrollo, mostrar información detallada del error
        if (config('app.debug')) {
            $response['error']['message'] = $exception->getMessage();
            $response['debug'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        return response()->json($response, $statusCode);
    }
} 