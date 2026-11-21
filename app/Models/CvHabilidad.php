<?php

namespace App\Models;

use App\Enums\NivelHabilidad;
use Database\Factories\CvHabilidadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CV: habilidad del alumno, clasificada por área de conocimiento.
 */
#[Table('cv_habilidades')]
#[Fillable(['alumno_id', 'categoria_id', 'nombre', 'nivel'])]
class CvHabilidad extends Model
{
    /** @use HasFactory<CvHabilidadFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nivel' => NivelHabilidad::class,
        ];
    }

    /**
     * @return BelongsTo<Alumno, $this>
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * @return BelongsTo<Categoria, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
