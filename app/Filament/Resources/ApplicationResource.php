<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection as DBCollection;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'APIs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('jobPost.title')->label('Position Applied')->sortable()->searchable(),
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('middle_name')->sortable()->searchable(),
                TextColumn::make('contact_number')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Export')
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->requiresConfirmation()
                        ->action(function (DBCollection $records) {
                            return response()->streamDownload(function () use ($records) {
                                echo \Barryvdh\DomPDF\Facade\Pdf::loadHtml(
                                    view(
                                        'downloadables.applications-pdf',
                                        ['records' => $records, 'agency' => Filament::getTenant()]
                                    )
                                )->setPaper('a4', 'landscape')->stream();
                            }, "Applications ".now()->format('F j, Y')." .pdf");
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'view' => Pages\ViewApplication::route('/{record}'),
            // 'create' => Pages\CreateApplication::route('/create'),
            // 'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('middle_name'),
                TextEntry::make('contact_number'),
                TextEntry::make('email'),
                TextEntry::make('jobPost.title'),
                TextEntry::make('cover_letter')
                    ->columnSpanFull(),
            ]);
    }
}
