<?php

namespace App\Filament\Resources\Categorias;

use App\Filament\Resources\Categorias\Pages\ManageCategorias;
use App\Models\Categoria;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Catálogo de áreas de conocimiento. Las 12 categorías obligatorias se cargan
 * por seeder; una categoría con vacantes no puede eliminarse.
 */
class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Catálogos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(120)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText('Se usa en la URL del filtro de vacantes.'),
                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('icono')
                    ->label('Ícono (Heroicon)')
                    ->placeholder('heroicon-o-briefcase')
                    ->maxLength(60),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'vacantes',
                'vacantes as vigentes_count' => fn (Builder $q) => $q->vigentes(),
            ]))
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('vigentes_count')
                    ->label('Vacantes vigentes')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('vacantes_count')
                    ->label('Vacantes totales')
                    ->sortable(),
                TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (Categoria $record): bool => $record->vacantes_count > 0),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategorias::route('/'),
        ];
    }
}
