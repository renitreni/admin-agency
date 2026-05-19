<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForeignAgencyAccountResource\Pages;
use App\Models\ForeignAgency;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ForeignAgencyAccountResource extends BaseResource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'FRA Accounts';

    protected static ?int $navigationSort = 20;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check()
            && Auth::user() instanceof \App\Models\User
            && Auth::user()->user_type !== \App\Models\User::TYPE_FRA;
    }

    public static function canViewAny(): bool
    {
        return Auth::check()
            && Auth::user() instanceof \App\Models\User
            && Auth::user()->user_type !== \App\Models\User::TYPE_FRA;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('user_type', User::TYPE_FRA);

        if (! Auth::check()) {
            return $query->whereRaw('1 = 0');
        }

        $tenant = Filament::getTenant();
        if (! $tenant) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('foreignAgencies', function (Builder $foreignAgencyQuery) use ($tenant) {
            $foreignAgencyQuery->where('agency_id', $tenant->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->disabledOn('edit')->unique(ignoreRecord: true)->required(),
                TextInput::make('password')->password()->confirmed()->hiddenOn('edit'),
                TextInput::make('password_confirmation')->password()->hiddenOn('edit'),
                Select::make('foreign_agency_id')
                    ->label('Foreign Recruitment Agency')
                    ->options(fn () => ForeignAgency::tenant()->pluck('name', 'id'))
                    ->relationship('foreignAgencies', 'name', fn ($query) => $query->tenant())
                    ->required()
                    ->multiple(),
                Select::make('agency_id')
                    ->options(fn () => Filament::getTenant()
                        ? [Filament::getTenant()->id => Filament::getTenant()->name]
                        : [])
                    ->relationship('agency', 'name', fn ($query) => $query->when(
                        Filament::getTenant(),
                        fn ($q) => $q->where((new \App\Models\Agency)->getTable() . '.id', Filament::getTenant()->id)
                    ))
                    ->required()
                    ->multiple(),
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::check()
            && Auth::user() instanceof User
            && Auth::user()->user_type !== User::TYPE_FRA
            && $record->user_type === User::TYPE_FRA;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('foreignAgencies.name')->label('F.R.A')->badge(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForeignAgencyAccounts::route('/'),
            'create' => Pages\CreateForeignAgencyAccount::route('/create'),
            'edit' => Pages\EditForeignAgencyAccount::route('/{record}/edit'),
        ];
    }
}
