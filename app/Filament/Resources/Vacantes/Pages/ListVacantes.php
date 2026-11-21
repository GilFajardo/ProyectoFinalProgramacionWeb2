<?php

namespace App\Filament\Resources\Vacantes\Pages;

use App\Filament\Actions\ReportePdfAction;
use App\Filament\Resources\Vacantes\VacanteResource;
use App\Models\Vacante;
use App\Services\ReporteService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVacantes extends ListRecords
{
    protected static string $resource = VacanteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReportePdfAction::make(ReporteService::VACANTES),
            CreateAction::make(),
        ];
    }

    /**
     * Pestañas rápidas por estado de vigencia.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = ['todas' => Tab::make('Todas')];

        foreach (Vacante::opcionesVigencia() as $vigencia => $etiqueta) {
            $tabs[$vigencia] = Tab::make($etiqueta)
                ->modifyQueryUsing(fn (Builder $query) => $query->conVigencia($vigencia));
        }

        return $tabs;
    }
}
