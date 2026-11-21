<?php

namespace App\Models;

use Database\Factories\CategoriaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Área de conocimiento / categoría de vacantes (catálogo de 12 registros).
 */
#[Fillable(['nombre', 'slug', 'descripcion', 'icono'])]
class Categoria extends Model
{
    /** @use HasFactory<CategoriaFactory> */
    use HasFactory;

    /**
     * @return HasMany<Vacante, $this>
     */
    public function vacantes(): HasMany
    {
        return $this->hasMany(Vacante::class);
    }

    /**
     * @return HasMany<CvHabilidad, $this>
     */
    public function habilidades(): HasMany
    {
        return $this->hasMany(CvHabilidad::class);
    }
}
