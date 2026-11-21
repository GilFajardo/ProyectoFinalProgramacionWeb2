<?php

namespace App\Filament\Resources\Empresas;

use App\Filament\Resources\Empresas\Pages\CreateEmpresa;
use App\Filament\Resources\Empresas\Pages\EditEmpresa;
use App\Filament\Resources\Empresas\Pages\ListEmpresas;
use App\Filament\Resources\Empresas\Pages\ViewEmpresa;
use App\Filament\Resources\Empresas\RelationManagers\ReclutadoresRelationManager;
use App\Filament\Resources\Empresas\RelationManagers\VacantesRelationManager;
use App\Filament\Resources\Empresas\Schemas\EmpresaForm;
use App\Filament\Resources\Empresas\Schemas\EmpresaInfolist;
use App\Filament\Resources\Empresas\Tables\EmpresasTable;
use App\Models\Empresa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/**
 * Gestión de empresas registradas y su vínculo con reclutadores.
 */
class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $modelLabel = 'empresa';

    protected static ?string $pluralModelLabel = 'empresas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Empresas y usuarios';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return EmpresaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmpresaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmpresasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ReclutadoresRelationManager::class,
            VacantesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmpresas::route('/'),
            'create' => CreateEmpresa::route('/create'),
            'view' => ViewEmpresa::route('/{record}'),
            'edit' => EditEmpresa::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'rfc', 'razon_social'];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
