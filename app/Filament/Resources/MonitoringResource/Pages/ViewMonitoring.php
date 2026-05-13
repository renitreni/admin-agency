<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use App\Filament\Resources\MonitoringResource;
use App\Models\Monitoring;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonitoring extends ViewRecord
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Monitoring $record */
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

