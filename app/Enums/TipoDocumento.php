<?php

namespace App\Enums;

use App\Enums\Concerns\TieneOpciones;
use Filament\Support\Contracts\HasLabel;

/**
 * Tipo de documento probatorio adjunto al CV.
 */
enum TipoDocumento: string implements HasLabel
{
    use TieneOpciones;

    case Certificado = 'certificado';
    case Constancia = 'constancia';
    case CartaRecomendacion = 'carta_recomendacion';
    case Titulo = 'titulo';
    case CvPdf = 'cv_pdf';
    case Otro = 'otro';

    public function getLabel(): string
    {
        return match ($this) {
            self::Certificado => 'Certificado',
            self::Constancia => 'Constancia',
            self::CartaRecomendacion => 'Carta de recomendación',
            self::Titulo => 'Título / Cédula',
            self::CvPdf => 'CV en PDF',
            self::Otro => 'Otro',
        };
    }
}
