<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LoginHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'loginHistories';

    protected static ?string $title = 'Login History';

    protected static ?string $icon = 'heroicon-o-clock';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('logged_in_at')
                    ->label('Logged in')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP address')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User agent')
                    ->limit(80)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->placeholder('—'),
            ])
            ->defaultSort('logged_in_at', 'desc')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('No login history yet')
            ->emptyStateDescription('Login events are recorded automatically when this user signs in.');
    }
}
