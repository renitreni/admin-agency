<?php

namespace App\Filament\Resources\ForeignAgencyAccountResource\Pages;

use App\Filament\Resources\ForeignAgencyAccountResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateForeignAgencyAccount extends CreateRecord
{
    protected static string $resource = ForeignAgencyAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_type'] = User::TYPE_FRA;
        $data['password'] = Hash::make($data['password']);

        return $data;
    }
}
