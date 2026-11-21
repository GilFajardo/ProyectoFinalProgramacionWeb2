<?php

namespace App\Models;

use App\Enums\TipoDocumento;
use Database\Factories\CvDocumentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * CV: documento probatorio del alumno. El archivo vive en el disco privado
 * "local" y solo se descarga a través de un controlador protegido por Policy.
 */
#[Fillable(['alumno_id', 'nombre', 'tipo', 'archivo_path', 'mime', 'tamano_kb'])]
class CvDocumento extends Model
{
    /** @use HasFactory<CvDocumentoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoDocumento::class,
            'tamano_kb' => 'integer',
        ];
    }

    /**
     * Elimina el archivo físico cuando se borra el registro.
     */
    protected static function booted(): void
    {
        static::deleted(function (CvDocumento $documento): void {
            Storage::disk('local')->delete($documento->archivo_path);
        });
    }

    /**
     * @return BelongsTo<Alumno, $this>
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
