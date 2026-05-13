<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringResource\Pages;
use App\Models\Monitoring;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'APIs';

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
                TextColumn::make('passport_number')
                    ->label('Passport')
                    ->searchable(),
                TextColumn::make('secret_code')
                    ->label('Secret Code')
                    ->searchable(),
                TextColumn::make('report')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('viewOnMap')
                    ->label('View on Map')
                    ->icon('heroicon-o-map-pin')
                    ->url(fn (Monitoring $record): ?string => $record->getGoogleMapsUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (Monitoring $record): bool => $record->hasLocation()),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMonitorings::route('/'),
            'view' => Pages\ViewMonitoring::route('/{record}'),
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
                TextEntry::make('report')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}

