<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForeignAgencyResource\Pages;
use App\Filament\Tables\Columns\ContactNumberColumn;
use App\Filament\Tables\Columns\EmailColumn;
use App\Models\ForeignAgency;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ForeignAgencyResource extends BaseResource
{
    protected static ?string $model = ForeignAgency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('primary_contact_number')
                    ->label('Primary Contact Number')
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('E-mail Address')
                    ->email()
                    ->columnSpanFull(),
                TextInput::make('address')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                ContactNumberColumn::make('primary_contact_number')
                    ->label('Primary Contact Number'),
                EmailColumn::make('email')
                    ->label('E-mail Address'),
                TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListForeignAgencies::route('/'),
            'create' => Pages\CreateForeignAgency::route('/create'),
            'edit' => Pages\EditForeignAgency::route('/{record}/edit'),
        ];
    }
}
