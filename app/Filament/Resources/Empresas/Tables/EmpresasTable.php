<?php

namespace App\Filament\Resources\Empresas\Tables;

use App\Models\Empresa;
use App\Services\ReporteService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmpresasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'reclutadores',
                'vacantes as vigentes_count' => fn (Builder $q) => $q->vigentes(),
                'postulaciones',
            ]))
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Empresa $record): ?string => $record->sector),
                TextColumn::make('rfc')
                    ->label('RFC')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('municipio')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_contacto')
                    ->label('Correo')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reclutadores_count')
                    ->label('Reclutadores')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('vigentes_count')
                    ->label('Vacantes vigentes')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('postulaciones_count')
                    ->label('Postulaciones')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('verificada')
                    ->boolean(),
                IconColumn::make('activa')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('activa'),
                TernaryFilter::make('verificada'),
                SelectFilter::make('sector')
                    ->options(fn (): array => Empresa::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector', 'sector')->all()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('fichaPdf')
                    ->label('Ficha PDF')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('gray')
                    ->action(function (Empresa $record): StreamedResponse {
                        $pdf = app(ReporteService::class)->empresas($record);

                        return response()->streamDownload(fn () => print ($pdf->output()), 'empresa-'.$record->id.'.pdf', ['Content-Type' => 'application/pdf']);
                    }),
                DeleteAction::make()->label('Dar de baja'),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Dar de baja'),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
