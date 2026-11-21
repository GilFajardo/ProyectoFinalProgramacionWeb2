<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Estatus de una postulación de un alumno a una vacante.
 */
enum EstatusPostulacion: string implements HasColor, HasLabel
{
    use TieneOpciones;

    case EnProceso = 'en_proceso';
    case Aceptado = 'aceptado';
    case Rechazado = 'rechazado';

    public function getLabel(): string
    {
        return match ($this) {
            self::EnProceso => 'EN PROCESO',
            self::Aceptado => 'ACEPTADO',
            self::Rechazado => 'RECHAZADO',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::EnProceso => 'warning',
            self::Aceptado => 'success',
            self::Rechazado => 'danger',
        };
    }

    /**
     * Clases de Tailwind para mostrar el estatus como insignia en Blade.
     */
    public function clasesBadge(): string
    {
        return match ($this) {
            self::EnProceso => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            self::Aceptado => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            self::Rechazado => 'bg-red-100 text-red-800 ring-red-600/20',
        };
    }
}
