<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Postulaciones\PostulacionResource;
use App\Models\Postulacion;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Las postulaciones más recientes para seguimiento rápido.
 */
class UltimasPostulaciones extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Últimas postulaciones';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Postulacion::query()->with(['alumno.user', 'vacante.empresa'])->latest())
            ->paginated([5])
            ->columns([
                TextColumn::make('alumno.user.name')->label('Alumno'),
                TextColumn::make('vacante.titulo')->label('Vacante'),
                TextColumn::make('vacante.empresa.nombre')->label('Empresa'),
                TextColumn::make('created_at')->label('Fecha')->since(),
                TextColumn::make('estatus')->badge(),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Ver')
                    ->url(fn (Postulacion $record): string => PostulacionResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
