<?php

namespace App\Filament\Actions;

use App\Enums\EstatusPostulacion;
use App\Models\Postulacion;
use App\Services\ReporteService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Acciones de Filament reutilizadas en las tablas de postulaciones.
 */
class PostulacionActions
{
    /**
     * Cambia el estatus de la postulación (ACEPTADO / RECHAZADO / EN PROCESO).
     */
    public static function cambiarEstatus(): Action
    {
        return Action::make('cambiarEstatus')
            ->label('Cambiar estatus')
            ->icon(Heroicon::OutlinedArrowPath)
            ->modalHeading(fn (Postulacion $record): string => 'Estatus de '.$record->alumno->user->name)
            ->fillForm(fn (Postulacion $record): array => [
                'estatus' => $record->estatus,
                'comentario_reclutador' => $record->comentario_reclutador,
            ])
            ->schema([
                ToggleButtons::make('estatus')
                    ->options(EstatusPostulacion::class)
                    ->inline()
                    ->required(),
                Textarea::make('comentario_reclutador')
                    ->label('Comentario para el alumno')
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->action(function (Postulacion $record, array $data): void {
                $estatus = $data['estatus'] instanceof EstatusPostulacion
                    ? $data['estatus']
                    : EstatusPostulacion::from($data['estatus']);

                $record->cambiarEstatus($estatus, $data['comentario_reclutador'] ?? null);

                Notification::make()->title('Estatus actualizado')->success()->send();
            });
    }

    /**
     * Descarga el CV estructurado del alumno en PDF.
     */
    public static function descargarCv(): Action
    {
        return Action::make('descargarCv')
            ->label('CV en PDF')
            ->icon(Heroicon::OutlinedDocumentText)
            ->color('gray')
            ->action(function (Postulacion $record): StreamedResponse {
                $pdf = app(ReporteService::class)->cv($record->alumno);

                return response()->streamDownload(
                    fn () => print ($pdf->output()),
                    'cv-'.$record->alumno->matricula.'.pdf',
                    ['Content-Type' => 'application/pdf'],
                );
            });
    }
}
