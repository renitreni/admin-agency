<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')->form([
                TextInput::make('password')->password()->confirmed()->required(),
                TextInput::make('password_confirmation')->required()->password(),
            ])->action(function (User $user) {
                $user->update(['password' => $this->record['password']]);

                Notification::make()
                    ->title('Password changed successfully!')
                    ->icon('heroicon-o-check')
                    ->iconColor('success')
                    ->send();
            }),
            Actions\DeleteAction::make(),
        ];
    }
}
