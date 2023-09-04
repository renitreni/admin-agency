<?php

namespace App\Filament\Resources\ForeignAgencyResource\Pages;

use App\Filament\Resources\ForeignAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditForeignAgency extends EditRecord
{
    protected static string $resource = ForeignAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
