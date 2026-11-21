<?php

namespace App\Filament\Resources\Vacantes\Schemas;

use App\Enums\Modalidad;
use App\Enums\TipoContrato;
use App\Models\Reclutador;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class VacanteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Descripción de la vacante')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título del puesto')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Select::make('categoria_id')
                            ->label('Área de conocimiento')
                            ->relationship('categoria', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('tipo_contrato')
                            ->label('Tipo de contratación')
                            ->options(TipoContrato::class)
                            ->required(),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->rows(5)
                            ->maxLength(10000)
                            ->columnSpanFull(),
                        Textarea::make('requisitos')
                            ->rows(4)
                            ->maxLength(5000),
                        Textarea::make('prestaciones')
                            ->rows(4)
                            ->maxLength(5000),
                    ]),

                Section::make('Empresa y vigencia')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('empresa_id')
                            ->label('Empresa')
                            ->relationship('empresa', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('reclutador_id', null)),
                        Select::make('reclutador_id')
                            ->label('Reclutador responsable')
                            ->options(fn (Get $get): array => Reclutador::query()
                                ->where('empresa_id', $get('empresa_id'))
                                ->with('user')
                                ->get()
                                ->mapWithKeys(fn (Reclutador $r): array => [$r->id => $r->user->name])
                                ->all())
                            ->disabled(fn (Get $get): bool => blank($get('empresa_id'))),
                        DatePicker::make('fecha_inicio')
                            ->label('Inicio de vigencia')
                            ->required()
                            ->default(today())
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('fecha_fin')
                            ->label('Fin de vigencia')
                            ->required()
                            ->default(today()->addDays(30))
                            ->afterOrEqual('fecha_inicio')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Toggle::make('activa')
                            ->label('Publicada')
                            ->helperText('Desactívala para ocultarla sin borrarla.')
                            ->default(true),
                    ]),

                Section::make('Condiciones')
                    ->columns(3)
                    ->columnSpan(2)
                    ->schema([
                        Select::make('modalidad')
                            ->options(Modalidad::class)
                            ->default(Modalidad::Presencial)
                            ->required(),
                        TextInput::make('ubicacion')
                            ->label('Ubicación')
                            ->maxLength(150),
                        TextInput::make('plazas')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(999)
                            ->default(1)
                            ->required(),
                        TextInput::make('salario_min')
                            ->label('Salario mínimo mensual')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        TextInput::make('salario_max')
                            ->label('Salario máximo mensual')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->gte('salario_min'),
                    ]),
            ]);
    }
}
