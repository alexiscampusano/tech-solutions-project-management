<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

final class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'estado',
        'responsable',
        'monto'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'monto' => 'decimal:2',
    ];

    public const ESTADOS = [
        'iniciado' => 'Iniciado',
        'en_progreso' => 'En Progreso',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado',
    ];

    /**
     * Obtener el estado formateado para mostrar
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Obtener el monto formateado para mostrar
     */
    public function getMontoFormateadoAttribute(): string
    {
        return '$' . number_format((float) $this->monto, 0, ',', '.');
    }

    /**
     * Obtener la fecha de inicio formateada en español
     */
    public function getFechaInicioFormateadaAttribute(): string
    {
        return $this->fecha_inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    }

    /**
     * Obtener la fecha de creación formateada en español
     */
    public function getFechaCreacionFormateadaAttribute(): string
    {
        return $this->created_at->locale('es')->isoFormat('D/M/YYYY HH:mm');
    }

    /**
     * Obtener la fecha de creación relativa en español
     */
    public function getFechaCreacionRelativaAttribute(): string
    {
        return $this->created_at->locale('es')->diffForHumans();
    }

    /**
     * Obtener la fecha de actualización formateada en español
     */
    public function getFechaActualizacionFormateadaAttribute(): string
    {
        return $this->updated_at->locale('es')->isoFormat('D/M/YYYY HH:mm');
    }

    /**
     * Obtener la fecha de actualización relativa en español
     */
    public function getFechaActualizacionRelativaAttribute(): string
    {
        return $this->updated_at->locale('es')->diffForHumans();
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para proyectos activos (no cancelados)
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 'cancelado');
    }
}
