<?php

namespace App\Filament\Resources\UserForSuperAdminViewResource\Pages;

use App\Filament\Resources\UserForSuperAdminViewResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditUserForSuperAdminView extends EditRecord
{
    protected static string $resource = UserForSuperAdminViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->form([
                    TextInput::make('password')->password()->confirmed()->required(),
                    TextInput::make('password_confirmation')->required()->password(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['password' => $data['password']]);

                    Notification::make()
                        ->title('Password changed successfully!')
                        ->icon('heroicon-o-check')
                        ->iconColor('success')
                        ->send();
                }),
            DeleteAction::make()
                ->action(function () {
                    DB::table('agency_user')->where('user_id', $this->record->id)->delete();
                    User::find($this->record->id)?->delete();

                    $tenant = Filament::getTenant();

                    return redirect()->to(
                        $tenant
                            ? route('filament.admin.resources.users-super-admin.index', ['tenant' => $tenant->uuid])
                            : UserForSuperAdminViewResource::getUrl('index')
                    );
                }),
        ];
    }
}
