<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use App\Models\Inquiry;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewInquiry extends ViewRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('Download Attachment')
                ->icon('heroicon-m-arrow-down-tray')
                ->requiresConfirmation()
                ->action(fn (Inquiry $record) => redirect()->to($record->getFirstMediaUrl('*'))),
            Actions\DeleteAction::make(),
        ];
    }
}
