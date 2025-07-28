<?php

declare(strict_types=1);

namespace App\Exceptions;

class DatabaseException extends ApiException
{
    public function __construct(string $operation, ?\Throwable $previous = null)
    {
        $message = "Error en la base de datos durante la operación: {$operation}";
        
        parent::__construct(
            message: $message,
            statusCode: 500,
            errorCode: 'DATABASE_ERROR',
            details: ['operation' => $operation],
            previous: $previous
        );
    }
} 