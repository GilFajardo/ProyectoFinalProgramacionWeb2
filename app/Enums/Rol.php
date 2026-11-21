<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Roles de usuario de la plataforma Vinculación en Línea.
 */
enum Rol: string implements HasColor, HasLabel
{
    use TieneOpciones;

    case Admin = 'admin';
    case Reclutador = 'reclutador';
    case Alumno = 'alumno';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Reclutador => 'Reclutador',
            self::Alumno => 'Alumno',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Reclutador => 'info',
            self::Alumno => 'success',
        };
    }

    /**
     * Nombre de la ruta del panel de inicio correspondiente al rol.
     */
    public function rutaInicio(): string
    {
        return match ($this) {
            self::Admin => 'filament.admin.pages.dashboard',
            self::Reclutador => 'reclutador.panel',
            self::Alumno => 'alumno.panel',
        };
    }
}
