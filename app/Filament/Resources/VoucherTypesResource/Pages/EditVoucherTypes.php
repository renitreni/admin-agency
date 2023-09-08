<?php

namespace App\Filament\Resources\VoucherTypesResource\Pages;

use App\Filament\Resources\VoucherTypesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoucherTypes extends EditRecord
{
    protected static string $resource = VoucherTypesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
