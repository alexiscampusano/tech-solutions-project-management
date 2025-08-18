<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Proyecto;
use App\Exceptions\ProyectoNotFoundException;
use App\Exceptions\DatabaseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

final class ProyectoService
{
    /**
     * Get all projects ordered by creation date
     */
    public function obtenerTodos(): Collection
    {
        try {
            return Proyecto::with('creador')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            throw new DatabaseException('obtener proyectos', $e);
        }
    }

    /**
     * Create a new project
     */
    public function crear(array $datos): Proyecto
    {
        try {
            $userId = $this->getCurrentUserId();
            if ($userId) {
                $datos['created_by'] = $userId;
            }
            
            return Proyecto::create($datos);
        } catch (\Exception $e) {
            throw new DatabaseException('crear proyecto', $e);
        }
    }

    /**
     * Get current user ID (JWT or session)
     */
    private function getCurrentUserId(): ?int
    {
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                return $user->id;
            }
        } catch (\Exception $e) {
        }

        return Auth::id();
    }

    /**
     * Get a project by ID
     */
    public function obtenerPorId(string $id): Proyecto
    {
        try {
            return Proyecto::with('creador')->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ProyectoNotFoundException($id);
        }
    }

    /**
     * Update an existing project
     */
    public function actualizar(string $id, array $datos): Proyecto
    {
        try {
            $proyecto = Proyecto::with('creador')->findOrFail($id);
            $proyecto->update($datos);
            return $proyecto->fresh('creador');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ProyectoNotFoundException($id);
        } catch (\Exception $e) {
            throw new DatabaseException('actualizar proyecto', $e);
        }
    }

    /**
     * Delete a project
     */
    public function eliminar(string $id): string
    {
        try {
            $proyecto = Proyecto::findOrFail($id);
            $nombreProyecto = $proyecto->nombre;
            $proyecto->delete();
            return $nombreProyecto;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new ProyectoNotFoundException($id);
        } catch (\Exception $e) {
            throw new DatabaseException('eliminar proyecto', $e);
        }
    }

    /**
     * Get available states for projects
     */
    public function obtenerEstados(): array
    {
        return Proyecto::ESTADOS;
    }
}