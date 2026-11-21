<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasLabel;

/**
 * Modalidad de trabajo de una vacante.
 */
enum Modalidad: string implements HasLabel
{
    use TieneOpciones;

    case Presencial = 'presencial';
    case Remoto = 'remoto';
    case Hibrido = 'hibrido';

    public function getLabel(): string
    {
        return match ($this) {
            self::Presencial => 'Presencial',
            self::Remoto => 'Remoto',
            self::Hibrido => 'Híbrido',
        };
    }
}
