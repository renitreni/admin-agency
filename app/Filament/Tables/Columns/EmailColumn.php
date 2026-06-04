<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class EmailColumn
{
    public static function make(
        string $name = 'email',
        bool $sortable = true,
        bool $searchable = true,
    ): TextColumn {
        $column = TextColumn::make($name);

        if ($sortable) {
            $column->sortable();
        }

        if ($searchable) {
            $column->searchable();
        }

        return $column
            ->color(fn (Model $record): ?string => filled($record->{$name}) ? 'primary' : null)
            ->action(static::composeAction($name));
    }

    public static function composeAction(string $emailAttribute = 'email', ?string $actionName = null): Action
    {
        $actionName ??= 'compose_'.$emailAttribute;

        return Action::make($actionName)
            ->modalHeading('Compose email')
            ->modalDescription(fn (Model $record): ?string => filled($record->{$emailAttribute})
                ? $record->{$emailAttribute}
                : null)
            ->modalFooterActions(fn (Model $record): array => [
                Action::make('gmail')
                    ->label('Gmail')
                    ->icon('heroicon-o-envelope')
                    ->url(fn (): string => static::gmailComposeUrl($record->{$emailAttribute}))
                    ->openUrlInNewTab()
                    ->cancelParentActions(),
                Action::make('default_email_client')
                    ->label('Email')
                    ->icon('heroicon-o-at-symbol')
                    ->url(fn (): string => 'mailto:'.static::mailtoAddress($record->{$emailAttribute}))
                    ->openUrlInNewTab()
                    ->cancelParentActions(),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->disabled(fn (Model $record): bool => blank($record->{$emailAttribute}));
    }

    public static function gmailComposeUrl(string $email): string
    {
        return 'https://mail.google.com/mail/?view=cm&fs=1&to='.rawurlencode($email);
    }

    public static function mailtoAddress(string $email): string
    {
        return $email;
    }
}
