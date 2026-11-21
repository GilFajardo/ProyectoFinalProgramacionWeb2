<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasLabel;

/**
 * Nivel de un registro de formación académica del CV.
 */
enum NivelFormacion: string implements HasLabel
{
    use TieneOpciones;

    case Bachillerato = 'bachillerato';
    case Tsu = 'tsu';
    case Licenciatura = 'licenciatura';
    case Maestria = 'maestria';
    case Diplomado = 'diplomado';
    case Curso = 'curso';
    case Certificacion = 'certificacion';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bachillerato => 'Bachillerato',
            self::Tsu => 'Técnico Superior Universitario',
            self::Licenciatura => 'Licenciatura / Ingeniería',
            self::Maestria => 'Maestría',
            self::Diplomado => 'Diplomado',
            self::Curso => 'Curso',
            self::Certificacion => 'Certificación',
        };
    }
}
