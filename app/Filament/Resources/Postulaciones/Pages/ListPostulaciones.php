<?php

namespace App\Filament\Resources\Postulaciones\Pages;

use App\Enums\EstatusPostulacion;
use App\Filament\Actions\ReportePdfAction;
use App\Filament\Resources\Postulaciones\PostulacionResource;
use App\Services\ReporteService;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPostulaciones extends ListRecords
{
    protected static string $resource = PostulacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReportePdfAction::make(ReporteService::POSTULACIONES),
        ];
    }

    /**
     * Pestañas rápidas por estatus.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = ['todas' => Tab::make('Todas')];

        foreach (EstatusPostulacion::cases() as $estatus) {
            $tabs[$estatus->value] = Tab::make($estatus->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('estatus', $estatus));
        }

        return $tabs;
    }
}
