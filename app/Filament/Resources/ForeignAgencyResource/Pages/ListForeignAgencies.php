<?php

namespace App\Filament\Resources\ForeignAgencyResource\Pages;

use App\Filament\Resources\ForeignAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForeignAgencies extends ListRecords
{
    protected static string $resource = ForeignAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
