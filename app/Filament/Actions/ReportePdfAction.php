<?php

namespace App\Filament\Actions;

use App\Enums\EstatusPostulacion;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Vacante;
use App\Services\ReporteService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Acción de Filament que abre un modal con filtros y descarga un reporte PDF.
 *
 * Uso: ReportePdfAction::make(ReporteService::VACANTES)
 */
class ReportePdfAction
{
    public static function make(string $tipo, ?string $nombre = null): Action
    {
        return Action::make($nombre ?? 'reportePdf'.ucfirst($tipo))
            ->label('Reporte PDF de '.mb_strtolower(ReporteService::tipos()[$tipo]))
            ->icon(Heroicon::OutlinedDocumentArrowDown)
            ->color('gray')
            ->modalHeading('Generar reporte PDF de '.mb_strtolower(ReporteService::tipos()[$tipo]))
            ->modalDescription('Deja los filtros vacíos para incluir todos los registros.')
            ->modalSubmitActionLabel('Descargar PDF')
            ->schema(self::filtros($tipo))
            ->action(function (array $data) use ($tipo): StreamedResponse {
                $servicio = app(ReporteService::class);
                $pdf = $servicio->generar($tipo, array_filter($data, 'filled'));

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    $servicio->nombreArchivo($tipo),
                    ['Content-Type' => 'application/pdf'],
                );
            });
    }

    /**
     * Campos de filtro disponibles según el tipo de reporte.
     *
     * @return list<Select>
     */
    public static function filtros(string $tipo): array
    {
        $empresa = Select::make('empresa_id')
            ->label('Empresa')
            ->options(fn (): array => Empresa::orderBy('nombre')->pluck('nombre', 'id')->all())
            ->searchable();

        return match ($tipo) {
            ReporteService::VACANTES => [
                $empresa,
                Select::make('categoria_id')
                    ->label('Categoría')
                    ->options(fn (): array => Categoria::orderBy('nombre')->pluck('nombre', 'id')->all()),
                Select::make('vigencia')
                    ->label('Vigencia')
                    ->options(Vacante::opcionesVigencia()),
            ],
            ReporteService::POSTULACIONES => [
                $empresa->live(),
                Select::make('vacante_id')
                    ->label('Vacante')
                    ->options(fn (Get $get): array => Vacante::query()
                        ->when($get('empresa_id'), fn ($q, $id) => $q->where('empresa_id', $id))
                        ->orderBy('titulo')
                        ->pluck('titulo', 'id')
                        ->all())
                    ->searchable(),
                Select::make('estatus')
                    ->label('Estatus')
                    ->options(EstatusPostulacion::class),
            ],
            default => [],
        };
    }
}
