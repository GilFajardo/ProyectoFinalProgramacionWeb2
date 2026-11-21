<?php

namespace App\Filament\Resources\Vacantes\Pages;

use App\Filament\Resources\Vacantes\VacanteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVacante extends ViewRecord
{
    protected static string $resource = VacanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
