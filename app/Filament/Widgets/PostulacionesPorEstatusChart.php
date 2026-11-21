<?php

namespace App\Filament\Widgets;

use App\Services\EstadisticaService;
use Filament\Widgets\ChartWidget;

/**
 * Distribución de postulaciones por estatus.
 */
class PostulacionesPorEstatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Postulaciones por estatus';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $datos = app(EstadisticaService::class)->postulacionesPorEstatus();

        return [
            'datasets' => [
                [
                    'data' => array_values($datos),
                    'backgroundColor' => ['#f59e0b', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => array_keys($datos),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'scales' => ['x' => ['display' => false], 'y' => ['display' => false]],
        ];
    }
}
