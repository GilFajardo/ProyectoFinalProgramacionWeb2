<?php

namespace App\Filament\Widgets;

use App\Services\EstadisticaService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Indicadores principales de la bolsa de trabajo.
 */
class EstadisticasGenerales extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $servicio = app(EstadisticaService::class);
        $resumen = $servicio->resumen();
        $tendencia = array_values($servicio->postulacionesPorMes(6));

        return [
            Stat::make('Empresas activas', $resumen['empresas'])
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->description('Registradas en la plataforma'),
            Stat::make('Vacantes vigentes', $resumen['vacantes_vigentes'])
                ->icon(Heroicon::OutlinedBriefcase)
                ->description('De '.$resumen['vacantes_total'].' publicadas en total')
                ->color('success'),
            Stat::make('Alumnos registrados', $resumen['alumnos'])
                ->icon(Heroicon::OutlinedAcademicCap)
                ->description('Con perfil y CV en línea'),
            Stat::make('Postulaciones', $resumen['postulaciones'])
                ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                ->description($resumen['tasa_aceptacion'].'% aceptadas')
                ->chart($tendencia)
                ->color('primary'),
        ];
    }
}
