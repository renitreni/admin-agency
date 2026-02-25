<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserForSuperAdminViewResource\Pages;
use App\Models\Agency;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserForSuperAdminViewResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'General Settings';

    protected static ?string $navigationLabel = 'Users (Super Admin)';

    protected static ?string $modelLabel = 'User';

    protected static ?string $slug = 'users-super-admin';

    /** Show all users across all tenants; only for the allowed_email super admin. */
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
        return static::isAllowedUser();
    }

    public static function canViewAny(): bool
    {
        return static::isAllowedUser();
    }

    public static function canCreate(): bool
    {
        return static::isAllowedUser();
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAllowedUser();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isAllowedUser();
    }

    public static function canDeleteAny(): bool
    {
        return static::isAllowedUser();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->disabledOn('edit')->unique(ignoreRecord: true)->required(),
                TextInput::make('password')->password()->confirmed()->hiddenOn('edit'),
                TextInput::make('password_confirmation')->password()->hiddenOn('edit'),
                Select::make('agency_id')
                    ->options(Agency::all()->pluck('name', 'id'))
                    ->relationship('agency', 'name')
                    ->required()
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('agency.name')->sortable()->badge(),
                TextColumn::make('email')->sortable(),
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
            'index' => Pages\ListUsersForSuperAdminView::route('/'),
            'create' => Pages\CreateUserForSuperAdminView::route('/create'),
            'edit' => Pages\EditUserForSuperAdminView::route('/{record}/edit'),
        ];
    }
}
