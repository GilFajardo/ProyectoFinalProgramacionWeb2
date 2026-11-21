<?php

namespace App\Filament\Widgets;

use App\Services\EstadisticaService;
use Filament\Widgets\ChartWidget;

/**
 * Tendencia de postulaciones de los últimos seis meses.
 */
class PostulacionesPorMesChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Postulaciones de los últimos 6 meses';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $datos = app(EstadisticaService::class)->postulacionesPorMes(6);

        return [
            'datasets' => [
                [
                    'label' => 'Postulaciones',
                    'data' => array_values($datos),
                    'borderColor' => '#0b3c7a',
                    'backgroundColor' => 'rgba(11, 60, 122, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => array_keys($datos),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
