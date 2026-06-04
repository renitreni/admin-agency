<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Services\AlertBannerService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    /**
     * Render a red monitoring alert banner above the data table.
     */
    public function getHeader(): ?View
    {
        $alertService = new AlertBannerService;
        $workersNeedingMonitoring = $alertService->getWorkersNeedingMonitoring();

        if ($workersNeedingMonitoring->isEmpty()) {
            return null;
        }

        return view('filament.resources.worker-resource.pages.list-workers-header', [
            'workersNeedingMonitoring' => $workersNeedingMonitoring,
            'heading' => $this->getHeading(),
            'subheading' => $this->getSubheading(),
            'headerActions' => $this->getCachedHeaderActions(),
            'breadcrumbs' => filament()->hasBreadcrumbs() ? $this->getBreadcrumbs() : [],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
