<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HandlerResource\Pages;
use App\Models\Handler;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class HandlerResource extends BaseResource
{
    protected static ?string $model = Handler::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $failed = [];
                            foreach ($records as $record) {
                                try {
                                    $record->delete();
                                } catch (QueryException $e) {
                                    if ($e->getCode() === '23000') {
                                        $failed[] = $record->name;
                                    } else {
                                        throw $e;
                                    }
                                }
                            }
                            if (!empty($failed)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Some handlers could not be deleted')
                                    ->body('The following handlers have associated deployments: ' . implode(', ', $failed) . '. Please remove or reassign deployments first.')
                                    ->persistent()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title('Handlers deleted successfully')
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListHandlers::route('/'),
            'create' => Pages\CreateHandler::route('/create'),
            'edit' => Pages\EditHandler::route('/{record}/edit'),
        ];
    }
}
