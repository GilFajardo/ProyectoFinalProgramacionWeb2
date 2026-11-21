<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Rol;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Cuenta')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->rule(Password::defaults())
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Déjala vacía para conservar la actual.' : null),
                        Select::make('role')
                            ->label('Rol')
                            ->options(Rol::class)
                            ->required()
                            ->live()
                            ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false)
                            ->helperText(fn (?User $record): ?string => $record?->is(auth()->user()) ? 'No puedes cambiar tu propio rol.' : null),
                        Toggle::make('activo')
                            ->label('Cuenta activa')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false),
                    ]),

                Section::make('Perfil de reclutador')
                    ->description('Vincula al reclutador con la empresa que representa.')
                    ->relationship('reclutador')
                    ->visible(fn (Get $get): bool => self::rol($get) === Rol::Reclutador)
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('empresa_id')
                            ->label('Empresa')
                            ->relationship('empresa', 'nombre')
                            ->searchable()
                            ->preload(),
                        TextInput::make('puesto')
                            ->maxLength(100),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                    ]),

                Section::make('Perfil de alumno')
                    ->relationship('alumno')
                    ->visible(fn (Get $get): bool => self::rol($get) === Rol::Alumno)
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('matricula')
                            ->label('Matrícula')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Select::make('carrera')
                            ->options(fn (): array => array_combine(config('upslp.carreras'), config('upslp.carreras')))
                            ->required(),
                        TextInput::make('cuatrimestre')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(10),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('ciudad')
                            ->maxLength(100),
                    ]),
            ]);
    }

    /**
     * El estado del select puede ser el Enum (registro existente) o su valor (formulario).
     */
    private static function rol(Get $get): ?Rol
    {
        $rol = $get('role');

        return $rol instanceof Rol ? $rol : Rol::tryFrom((string) $rol);
    }
}
