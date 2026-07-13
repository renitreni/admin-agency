<?php

namespace App\Filament\Resources\WorkerEmergencyResource\Pages;

use App\Filament\Resources\WorkerEmergencyResource;
use App\Services\AlertBannerService;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListWorkerEmergencies extends ListRecords
{
    protected static string $resource = WorkerEmergencyResource::class;

    /**
     * Render an emergency alert banner above the data table.
     */
    public function getHeader(): ?View
    {
        $alertService = new AlertBannerService();
        $workerEmergencies = $alertService->getWorkerEmergencies();

        if ($workerEmergencies->isEmpty()) {
            return null;
        }

        return view('filament.resources.worker-resource.pages.emergency-alert-banner', [
            'workerEmergencies' => $workerEmergencies,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $status = data_get($this->tableFilters, 'status.status');

        return match ($status) {
            'active' => $query->unresolved(),
            'resolved' => $query->resolved(),
            default => $query,
        };
    }
}
