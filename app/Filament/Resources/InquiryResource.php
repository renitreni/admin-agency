<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Filament\Tables\Columns\ContactNumberColumn;
use App\Filament\Tables\Columns\EmailColumn;
use App\Models\Inquiry;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InquiryResource extends BaseResource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

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
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                ContactNumberColumn::make('phone', sortable: false, searchable: false),
                EmailColumn::make('email', sortable: false, searchable: false),
                TextColumn::make('company_no'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //   Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                //   Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListInquiries::route('/'),
            'view' => Pages\ViewInquiry::route('/{record}'),
            // 'create' => Pages\CreateInquiry::route('/create'),
            // 'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('email'),
                TextEntry::make('company_no'),
                TextEntry::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
