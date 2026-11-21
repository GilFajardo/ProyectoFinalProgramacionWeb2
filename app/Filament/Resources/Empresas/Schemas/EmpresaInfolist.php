<?php

namespace App\Filament\Resources\Empresas\Schemas;

use App\Models\Empresa;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmpresaInfolist
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
                        TextEntry::make('nombre')->weight('bold'),
                        TextEntry::make('razon_social')->label('Razón social')->placeholder('—'),
                        TextEntry::make('rfc')->label('RFC')->placeholder('—')->copyable(),
                        TextEntry::make('sector')->placeholder('—'),
                        TextEntry::make('descripcion')->label('Descripción')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('email_contacto')->label('Correo')->placeholder('—')->copyable(),
                        TextEntry::make('telefono')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('sitio_web')->label('Sitio web')->placeholder('—')->url(fn (Empresa $record): ?string => $record->sitio_web, shouldOpenInNewTab: true),
                        TextEntry::make('direccion')
                            ->label('Ubicación')
                            ->state(fn (Empresa $record): string => collect([$record->direccion, $record->municipio, $record->estado])->filter()->implode(', ')),
                    ]),
                Section::make('Resumen')
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('logo_path')->label('Logotipo')->disk('public')->visible(fn (Empresa $record): bool => filled($record->logo_path)),
                        IconEntry::make('activa')->boolean(),
                        IconEntry::make('verificada')->boolean(),
                        TextEntry::make('reclutadores_count')->label('Reclutadores')->state(fn (Empresa $record): int => $record->reclutadores()->count()),
                        TextEntry::make('vacantes_vigentes')->label('Vacantes vigentes')->state(fn (Empresa $record): int => $record->vacantes()->vigentes()->count())->badge()->color('success'),
                        TextEntry::make('postulaciones_count')->label('Postulaciones recibidas')->state(fn (Empresa $record): int => $record->postulaciones()->count()),
                        TextEntry::make('created_at')->label('Registrada')->dateTime('d/m/Y'),
                        TextEntry::make('deleted_at')->label('Dada de baja')->dateTime('d/m/Y')->visible(fn (Empresa $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}
