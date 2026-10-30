<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerBlocklistResource\Pages;
use App\Models\WorkerBlocklist;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class WorkerBlocklistResource extends Resource
{
    protected static ?string $model = WorkerBlocklist::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Worker Blocklist';

    protected static ?int $navigationSort = 15;

    public static function canViewAny(): bool
    {
        return Auth::check() && ! static::isFraUser();
    }

    public static function canCreate(): bool
    {
        return Auth::check() && ! static::isFraUser();
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $agencyId = $user?->agency->first()?->id;

        return $form
            ->schema([
                Section::make('Worker Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->maxLength(100),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->maxLength(100),
                                TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(100),
                                TextInput::make('passport_number')
                                    ->label('Passport Number')
                                    ->maxLength(50),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(100),
                                TextInput::make('contact_number')
                                    ->label('Contact Number')
                                    ->maxLength(50),
                                Forms\Components\DatePicker::make('date_birth')
                                    ->label('Date of Birth'),
                                TextInput::make('place_birth')
                                    ->label('Place of Birth')
                                    ->maxLength(100),
                            ]),
                    ]),
                Section::make('Blocklist Details')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Reason for Blocklisting')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                        Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->maxLength(1000),
                        Select::make('match_confidence')
                            ->label('Match Confidence')
                            ->options([
                                'high' => 'High',
                                'medium' => 'Medium',
                                'low' => 'Low',
                            ])
                            ->default('medium')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;

        return $table
            ->defaultSort('blocked_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->label('Middle Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('passport_number')
                    ->label('Passport No.')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('contact_number')
                    ->label('Contact No.')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('date_birth')
                    ->label('DOB')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('agency.name')
                    ->label('Blocklisted By Agency')
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('match_confidence')
                    ->label('Confidence')
                    ->colors([
                        'danger' => 'high',
                        'warning' => 'medium',
                        'gray' => 'low',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                TextColumn::make('blocked_at')
                    ->label('Blocked At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('blockedBy.name')
                    ->label('Blocked By')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('match_confidence')
                    ->label('Confidence')
                    ->options([
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                SelectFilter::make('agency_id')
                    ->label('Agency')
                    ->relationship('agency', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ViewAction::make()
                    ->visible(fn (WorkerBlocklist $record): bool => $record->agency_id !== $userAgencyId),
                EditAction::make()
                    ->visible(fn (WorkerBlocklist $record): bool => $record->agency_id === $userAgencyId),
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (WorkerBlocklist $record) => $record->update(['is_active' => false]))
                    ->visible(fn (WorkerBlocklist $record): bool => $record->agency_id === $userAgencyId && $record->is_active),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => $userAgencyId !== null)
                        ->action(fn (Collection $records) => $records
                            ->where('agency_id', $userAgencyId)
                            ->each(fn ($record) => $record->update(['is_active' => false]).$record->delete())),
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
            'index' => Pages\ListWorkerBlocklists::route('/'),
            'create' => Pages\CreateWorkerBlocklist::route('/create'),
            'edit' => Pages\EditWorkerBlocklist::route('/{record}/edit'),
            'view' => Pages\ViewWorkerBlocklist::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;
        $showOwn = request()->query('agency') !== 'other';

        if ($showOwn && $userAgencyId) {
            // Show own agency entries by default
            return $query->ownedByAgency($userAgencyId);
        } elseif (! $showOwn && $userAgencyId) {
            // Show other agencies entries (read-only view)
            return $query->fromOtherAgencies($userAgencyId)->active();
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;

        if (! $userAgencyId) {
            return null;
        }

        $count = static::getModel()::query()
            ->ownedByAgency($userAgencyId)
            ->active()
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
