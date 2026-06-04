<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class ContactNumberColumn
{
    public static function make(
        string $name = 'contact_number',
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
            ->action(static::contactAction($name));
    }

    public static function contactAction(string $numberAttribute = 'contact_number', ?string $actionName = null): Action
    {
        $actionName ??= 'contact_'.$numberAttribute;

        return Action::make($actionName)
            ->modalHeading('Contact number')
            ->modalDescription(fn (Model $record): ?string => filled($record->{$numberAttribute})
                ? $record->{$numberAttribute}
                : null)
            ->modalFooterActions(fn (Model $record): array => [
                Action::make('phone')
                    ->label('Phone')
                    ->icon('heroicon-o-phone')
                    ->url(fn (): string => static::telUrl($record->{$numberAttribute}))
                    ->cancelParentActions(),
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn (): string => static::whatsAppUrl($record->{$numberAttribute}))
                    ->openUrlInNewTab()
                    ->cancelParentActions(),
                Action::make('viber')
                    ->label('Viber')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->url(fn (): string => static::viberUrl($record->{$numberAttribute}))
                    ->cancelParentActions(),
            ])
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->disabled(fn (Model $record): bool => blank($record->{$numberAttribute}));
    }

    public static function digitsOnly(string $number): string
    {
        return preg_replace('/\D+/', '', $number) ?? '';
    }

    public static function telUrl(string $number): string
    {
        $digits = static::digitsOnly($number);

        if ($digits === '') {
            return '#';
        }

        return 'tel:+'.$digits;
    }

    public static function whatsAppUrl(string $number): string
    {
        $digits = static::digitsOnly($number);

        if ($digits === '') {
            return '#';
        }

        return 'https://wa.me/'.$digits;
    }

    public static function viberUrl(string $number): string
    {
        $digits = static::digitsOnly($number);

        if ($digits === '') {
            return '#';
        }

        return 'viber://chat?number='.rawurlencode('+'.$digits);
    }
}
