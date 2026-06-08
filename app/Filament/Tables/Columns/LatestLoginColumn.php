<?php

namespace App\Filament\Tables\Columns;

use App\Models\User;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class LatestLoginColumn
{
    public static function make(string $name = 'latestLoginHistory.logged_in_at'): TextColumn
    {
        return TextColumn::make($name)
            ->label('Last login')
            ->dateTime()
            ->sortable()
            ->placeholder('Never')
            ->color(fn (Model $record): ?string => $record->latestLoginHistory ? 'primary' : null)
            ->action(static::loginHistoryAction());
    }

    public static function loginHistoryAction(?string $actionName = null): Action
    {
        return Action::make($actionName ?? 'view_login_history')
            ->modalHeading('Login History')
            ->modalDescription(fn (User $record): string => $record->name)
            ->modalContent(fn (User $record) => view('filament.components.login-history-table', [
                'histories' => $record->loginHistories,
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalWidth(MaxWidth::ThreeExtraLarge)
            ->disabled(fn (User $record): bool => $record->latestLoginHistory === null);
    }
}
