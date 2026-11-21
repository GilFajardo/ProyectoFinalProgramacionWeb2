<?php

namespace App\Filament\Resources\Vacantes;

use App\Filament\Resources\Vacantes\Pages\CreateVacante;
use App\Filament\Resources\Vacantes\Pages\EditVacante;
use App\Filament\Resources\Vacantes\Pages\ListVacantes;
use App\Filament\Resources\Vacantes\Pages\ViewVacante;
use App\Filament\Resources\Vacantes\RelationManagers\PostulacionesRelationManager;
use App\Filament\Resources\Vacantes\Schemas\VacanteForm;
use App\Filament\Resources\Vacantes\Schemas\VacanteInfolist;
use App\Filament\Resources\Vacantes\Tables\VacantesTable;
use App\Models\Vacante;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * Gestión de ofertas de empleo y su periodo de vigencia.
 */
class VacanteResource extends Resource
{
    protected static ?string $model = Vacante::class;

    protected static ?string $modelLabel = 'vacante';

    protected static ?string $pluralModelLabel = 'vacantes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Bolsa de trabajo';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return VacanteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VacanteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VacantesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PostulacionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVacantes::route('/'),
            'create' => CreateVacante::route('/create'),
            'view' => ViewVacante::route('/{record}'),
            'edit' => EditVacante::route('/{record}/edit'),
        ];
    }

    /**
     * Número de vacantes vigentes como insignia en el menú.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) Vacante::vigentes()->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Vacantes vigentes';
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['titulo', 'empresa.nombre'];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
