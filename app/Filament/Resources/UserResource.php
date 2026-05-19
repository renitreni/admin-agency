<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Agency;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use App\Filament\Resources\BaseResource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends BaseResource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'General Settings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(Auth::user() && Auth::user()->email === config('app.allowed_email'), function ($query) {
                $query->whereNot('email', config('app.allowed_email'));
            })
            ->when(Auth::check(), function ($query) {
                $query->where('user_type', User::TYPE_AGENCY);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->disabledOn('edit')->unique(ignoreRecord: true)->required(),
                TextInput::make('password')->password()->revealable()->confirmed()->hiddenOn('edit'),
                TextInput::make('password_confirmation')->password()->revealable()->hiddenOn('edit'),
                Select::make('agency_id')
                    ->options(fn () => Filament::getTenant()
                        ? [Filament::getTenant()->id => Filament::getTenant()->name]
                        : Agency::all()->pluck('name', 'id')->all())
                    ->relationship('agency', 'name', fn ($query) => $query->when(
                        Filament::getTenant(),
                        fn ($q) => $q->where((new Agency)->getTable() . '.id', Filament::getTenant()->id)
                    ))
                    ->required()
                    ->multiple(),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_type'] = User::TYPE_AGENCY;
        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('agency.name')->sortable()->badge(),
                TextColumn::make('email')->sortable(),
                TextColumn::make('user_type')->badge(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
