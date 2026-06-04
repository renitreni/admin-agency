<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Tables\Columns\EmailColumn;
use App\Models\Agency;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AgencyResource extends BaseResource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'General Settings';

    protected static ?string $navigationLabel = 'Agencies (Super Admin)';

    /** Only for the allowed_email super admin. */
    protected static bool $isScopedToTenant = false;

    /**
     * Only the user whose email matches config('app.allowed_email') can see and use this resource.
     */
    protected static function isAllowedUser(): bool
    {
        $allowedEmail = config('app.allowed_email');

        return $allowedEmail && Auth::check() && Auth::user()->email === $allowedEmail;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function canViewAny(): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function canCreate(): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function canDeleteAny(): bool
    {
        return static::isAllowedUser() && ! static::isFraUser();
    }

    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()->whereNot('name', ['Yaramay']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->columnSpan(1),
                        TextInput::make('email')->columnSpan(1),
                        Placeholder::make('uuid')
                            ->hiddenOn('create')
                            ->content(fn ($record): string => $record->uuid ?? '')
                            ->columnSpan(2),
                        // SpatieMediaLibraryFileUpload::make('logo')
                        //     ->columnSpan(2)
                        //     ->collection('logo'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable(),
                EmailColumn::make('email'),
                TextColumn::make('uuid')->searchable()->copyable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'create' => Pages\CreateAgency::route('/create'),
            'index' => Pages\ListAgencies::route('/'),
            'edit' => Pages\EditAgency::route('/{record}/edit'),
        ];
    }
}
