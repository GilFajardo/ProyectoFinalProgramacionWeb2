<?php

namespace App\Filament\Resources\Vacantes\RelationManagers;

use App\Enums\EstatusPostulacion;
use App\Filament\Actions\PostulacionActions;
use App\Models\Postulacion;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Candidatos postulados a la vacante, con cambio de estatus y descarga de CV.
 */
class PostulacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'postulaciones';

    protected static ?string $title = 'Candidatos postulados';

    protected static ?string $modelLabel = 'postulación';

    protected static ?string $pluralModelLabel = 'postulaciones';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('alumno.user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->description(fn (Postulacion $record): string => $record->alumno->user->email),
                TextColumn::make('alumno.matricula')
                    ->label('Matrícula')
                    ->searchable(),
                TextColumn::make('alumno.carrera')
                    ->label('Carrera')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Postulación')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estatus')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('estatus')
                    ->options(EstatusPostulacion::class),
            ])
            ->recordActions([
                PostulacionActions::cambiarEstatus(),
                PostulacionActions::descargarCv(),
            ]);
    }
}
