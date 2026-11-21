<?php

namespace App\Filament\Resources\Postulaciones;

use App\Enums\EstatusPostulacion;
use App\Filament\Resources\Postulaciones\Pages\ListPostulaciones;
use App\Filament\Resources\Postulaciones\Pages\ViewPostulacion;
use App\Filament\Resources\Postulaciones\Schemas\PostulacionInfolist;
use App\Filament\Resources\Postulaciones\Tables\PostulacionesTable;
use App\Models\Postulacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Supervisión de postulaciones. Las crean los alumnos desde el portal;
 * aquí el administrador las consulta y puede ajustar su estatus.
 */
class PostulacionResource extends Resource
{
    protected static ?string $model = Postulacion::class;

    protected static ?string $slug = 'postulaciones';

    protected static ?string $modelLabel = 'postulación';

    protected static ?string $pluralModelLabel = 'postulaciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Bolsa de trabajo';

    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return PostulacionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostulacionesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Postulacion::where('estatus', EstatusPostulacion::EnProceso)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Postulaciones en proceso';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostulaciones::route('/'),
            'view' => ViewPostulacion::route('/{record}'),
        ];
    }
}
