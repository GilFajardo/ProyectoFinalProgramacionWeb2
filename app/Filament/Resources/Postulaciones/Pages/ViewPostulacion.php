<?php

namespace App\Filament\Resources\Postulaciones\Pages;

use App\Filament\Actions\PostulacionActions;
use App\Filament\Resources\Postulaciones\PostulacionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPostulacion extends ViewRecord
{
    protected static string $resource = PostulacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PostulacionActions::descargarCv(),
            PostulacionActions::cambiarEstatus(),
        ];
    }
}
