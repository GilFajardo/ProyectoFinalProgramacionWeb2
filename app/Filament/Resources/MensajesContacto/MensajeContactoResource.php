<?php

namespace App\Filament\Resources\MensajesContacto;

use App\Filament\Resources\MensajesContacto\Pages\ManageMensajesContacto;
use App\Models\MensajeContacto;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * Bandeja de mensajes recibidos desde el formulario público de contacto.
 * Solo lectura: los mensajes se marcan como leídos de forma individual o masiva.
 */
class MensajeContactoResource extends Resource
{
    protected static ?string $model = MensajeContacto::class;

    protected static ?string $slug = 'mensajes-contacto';

    protected static ?string $modelLabel = 'mensaje';

    protected static ?string $pluralModelLabel = 'mensajes de contacto';

    protected static ?string $navigationLabel = 'Mensajes de contacto';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Comunicación';

    protected static ?string $recordTitleAttribute = 'asunto';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('email')->label('Correo')->copyable(),
                TextEntry::make('asunto')->columnSpanFull()->weight('bold'),
                TextEntry::make('mensaje')->columnSpanFull(),
                TextEntry::make('created_at')->label('Recibido')->dateTime('d/m/Y H:i'),
                TextEntry::make('ip')->label('IP')->placeholder('—'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('leido')
                    ->label('')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedEnvelopeOpen)
                    ->falseIcon(Heroicon::Envelope)
                    ->falseColor('primary')
                    ->trueColor('gray'),
                TextColumn::make('asunto')
                    ->searchable()
                    ->weight(fn (MensajeContacto $record): string => $record->leido ? 'normal' : 'bold')
                    ->description(fn (MensajeContacto $record): string => str($record->mensaje)->limit(80)->toString()),
                TextColumn::make('nombre')
                    ->searchable()
                    ->description(fn (MensajeContacto $record): string => $record->email),
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('leido')->label('Leído'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('marcarLeido')
                    ->label('Marcar leído')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (MensajeContacto $record): bool => ! $record->leido)
                    ->action(fn (MensajeContacto $record) => $record->update(['leido' => true])),
                Action::make('responder')
                    ->label('Responder')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('gray')
                    ->url(fn (MensajeContacto $record): string => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.$record->asunto)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('marcarLeidos')
                        ->label('Marcar como leídos')
                        ->icon(Heroicon::OutlinedEnvelopeOpen)
                        ->action(fn (Collection $records) => $records->each->update(['leido' => true]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $sinLeer = MensajeContacto::where('leido', false)->count();

        return $sinLeer > 0 ? (string) $sinLeer : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMensajesContacto::route('/'),
        ];
    }
}
