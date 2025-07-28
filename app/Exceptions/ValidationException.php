<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends ApiException
{
    public function __construct(array $errors, string $message = 'Los datos proporcionados no son válidos')
    {
        parent::__construct(
            message: $message,
            statusCode: 422,
            errorCode: 'VALIDATION_ERROR',
            details: ['validation_errors' => $errors]
        );
    }
} 