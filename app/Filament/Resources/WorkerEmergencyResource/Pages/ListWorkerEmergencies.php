<?php

namespace App\Filament\Resources\WorkerEmergencyResource\Pages;

use App\Filament\Resources\WorkerEmergencyResource;
use Filament\Resources\Pages\ListRecords;

class ListWorkerEmergencies extends ListRecords
{
    protected static string $resource = WorkerEmergencyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}