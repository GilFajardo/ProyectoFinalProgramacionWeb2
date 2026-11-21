<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasLabel;

/**
 * Tipo de contratación ofrecida en una vacante.
 */
enum TipoContrato: string implements HasLabel
{
    use TieneOpciones;

    case TiempoCompleto = 'tiempo_completo';
    case MedioTiempo = 'medio_tiempo';
    case Practicas = 'practicas';
    case Estadia = 'estadia';
    case ServicioSocial = 'servicio_social';
    case Proyecto = 'proyecto';

    public function getLabel(): string
    {
        return match ($this) {
            self::TiempoCompleto => 'Tiempo completo',
            self::MedioTiempo => 'Medio tiempo',
            self::Practicas => 'Prácticas profesionales',
            self::Estadia => 'Estadía',
            self::ServicioSocial => 'Servicio social',
            self::Proyecto => 'Por proyecto',
        };
    }
}
