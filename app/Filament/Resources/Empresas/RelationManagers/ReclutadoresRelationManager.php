<?php

namespace App\Filament\Resources\Empresas\RelationManagers;

use App\Models\Reclutador;
use Filament\Actions\AssociateAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Vínculo empresa–reclutador: permite asociar reclutadores sin empresa
 * o desvincularlos. Los usuarios se crean en el recurso Usuarios.
 */
class ReclutadoresRelationManager extends RelationManager
{
    protected static string $relationship = 'reclutadores';

    protected static ?string $title = 'Reclutadores vinculados';

    protected static ?string $modelLabel = 'reclutador';

    protected static ?string $pluralModelLabel = 'reclutadores';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('puesto')
                    ->maxLength(100),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(20),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->recordTitle(fn (Reclutador $record): string => $record->user->name.' ('.$record->user->email.')')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Correo')
                    ->copyable(),
                TextColumn::make('puesto')
                    ->placeholder('—'),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->placeholder('—'),
                IconColumn::make('user.activo')
                    ->label('Cuenta activa')
                    ->boolean(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Vincular reclutador')
                    ->modalHeading('Vincular un reclutador sin empresa')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereNull('empresa_id')->with('user')),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make()
                    ->label('Desvincular'),
            ]);
    }
}
