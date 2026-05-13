<?php

namespace App\Filament\Resources\MonitoringResource\Pages;

use App\Filament\Resources\MonitoringResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonitorings extends ListRecords
{
    protected static string $resource = MonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('open_reporting_form')
                ->label('Open Reporting Form')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(route('monitoring.login.show'))
                ->openUrlInNewTab(),
        ];
    }
}
