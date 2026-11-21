<?php

namespace App\Filament\Widgets;

use App\Services\EstadisticaService;
use Filament\Widgets\ChartWidget;

/**
 * Vacantes por área de conocimiento (totales vs. vigentes).
 */
class VacantesPorCategoriaChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Vacantes por área de conocimiento';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $servicio = app(EstadisticaService::class);
        $totales = $servicio->vacantesPorCategoria();
        $vigentes = $servicio->vacantesPorCategoria(soloVigentes: true);

        return [
            'datasets' => [
                [
                    'label' => 'Vigentes',
                    'data' => array_values($vigentes),
                    'backgroundColor' => '#0b3c7a',
                ],
                [
                    'label' => 'Totales',
                    'data' => array_values($totales),
                    'backgroundColor' => '#93c5fd',
                ],
            ],
            'labels' => array_keys($totales),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
