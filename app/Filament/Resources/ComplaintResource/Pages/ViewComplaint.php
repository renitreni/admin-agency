<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Models\Complaint;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Complaint $record */
        $record = $this->record;

        return [
            Actions\Action::make('viewOnMap')
                ->label('View on Map')
                ->icon('heroicon-o-map-pin')
                ->url($record->getGoogleMapsUrl())
                ->openUrlInNewTab()
                ->visible($record->hasLocation()),
            Actions\DeleteAction::make(),
        ];
    }
}
