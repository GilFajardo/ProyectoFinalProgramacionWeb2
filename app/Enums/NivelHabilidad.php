<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasLabel;

/**
 * Nivel de dominio de una habilidad del CV.
 */
enum NivelHabilidad: string implements HasLabel
{
    use TieneOpciones;

    case Basico = 'basico';
    case Intermedio = 'intermedio';
    case Avanzado = 'avanzado';
    case Experto = 'experto';

    public function getLabel(): string
    {
        return match ($this) {
            self::Basico => 'Básico',
            self::Intermedio => 'Intermedio',
            self::Avanzado => 'Avanzado',
            self::Experto => 'Experto',
        };
    }

    /**
     * Porcentaje aproximado para representar el nivel en una barra de progreso.
     */
    public function porcentaje(): int
    {
        return match ($this) {
            self::Basico => 25,
            self::Intermedio => 50,
            self::Avanzado => 75,
            self::Experto => 100,
        };
    }
}
