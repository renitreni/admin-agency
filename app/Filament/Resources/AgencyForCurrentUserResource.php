<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgencyForCurrentUserResource\Pages;
use App\Models\Agency;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AgencyForCurrentUserResource extends Resource
{
    protected static ?string $model = Agency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'General Settings';

    protected static ?string $navigationLabel = 'My Agency';

    protected static ?string $modelLabel = 'Agency';

    protected static ?string $slug = 'my-agency';

    /** Show only agencies the current user belongs to. */
    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $agencyIds = Auth::check()
            ? Auth::user()->agency->pluck('id')->toArray()
            : [];

        return parent::getEloquentQuery()->whereIn('id', $agencyIds);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->agency->isNotEmpty();
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->agency->isNotEmpty();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check() && Auth::user()->agency->contains('id', $record->id);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('uuid')->searchable()->copyable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListAgenciesForCurrentUser::route('/'),
            'edit' => Pages\EditAgencyForCurrentUser::route('/{record}/edit'),
        ];
    }
}
