<?php

declare(strict_types=1);

namespace App\Exceptions;

class ProyectoNotFoundException extends ApiException
{
    public function __construct(string $proyectoId)
    {
        parent::__construct(
            message: "El proyecto con ID '{$proyectoId}' no fue encontrado",
            statusCode: 404,
            errorCode: 'PROYECTO_NOT_FOUND',
            details: ['proyecto_id' => $proyectoId]
        );
    }
} 