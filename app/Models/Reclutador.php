<?php

namespace App\Models;

use Database\Factories\ReclutadorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Perfil de reclutador: vincula a un usuario con la empresa que representa.
 */
#[Table('reclutadores')]
#[Fillable(['user_id', 'empresa_id', 'puesto', 'telefono'])]
class Reclutador extends Model
{
    /** @use HasFactory<ReclutadorFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Empresa, $this>
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Vacantes creadas por este reclutador.
     *
     * @return HasMany<Vacante, $this>
     */
    public function vacantes(): HasMany
    {
        return $this->hasMany(Vacante::class);
    }

    /**
     * Un reclutador solo puede publicar vacantes si está vinculado a una empresa activa.
     */
    public function puedePublicar(): bool
    {
        return $this->empresa !== null && $this->empresa->activa;
    }
}
