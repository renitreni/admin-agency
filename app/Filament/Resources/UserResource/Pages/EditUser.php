<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')->form([
                TextInput::make('password')->password()->confirmed()->required(),
                TextInput::make('password_confirmation')->required()->password(),
            ])->action(function (User $user, $data) {
                $user->update(['password' => $data['password']]);
                
                Notification::make()
                    ->title('Password changed successfully!')
                    ->icon('heroicon-o-check')
                    ->iconColor('success')
                    ->send();
            }),
            DeleteAction::make()
                ->action(function () {
                    DB::table('agency_user')->where('user_id', $this->record['id'])->delete();
                    User::find($this->record['id'])->delete();
                    return redirect()->to(route('filament.admin.resources.users.index', ['tenant' => Filament::getTenant()->uuid]));
                }),
        ];
    }
}
