<?php

namespace App\Filament\Resources\Vacantes\Tables;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Models\Vacante;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VacantesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['empresa', 'categoria'])->withCount('postulaciones'))
            ->defaultSort('fecha_inicio', 'desc')
            ->columns([
                TextColumn::make('titulo')
                    ->label('Vacante')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Vacante $record): string => $record->empresa?->nombre ?? '—')
                    ->wrap(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categoria.nombre')
                    ->label('Categoría')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tipo_contrato')
                    ->label('Contratación')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('modalidad')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estado_vigencia')
                    ->label('Vigencia')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Vacante::etiquetaVigencia($state))
                    ->color(fn (string $state): string => Vacante::colorVigencia($state)),
                TextColumn::make('postulaciones_count')
                    ->label('Postulaciones')
                    ->sortable()
                    ->alignCenter(),
                ToggleColumn::make('activa')
                    ->label('Publicada'),
            ])
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->preload(),
                SelectFilter::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tipo_contrato')
                    ->label('Contratación')
                    ->options(TipoContrato::class),
                SelectFilter::make('modalidad')
                    ->options(Modalidad::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('extenderVigencia')
                        ->label('Extender vigencia')
                        ->icon(Heroicon::OutlinedCalendarDays)
                        ->schema([
                            DatePicker::make('fecha_fin')
                                ->label('Nueva fecha de fin')
                                ->required()
                                ->native(false)
                                ->minDate(fn (Vacante $record) => $record->fecha_inicio)
                                ->default(fn (Vacante $record) => $record->fecha_fin->max(today())->addDays(15)),
                        ])
                        ->action(function (Vacante $record, array $data): void {
                            $record->update(['fecha_fin' => $data['fecha_fin'], 'activa' => true]);

                            Notification::make()->title('Vigencia actualizada')->success()->send();
                        }),
                    DeleteAction::make()->label('Dar de baja'),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('desactivar')
                        ->label('Despublicar seleccionadas')
                        ->icon(Heroicon::OutlinedEyeSlash)
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['activa' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make()->label('Dar de baja'),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
