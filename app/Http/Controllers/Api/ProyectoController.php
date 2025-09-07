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
use Symfony\Component\HttpFoundation\Response;

final class ProyectoController extends Controller
{
    public function __construct(
        private readonly ProyectoService $proyectoService
    ) {}

    /**
     * Get all projects
     */
    public function index(): JsonResponse
    {
        try {
            $proyectos = $this->proyectoService->obtenerTodos();

            return response()->json([
                'success' => true,
                'data' => $proyectos,
                'message' => 'Proyectos obtenidos exitosamente'
            ], Response::HTTP_OK);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los proyectos',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new project
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
            ], Response::HTTP_CREATED);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el proyecto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a single project by ID
     */
    public function show(string $id): JsonResponse
    {
        try {
            $proyecto = $this->proyectoService->obtenerPorId($id);

            return response()->json([
                'success' => true,
                'data' => $proyecto,
                'message' => 'Proyecto obtenido exitosamente'
            ], Response::HTTP_OK);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update an existing project
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
            ], Response::HTTP_OK);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
                
        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a project by ID
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->proyectoService->eliminar($id);

            return response()->json([], Response::HTTP_NO_CONTENT);

        } catch (ProyectoNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
                
        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proyecto',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get available states for projects
     */
    public function estados(): JsonResponse
    {
        try {
            $estados = $this->proyectoService->obtenerEstados();

            return response()->json([
                'success' => true,
                'data' => $estados,
                'message' => 'Estados obtenidos exitosamente'
            ], Response::HTTP_OK);

        } catch (DatabaseException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estados',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate project data for API requests
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
