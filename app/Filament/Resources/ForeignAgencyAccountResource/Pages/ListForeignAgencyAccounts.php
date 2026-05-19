<?php

namespace App\Filament\Resources\ForeignAgencyAccountResource\Pages;

use App\Filament\Resources\ForeignAgencyAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForeignAgencyAccounts extends ListRecords
{
    protected static string $resource = ForeignAgencyAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
