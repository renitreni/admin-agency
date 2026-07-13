<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerEmergencyResource\Pages;
use App\Models\WorkerEmergency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use App\Filament\Resources\BaseResource;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WorkerEmergencyResource extends BaseResource
{
    protected static ?string $model = WorkerEmergency::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'APIs';

    protected static ?string $navigationLabel = 'Worker Emergencies';

    protected static ?string $modelLabel = 'Worker Emergency';

    protected static ?string $pluralModelLabel = 'Worker Emergencies';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('worker.fullname')
                    ->label('Worker')
                    ->sortable(['workers.first_name'])
                    ->searchable(['workers.first_name', 'workers.last_name']),
                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('passport_number')
                    ->label('Passport')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (WorkerEmergency $record): string => $record->isResolved() ? 'success' : 'danger')
                    ->state(fn (WorkerEmergency $record): string => $record->isResolved() ? 'Resolved' : 'Active'),
                TextColumn::make('resolved_at')
                    ->label('Resolved At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('resolvedBy.name')
                    ->label('Resolved By')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\Filter::make('status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'resolved' => 'Resolved',
                            ])
                            ->placeholder('All'),
                    ])
            ])
            ->actions([
                Tables\Actions\Action::make('viewOnMap')
                    ->label('View on Map')
                    ->icon('heroicon-o-map-pin')
                    ->url(fn (WorkerEmergency $record): ?string => $record->getGoogleMapsUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (WorkerEmergency $record): bool => $record->hasLocation()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Resolve Emergency')
                    ->modalDescription('Are you sure you want to mark this emergency as resolved?')
                    ->modalSubmitActionLabel('Yes, Resolve')
                    ->form([
                        Forms\Components\Textarea::make('resolution_reason')
                            ->label('Resolution Reason')
                            ->placeholder('Enter the reason for resolving this emergency...')
                            ->required()
                            ->minLength(10)
                            ->maxLength(1000),
                    ])
                    ->action(function (WorkerEmergency $record, array $data) {
                        $record->markAsResolved(auth()->id(), $data['resolution_reason']);

                        Notification::make()
                            ->title('Emergency Resolved')
                            ->body('The emergency has been marked as resolved.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (WorkerEmergency $record): bool => ! $record->isResolved()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkerEmergencies::route('/'),
            'view' => Pages\ViewWorkerEmergency::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('worker.fullname')
                    ->label('Worker'),
                TextEntry::make('worker.agency.name')
                    ->label('Agency'),
                TextEntry::make('passport_number')
                    ->label('Passport Number'),
                TextEntry::make('secret_code')
                    ->label('Secret Code'),
                TextEntry::make('latitude')->placeholder('—'),
                TextEntry::make('longitude')->placeholder('—'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (WorkerEmergency $record): string => $record->isResolved() ? 'success' : 'danger')
                    ->state(fn (WorkerEmergency $record): string => $record->isResolved() ? 'Resolved' : 'Active'),
                TextEntry::make('created_at')
                    ->label('Reported At')
                    ->dateTime(),
                TextEntry::make('resolved_at')
                    ->label('Resolved At')
                    ->dateTime()
                    ->placeholder('—')
                    ->visible(fn (WorkerEmergency $record): bool => $record->isResolved()),
                TextEntry::make('resolvedBy.name')
                    ->label('Resolved By')
                    ->placeholder('—')
                    ->visible(fn (WorkerEmergency $record): bool => $record->isResolved()),
                TextEntry::make('resolution_reason')
                    ->label('Resolution Reason')
                    ->placeholder('—')
                    ->columnSpanFull()
                    ->visible(fn (WorkerEmergency $record): bool => $record->isResolved()),
            ]);
    }
}