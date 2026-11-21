<?php

namespace App\Filament\Pages;

use App\Filament\Actions\ReportePdfAction;
use App\Services\ReporteService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Centro de reportes PDF institucionales (vacantes, postulaciones y empresas).
 */
class Reportes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Reportes';

    protected static ?string $navigationLabel = 'Reportes PDF';

    protected static ?string $title = 'Reportes PDF';

    protected ?string $subheading = 'Genera reportes imprimibles para analizar la información de la bolsa de trabajo.';

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'lg' => 3])
                    ->schema([
                        $this->tarjeta(
                            'Vacantes',
                            Heroicon::OutlinedBriefcase,
                            'Listado de ofertas con su periodo de vigencia, estado, plazas y número de postulaciones. Filtra por empresa, categoría o vigencia.',
                            ReporteService::VACANTES,
                        ),
                        $this->tarjeta(
                            'Postulaciones',
                            Heroicon::OutlinedClipboardDocumentCheck,
                            'Candidatos postulados con su matrícula, carrera, vacante y estatus (EN PROCESO, ACEPTADO o RECHAZADO).',
                            ReporteService::POSTULACIONES,
                        ),
                        $this->tarjeta(
                            'Empresas',
                            Heroicon::OutlinedBuildingOffice2,
                            'Empresas registradas con sus reclutadores, vacantes vigentes y postulaciones recibidas.',
                            ReporteService::EMPRESAS,
                        ),
                    ]),
            ]);
    }

    private function tarjeta(string $titulo, Heroicon $icono, string $descripcion, string $tipo): Section
    {
        return Section::make($titulo)
            ->icon($icono)
            ->schema([
                Text::make($descripcion),
                Actions::make([
                    ReportePdfAction::make($tipo)
                        ->label('Generar PDF')
                        ->color('primary'),
                ]),
            ]);
    }
}
