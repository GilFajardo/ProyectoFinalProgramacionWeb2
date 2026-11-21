<?php

namespace App\Models;

use App\Enums\EstatusPostulacion;
use Database\Factories\PostulacionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Postulación de un alumno a una vacante.
 * La pareja (vacante_id, alumno_id) es única en la base de datos.
 */
#[Table('postulaciones')]
#[Fillable([
    'vacante_id', 'alumno_id', 'estatus', 'carta_presentacion',
    'comentario_reclutador', 'estatus_actualizado_en',
])]
class Postulacion extends Model
{
    /** @use HasFactory<PostulacionFactory> */
    use HasFactory;

    /**
     * Valor por defecto visible antes de guardar (coincide con la migración).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'estatus' => 'en_proceso',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estatus' => EstatusPostulacion::class,
            'estatus_actualizado_en' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Vacante, $this>
     */
    public function vacante(): BelongsTo
    {
        return $this->belongsTo(Vacante::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Alumno, $this>
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * Cambia el estatus y registra el momento del cambio.
     */
    public function cambiarEstatus(EstatusPostulacion $estatus, ?string $comentario = null): void
    {
        $this->forceFill([
            'estatus' => $estatus,
            'comentario_reclutador' => $comentario ?? $this->comentario_reclutador,
            'estatus_actualizado_en' => now(),
        ])->save();
    }
}
