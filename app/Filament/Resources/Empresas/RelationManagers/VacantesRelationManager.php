<?php

namespace App\Filament\Resources\Empresas\RelationManagers;

use App\Filament\Resources\Vacantes\VacanteResource;
use App\Models\Vacante;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Vacantes publicadas por la empresa (solo consulta; se gestionan en el recurso Vacantes).
 */
class VacantesRelationManager extends RelationManager
{
    protected static string $relationship = 'vacantes';

    protected static ?string $title = 'Vacantes de la empresa';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('categoria')->withCount('postulaciones'))
            ->defaultSort('fecha_inicio', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Vacante')
                    ->searchable(),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría'),
                TextColumn::make('fecha_fin')
                    ->label('Vigente hasta')
                    ->date('d/m/Y'),
                TextColumn::make('estado_vigencia')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Vacante::etiquetaVigencia($state))
                    ->color(fn (string $state): string => Vacante::colorVigencia($state)),
                TextColumn::make('postulaciones_count')
                    ->label('Postulaciones')
                    ->alignCenter(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Vacante $record): string => VacanteResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
