<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\Rol;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Pestañas por rol.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = ['todos' => Tab::make('Todos')];

        foreach (Rol::cases() as $rol) {
            $tabs[$rol->value] = Tab::make($rol->getLabel().'es')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', $rol));
        }

        return $tabs;
    }
}
