<?php

namespace App\Models;

use Database\Factories\AlumnoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Perfil de alumno (solicitante). Contiene los datos generales del CV
 * y se relaciona con sus secciones estructuradas y sus postulaciones.
 */
#[Fillable([
    'user_id', 'matricula', 'carrera', 'cuatrimestre', 'telefono',
    'fecha_nacimiento', 'ciudad', 'resumen', 'linkedin_url',
    'portafolio_url', 'foto_path',
])]
class Alumno extends Model
{
    /** @use HasFactory<AlumnoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'cuatrimestre' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Postulacion, $this>
     */
    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class);
    }

    /**
     * Vacantes a las que el alumno se ha postulado.
     *
     * @return BelongsToMany<Vacante, $this>
     */
    public function vacantesPostuladas(): BelongsToMany
    {
        return $this->belongsToMany(Vacante::class, 'postulaciones')
            ->withPivot(['estatus', 'created_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<CvExperiencia, $this>
     */
    public function experiencias(): HasMany
    {
        return $this->hasMany(CvExperiencia::class)->orderByDesc('fecha_inicio');
    }

    /**
     * @return HasMany<CvFormacion, $this>
     */
    public function formaciones(): HasMany
    {
        return $this->hasMany(CvFormacion::class)->orderByDesc('fecha_inicio');
    }

    /**
     * @return HasMany<CvHabilidad, $this>
     */
    public function habilidades(): HasMany
    {
        return $this->hasMany(CvHabilidad::class);
    }

    /**
     * @return HasMany<CvDocumento, $this>
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(CvDocumento::class);
    }

    /**
     * Indica si el alumno ya está postulado a la vacante indicada.
     */
    public function yaSePostuloA(Vacante $vacante): bool
    {
        return $this->postulaciones()->whereBelongsTo($vacante)->exists();
    }
}
