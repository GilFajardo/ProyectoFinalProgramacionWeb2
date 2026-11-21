<?php

namespace App\Filament\Resources\Vacantes\Schemas;

use App\Models\Vacante;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VacanteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Vacante')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('titulo')->weight('bold')->columnSpanFull(),
                        TextEntry::make('categoria.nombre')->label('Área de conocimiento')->badge(),
                        TextEntry::make('tipo_contrato')->label('Contratación')->badge(),
                        TextEntry::make('descripcion')->label('Descripción')->columnSpanFull()->prose(),
                        TextEntry::make('requisitos')->placeholder('—')->prose(),
                        TextEntry::make('prestaciones')->placeholder('—')->prose(),
                    ]),
                Section::make('Publicación')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('estado_vigencia')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => Vacante::etiquetaVigencia($state))
                            ->color(fn (string $state): string => Vacante::colorVigencia($state)),
                        TextEntry::make('empresa.nombre')->label('Empresa'),
                        TextEntry::make('reclutador.user.name')->label('Reclutador')->placeholder('—'),
                        TextEntry::make('vigencia')
                            ->label('Vigencia')
                            ->state(fn (Vacante $record): string => $record->fecha_inicio->format('d/m/Y').' al '.$record->fecha_fin->format('d/m/Y')),
                        TextEntry::make('modalidad')->badge(),
                        TextEntry::make('ubicacion')->label('Ubicación')->placeholder('—'),
                        TextEntry::make('salario_formateado')->label('Salario'),
                        TextEntry::make('plazas'),
                        TextEntry::make('slug')->label('URL pública')->color('gray')->copyable(),
                        TextEntry::make('deleted_at')->label('Dada de baja')->dateTime('d/m/Y')->visible(fn (Vacante $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}
