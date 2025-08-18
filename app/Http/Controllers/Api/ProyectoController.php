<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProyectoService;
use App\Exceptions\ProyectoNotFoundException;
use App\Exceptions\DatabaseException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Models\Proyecto;

final class ProyectoController extends Controller
{
    public function __construct(
        private readonly ProyectoService $proyectoService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $proyectos = $this->proyectoService->obtenerTodos();

            return response()->json([
                'success' => true,
                'data' => $proyectos,
                'message' => 'Proyectos obtenidos exitosamente'
            ]);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los proyectos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validarDatosProyecto($request);

        try {
            $proyecto = $this->proyectoService->crear($validated);

            return response()->json([
                'success' => true,
                'data' => $proyecto,
                'message' => 'Proyecto creado exitosamente'
            ], 201);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $proyecto = $this->proyectoService->obtenerPorId($id);

            return response()->json([
                'success' => true,
                'data' => $proyecto,
                'message' => 'Proyecto obtenido exitosamente'
            ]);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $this->validarDatosProyecto($request);

        try {
            $proyecto = $this->proyectoService->actualizar($id, $validated);

            return response()->json([
                'success' => true,
                'data' => $proyecto,
                'message' => 'Proyecto actualizado exitosamente'
            ]);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], 404);
                
        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $nombreProyecto = $this->proyectoService->eliminar($id);

            return response()->json([
                'success' => true,
                'message' => "Proyecto '{$nombreProyecto}' eliminado exitosamente"
            ]);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], 404);
                
        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estados disponibles para proyectos
     */
    public function estados(): JsonResponse
    {
        try {
            $estados = $this->proyectoService->obtenerEstados();

            return response()->json([
                'success' => true,
                'data' => $estados,
                'message' => 'Estados obtenidos exitosamente'
            ]);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar datos del proyecto para API requests
     */
    private function validarDatosProyecto(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'estado' => ['required', Rule::in(array_keys(Proyecto::ESTADOS))],
            'responsable' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0|max:999999999999999.99',
        ]);
    }
}
