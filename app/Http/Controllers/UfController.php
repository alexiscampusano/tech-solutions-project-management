<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

final class UfController extends Controller
{
    public function __construct(
        private readonly UfService $ufService
    ) {}

    /**
     * Obtener el valor actual de la UF
     */
    public function getValorActual(): JsonResponse
    {
        $resultado = $this->ufService->getValorActual();

        return response()->json([
            'success' => $resultado['status'] === 'success',
            'data' => $resultado,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtener valor de UF por fecha específica
     */
    public function getValorPorFecha(Request $request): JsonResponse
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $fecha = Carbon::parse($request->fecha);
        $resultado = $this->ufService->getValorPorFecha($fecha);

        return response()->json([
            'success' => $resultado['status'] === 'success',
            'data' => $resultado,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Convertir monto en pesos a UF
     */
    public function convertirPesosAUf(Request $request): JsonResponse
    {
        $request->validate([
            'monto' => 'required|numeric|min:0'
        ]);

        $resultado = $this->ufService->convertirPesosAUf((float) $request->monto);

        return response()->json([
            'success' => $resultado['status'] === 'success',
            'data' => $resultado,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Limpiar cache de UF
     */
    public function limpiarCache(): JsonResponse
    {
        $limpiado = $this->ufService->limpiarCache();

        return response()->json([
            'success' => $limpiado,
            'message' => $limpiado ? 'Cache limpiado exitosamente' : 'Error al limpiar cache',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtener estado del cache
     */
    public function estadoCache(): JsonResponse
    {
        $tieneCache = $this->ufService->tieneDatosEnCache();

        return response()->json([
            'success' => true,
            'data' => [
                'tiene_cache' => $tieneCache,
                'mensaje' => $tieneCache ? 'Datos en cache disponibles' : 'No hay datos en cache'
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}
