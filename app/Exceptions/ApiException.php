<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    protected array $details;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 500,
        string $errorCode = 'INTERNAL_ERROR',
        array $details = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
            ]
        ];

        if (!empty($this->details)) {
            $response['error']['details'] = $this->details;
        }

        // En desarrollo, incluir información adicional para debugging
        if (config('app.debug')) {
            $response['debug'] = [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString(),
            ];
        }

        return response()->json($response, $this->statusCode);
    }
} 