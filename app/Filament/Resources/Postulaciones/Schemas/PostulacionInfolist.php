<?php

namespace App\Filament\Resources\Postulaciones\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostulacionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Alumno')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('alumno.user.name')->label('Nombre')->weight('bold'),
                        TextEntry::make('alumno.matricula')->label('Matrícula'),
                        TextEntry::make('alumno.user.email')->label('Correo')->copyable(),
                        TextEntry::make('alumno.telefono')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('alumno.carrera')->label('Carrera')->columnSpanFull(),
                        TextEntry::make('alumno.resumen')->label('Perfil profesional')->placeholder('—')->columnSpanFull(),
                    ]),
                Section::make('Vacante y seguimiento')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('vacante.titulo')->label('Vacante')->weight('bold')->columnSpanFull(),
                        TextEntry::make('vacante.empresa.nombre')->label('Empresa'),
                        TextEntry::make('estatus')->badge(),
                        TextEntry::make('created_at')->label('Fecha de postulación')->dateTime('d/m/Y H:i'),
                        TextEntry::make('estatus_actualizado_en')->label('Último cambio de estatus')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('carta_presentacion')->label('Carta de presentación')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('comentario_reclutador')->label('Comentario del reclutador')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}
