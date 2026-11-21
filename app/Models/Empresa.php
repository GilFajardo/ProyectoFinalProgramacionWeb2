<?php

namespace App\Models;

use Database\Factories\EmpresaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Empresa registrada. Tiene uno o más reclutadores y muchas vacantes.
 */
#[Fillable([
    'nombre', 'razon_social', 'rfc', 'sector', 'descripcion', 'sitio_web',
    'email_contacto', 'telefono', 'direccion', 'municipio', 'estado',
    'logo_path', 'verificada', 'activa',
])]
class Empresa extends Model
{
    /** @use HasFactory<EmpresaFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verificada' => 'boolean',
            'activa' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Reclutador, $this>
     */
    public function reclutadores(): HasMany
    {
        return $this->hasMany(Reclutador::class);
    }

    /**
     * @return HasMany<Vacante, $this>
     */
    public function vacantes(): HasMany
    {
        return $this->hasMany(Vacante::class);
    }

    /**
     * Todas las postulaciones recibidas por las vacantes de la empresa.
     *
     * @return HasManyThrough<Postulacion, Vacante, $this>
     */
    public function postulaciones(): HasManyThrough
    {
        return $this->hasManyThrough(Postulacion::class, Vacante::class);
    }

    #[Scope]
    protected function activas(Builder $query): void
    {
        $query->where('activa', true);
    }
}
