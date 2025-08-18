<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     * Get current UF value
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
     * Get UF value by specific date
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
     * Convert pesos to UF
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
     * Clean UF cache
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
     * Get UF cache status
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
