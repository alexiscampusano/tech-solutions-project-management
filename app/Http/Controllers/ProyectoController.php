<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Exceptions\ProyectoNotFoundException;
use App\Exceptions\DatabaseException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

final class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            $proyectos = Proyecto::orderBy('created_at', 'desc')->get();

            if ($request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'data' => $proyectos,
                    'message' => 'Proyectos obtenidos exitosamente'
                ]);
            }

            return view('proyectos.index', compact('proyectos'));

        } catch (\Exception $e) {
            if ($request->is('api/*')) {
                throw new DatabaseException('obtener proyectos', $e);
            }
            
            return view('proyectos.index', ['proyectos' => collect()])
                ->with('error', 'Error al cargar los proyectos');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $estados = Proyecto::ESTADOS;
        return view('proyectos.create', compact('estados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $isApiRequest = $request->is('api/*');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'estado' => ['required', Rule::in(array_keys(Proyecto::ESTADOS))],
            'responsable' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0|max:999999999999999.99',
        ]);

        try {
            $proyecto = Proyecto::create($validated);

            if ($isApiRequest) {
                return response()->json([
                    'success' => true,
                    'data' => $proyecto,
                    'message' => 'Proyecto creado exitosamente'
                ], 201);
            }

            return redirect()->route('proyectos.index')
                ->with('success', 'Proyecto creado exitosamente');

        } catch (\Exception $e) {
            if ($isApiRequest) {
                throw new DatabaseException('crear proyecto', $e);
            }

            return back()->withInput()
                ->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): View|JsonResponse|RedirectResponse
    {
        $isApiRequest = $request->is('api/*');
        
        try {
            $proyecto = Proyecto::findOrFail($id);

            if ($isApiRequest) {
                return response()->json([
                    'success' => true,
                    'data' => $proyecto,
                    'message' => 'Proyecto obtenido exitosamente'
                ]);
            }

            return view('proyectos.show', compact('proyecto'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($isApiRequest) {
                throw new ProyectoNotFoundException($id);
            }

            return redirect()->route('proyectos.index')
                ->with('error', 'Proyecto no encontrado');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View|RedirectResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);
            $estados = Proyecto::ESTADOS;
            
            return view('proyectos.edit', compact('proyecto', 'estados'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('proyectos.index')
                ->with('error', 'Proyecto no encontrado');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $isApiRequest = $request->is('api/*');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'estado' => ['required', Rule::in(array_keys(Proyecto::ESTADOS))],
            'responsable' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0|max:999999999999999.99',
        ]);

        try {
            $proyecto = Proyecto::findOrFail($id);
            $proyecto->update($validated);

            if ($isApiRequest) {
                return response()->json([
                    'success' => true,
                    'data' => $proyecto->fresh(),
                    'message' => 'Proyecto actualizado exitosamente'
                ]);
            }

            return redirect()->route('proyectos.index')
                ->with('success', 'Proyecto actualizado exitosamente');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($isApiRequest) {
                throw new ProyectoNotFoundException($id);
            }

            return back()->withInput()
                ->with('error', 'Proyecto no encontrado');
                
        } catch (\Exception $e) {
            if ($isApiRequest) {
                throw new DatabaseException('actualizar proyecto', $e);
            }

            return back()->withInput()
                ->with('error', 'Error al actualizar el proyecto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $isApiRequest = $request->is('api/*');
        
        try {
            $proyecto = Proyecto::findOrFail($id);
            $nombreProyecto = $proyecto->nombre;
            $proyecto->delete();

            if ($isApiRequest) {
                return response()->json([
                    'success' => true,
                    'message' => "Proyecto '{$nombreProyecto}' eliminado exitosamente"
                ]);
            }

            return redirect()->route('proyectos.index')
                ->with('success', "Proyecto '{$nombreProyecto}' eliminado exitosamente");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($isApiRequest) {
                throw new ProyectoNotFoundException($id);
            }

            return redirect()->route('proyectos.index')
                ->with('error', 'Proyecto no encontrado');
                
        } catch (\Exception $e) {
            if ($isApiRequest) {
                throw new DatabaseException('eliminar proyecto', $e);
            }

            return redirect()->route('proyectos.index')
                ->with('error', 'Error al eliminar el proyecto: ' . $e->getMessage());
        }
    }
}
