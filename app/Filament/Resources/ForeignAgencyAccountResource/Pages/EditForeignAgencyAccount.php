<?php

namespace App\Filament\Resources\ForeignAgencyAccountResource\Pages;

use App\Filament\Resources\ForeignAgencyAccountResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EditForeignAgencyAccount extends EditRecord
{
    protected static string $resource = ForeignAgencyAccountResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_type'] = User::TYPE_FRA;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')->password()->confirmed()->required(),
                    \Filament\Forms\Components\TextInput::make('password_confirmation')->required()->password(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['password' => Hash::make($data['password'])]);
                }),
            DeleteAction::make()
                ->action(function () {
                    DB::table('foreign_agency_user')->where('user_id', $this->record->id)->delete();
                    DB::table('agency_user')->where('user_id', $this->record->id)->delete();
                    User::find($this->record->id)?->delete();

                    return redirect()->to(ForeignAgencyAccountResource::getUrl('index'));
                }),
        ];
    }
}
