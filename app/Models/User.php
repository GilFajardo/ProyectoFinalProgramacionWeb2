<?php

namespace App\Models;

use App\Enums\Rol;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario autenticable de la plataforma. Su rol determina a qué área accede:
 * administrador (panel Filament), reclutador o alumno (portales Blade).
 *
 * Nota de seguridad: "role" es asignable para que el administrador lo gestione
 * desde Filament, pero los Form Requests públicos nunca lo incluyen en validated().
 */
#[Fillable(['name', 'email', 'password', 'role', 'activo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Rol::class,
            'activo' => 'boolean',
        ];
    }

    /**
     * Perfil de alumno (solo existe si el rol es alumno).
     *
     * @return HasOne<Alumno, $this>
     */
    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    /**
     * Perfil de reclutador (solo existe si el rol es reclutador).
     *
     * @return HasOne<Reclutador, $this>
     */
    public function reclutador(): HasOne
    {
        return $this->hasOne(Reclutador::class);
    }

    public function tieneRol(Rol $rol): bool
    {
        return $this->role === $rol;
    }

    public function esAdmin(): bool
    {
        return $this->tieneRol(Rol::Admin);
    }

    public function esReclutador(): bool
    {
        return $this->tieneRol(Rol::Reclutador);
    }

    public function esAlumno(): bool
    {
        return $this->tieneRol(Rol::Alumno);
    }

    /**
     * Solo los administradores activos pueden entrar al panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->esAdmin() && $this->activo;
    }
}
