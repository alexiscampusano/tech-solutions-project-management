<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

final class UfService
{
    private const UF_API_URL = 'https://mindicador.cl/api/uf';
    private const CACHE_KEY = 'uf_valor_actual';
    private const CACHE_DURATION = 3600; 

    /**
     * Get current UF value
     */
    public function getValorActual(): array
    {
        try {
            $cachedValue = Cache::get(self::CACHE_KEY);
            if ($cachedValue) {
                return $cachedValue;
            }

            $response = Http::timeout(10)->get(self::UF_API_URL);

            if (!$response->successful()) {
                Log::warning('API UF no disponible', [
                    'status' => $response->status(),
                    'url' => self::UF_API_URL
                ]);
                return $this->getDefaultResponse();
            }

            $data = $response->json();

            if (!isset($data['serie']) || empty($data['serie'])) {
                Log::warning('Respuesta API UF sin datos válidos', ['data' => $data]);
                return $this->getDefaultResponse();
            }

            $ultimoValor = $data['serie'][0];
            
            $resultado = [
                'valor' => (float) $ultimoValor['valor'],
                'fecha' => Carbon::parse($ultimoValor['fecha']),
                'valor_formateado' => '$' . number_format((float) $ultimoValor['valor'], 2, ',', '.'),
                'fecha_formateada' => Carbon::parse($ultimoValor['fecha'])->format('d/m/Y'),
                'status' => 'success',
                'fuente' => 'API mindicador.cl',
                'cache' => false
            ];

            Cache::put(self::CACHE_KEY, $resultado, self::CACHE_DURATION);

            Log::info('Valor UF obtenido exitosamente', [
                'valor' => $resultado['valor'],
                'fecha' => $resultado['fecha_formateada']
            ]);

            return $resultado;

        } catch (\Exception $e) {
            Log::error('Error al obtener valor UF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getDefaultResponse();
        }
    }

    /**
     * Get UF value by specific date
     */
    public function getValorPorFecha(Carbon $fecha): array
    {
        try {
            $fechaFormateada = $fecha->format('d-m-Y');
            $url = self::UF_API_URL . '/' . $fechaFormateada;

            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                return $this->getDefaultResponse();
            }

            $data = $response->json();

            if (!isset($data['serie']) || empty($data['serie'])) {
                return $this->getDefaultResponse();
            }

            $valorFecha = $data['serie'][0];

            return [
                'valor' => (float) $valorFecha['valor'],
                'fecha' => Carbon::parse($valorFecha['fecha']),
                'valor_formateado' => '$' . number_format((float) $valorFecha['valor'], 2, ',', '.'),
                'fecha_formateada' => Carbon::parse($valorFecha['fecha'])->format('d/m/Y'),
                'status' => 'success',
                'fuente' => 'API mindicador.cl',
                'cache' => false
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener valor UF por fecha', [
                'fecha' => $fecha->format('Y-m-d'),
                'error' => $e->getMessage()
            ]);

            return $this->getDefaultResponse();
        }
    }

    /**
     * Convert pesos to UF
     */
    public function convertirPesosAUf(float $montoPesos): array
    {
        $ufData = $this->getValorActual();
        
        if ($ufData['status'] !== 'success') {
            return $ufData;
        }

        $valorUf = $ufData['valor'];
        $montoEnUf = $montoPesos / $valorUf;

        return [
            'monto_pesos' => $montoPesos,
            'monto_pesos_formateado' => '$' . number_format($montoPesos, 0, ',', '.'),
            'valor_uf' => $valorUf,
            'monto_uf' => $montoEnUf,
            'monto_uf_formateado' => number_format($montoEnUf, 2, ',', '.') . ' UF',
            'fecha_conversion' => $ufData['fecha_formateada'],
            'status' => 'success'
        ];
    }

    /**
     * Clean UF cache
     */
    public function limpiarCache(): bool
    {
        return Cache::forget(self::CACHE_KEY);
    }

    /**
     * Check if there are data in cache
     */
    public function tieneDatosEnCache(): bool
    {
        return Cache::has(self::CACHE_KEY);
    }

    /**
     * Default response when there are errors
     */
    private function getDefaultResponse(): array
    {
        return [
            'valor' => 0.0,
            'fecha' => Carbon::now(),
            'valor_formateado' => 'No disponible',
            'fecha_formateada' => Carbon::now()->format('d/m/Y'),
            'status' => 'error',
            'fuente' => 'Valor por defecto',
            'cache' => false,
            'mensaje' => 'No se pudo obtener el valor de la UF. Intenta más tarde.'
        ];
    }
} 