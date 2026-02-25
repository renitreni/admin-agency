<?php

namespace App\Filament\Resources\UserForSuperAdminViewResource\Pages;

use App\Filament\Resources\UserForSuperAdminViewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsersForSuperAdminView extends ListRecords
{
    protected static string $resource = UserForSuperAdminViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
