<?php

namespace App\Filament\Resources\Postulaciones\Tables;

use App\Enums\EstatusPostulacion;
use App\Filament\Actions\PostulacionActions;
use App\Models\Postulacion;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PostulacionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['alumno.user', 'vacante.empresa']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->description(fn (Postulacion $record): string => $record->alumno->matricula),
                TextColumn::make('vacante.titulo')
                    ->label('Vacante')
                    ->searchable()
                    ->wrap()
                    ->description(fn (Postulacion $record): string => $record->vacante?->empresa?->nombre ?? '—'),
                TextColumn::make('alumno.carrera')
                    ->label('Carrera')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estatus')
                    ->badge()
                    ->sortable(),
                TextColumn::make('estatus_actualizado_en')
                    ->label('Actualizado')
                    ->since()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estatus')
                    ->options(EstatusPostulacion::class),
                SelectFilter::make('empresa')
                    ->label('Empresa')
                    ->relationship('vacante.empresa', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->relationship('vacante.categoria', 'nombre')
                    ->preload(),
                Filter::make('fecha')
                    ->schema([
                        DatePicker::make('desde')->label('Postulado desde'),
                        DatePicker::make('hasta')->label('Postulado hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['desde'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('created_at', '>=', $fecha))
                        ->when($data['hasta'] ?? null, fn (Builder $q, $fecha) => $q->whereDate('created_at', '<=', $fecha))),
            ])
            ->recordActions([
                ViewAction::make(),
                PostulacionActions::cambiarEstatus(),
                PostulacionActions::descargarCv(),
            ]);
    }
}
