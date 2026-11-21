<?php

namespace App\Models;

use Database\Factories\CvExperienciaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CV: empleo anterior, práctica profesional o estadía del alumno.
 */
#[Fillable(['alumno_id', 'empresa', 'puesto', 'descripcion', 'fecha_inicio', 'fecha_fin', 'actual'])]
class CvExperiencia extends Model
{
    /** @use HasFactory<CvExperienciaFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'actual' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Alumno, $this>
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
