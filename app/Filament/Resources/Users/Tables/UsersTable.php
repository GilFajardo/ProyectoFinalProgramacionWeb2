<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\Rol;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['reclutador.empresa', 'alumno']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->sortable(),
                TextColumn::make('detalle')
                    ->label('Empresa / matrícula')
                    ->state(fn (User $record): ?string => match ($record->role) {
                        Rol::Reclutador => $record->reclutador?->empresa?->nombre ?? 'Sin empresa',
                        Rol::Alumno => $record->alumno?->matricula,
                        default => null,
                    })
                    ->placeholder('—'),
                ToggleColumn::make('activo')
                    ->label('Activa')
                    ->disabled(fn (User $record): bool => $record->is(auth()->user())),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options(Rol::class),
                TernaryFilter::make('activo')
                    ->label('Cuenta activa'),
                TernaryFilter::make('sin_empresa')
                    ->label('Reclutadores sin empresa')
                    ->queries(
                        true: fn (Builder $query) => $query->where('role', Rol::Reclutador)->whereHas('reclutador', fn (Builder $q) => $q->whereNull('empresa_id')),
                        false: fn (Builder $query) => $query->where('role', Rol::Reclutador)->whereHas('reclutador', fn (Builder $q) => $q->whereNotNull('empresa_id')),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->is(auth()->user())),
            ]);
    }
}
