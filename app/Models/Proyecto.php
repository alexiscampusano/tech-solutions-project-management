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
        'monto',
        'created_by'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'monto' => 'decimal:2',
        'created_by' => 'integer',
    ];

    public const ESTADOS = [
        'iniciado' => 'Iniciado',
        'en_progreso' => 'En Progreso',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado',
    ];

    /**
     * Get formatted state for display
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    /**
     * Get formatted amount for display
     */
    public function getMontoFormateadoAttribute(): string
    {
        return '$' . number_format((float) $this->monto, 0, ',', '.');
    }

    /**
     * Get formatted start date in Spanish
     */
    public function getFechaInicioFormateadaAttribute(): string
    {
        return $this->fecha_inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    }

    /**
     * Get formatted creation date in Spanish
     */
    public function getFechaCreacionFormateadaAttribute(): string
    {
        return $this->created_at->locale('es')->isoFormat('D/M/YYYY HH:mm');
    }

    /**
     * Get relative creation date in Spanish
     */
    public function getFechaCreacionRelativaAttribute(): string
    {
        return $this->created_at->locale('es')->diffForHumans();
    }

    /**
     * Get formatted update date in Spanish
     */
    public function getFechaActualizacionFormateadaAttribute(): string
    {
        return $this->updated_at->locale('es')->isoFormat('D/M/YYYY HH:mm');
    }

    /**
     * Get relative update date in Spanish
     */
    public function getFechaActualizacionRelativaAttribute(): string
    {
        return $this->updated_at->locale('es')->diffForHumans();
    }

    /**
     * Scope to filter by state
     */
    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope to filter active projects (not canceled)
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 'cancelado');
    }

    /**
     * Relation: Project belongs to a User (creator)
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
