<?php

namespace App\Models;

use App\Enums\NivelFormacion;
use Database\Factories\CvFormacionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CV: formación académica, curso o certificación del alumno.
 */
#[Table('cv_formaciones')]
#[Fillable(['alumno_id', 'institucion', 'titulo', 'nivel', 'fecha_inicio', 'fecha_fin', 'en_curso'])]
class CvFormacion extends Model
{
    /** @use HasFactory<CvFormacionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nivel' => NivelFormacion::class,
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'en_curso' => 'boolean',
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
