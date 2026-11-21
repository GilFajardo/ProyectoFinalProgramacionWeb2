<?php

namespace App\Filament\Resources\Empresas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmpresaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Datos generales')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre comercial')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('razon_social')
                            ->label('Razón social')
                            ->maxLength(200),
                        TextInput::make('rfc')
                            ->label('RFC')
                            ->minLength(12)
                            ->maxLength(13)
                            ->regex('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/i')
                            ->validationMessages(['regex' => 'El RFC no tiene un formato válido.'])
                            ->unique(ignoreRecord: true)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? mb_strtoupper($state) : null),
                        TextInput::make('sector')
                            ->maxLength(100)
                            ->datalist(['Automotriz', 'Tecnología', 'Manufactura', 'Servicios', 'Construcción', 'Salud', 'Educación', 'Comercio', 'Logística']),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Estado')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('activa')
                            ->helperText('Una empresa inactiva no puede publicar vacantes.')
                            ->default(true),
                        Toggle::make('verificada')
                            ->helperText('Insignia de empresa verificada por Vinculación.')
                            ->default(false),
                        FileUpload::make('logo_path')
                            ->label('Logotipo')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public')
                            ->maxSize(1024),
                    ]),

                Section::make('Contacto y ubicación')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('email_contacto')
                            ->label('Correo de contacto')
                            ->email()
                            ->maxLength(150),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('sitio_web')
                            ->label('Sitio web')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('municipio')
                            ->required()
                            ->maxLength(100)
                            ->default('San Luis Potosí'),
                        TextInput::make('estado')
                            ->required()
                            ->maxLength(100)
                            ->default('San Luis Potosí'),
                    ]),
            ]);
    }
}
