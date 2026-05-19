<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Models\Worker;
use App\Services\MonitoringService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    /**
     * Render a red monitoring alert banner above the data table.
     */
    public function getHeader(): ?View
    {
        $monitoringService = new MonitoringService();
        $user = Auth::user();

        $workersNeedingMonitoring = $user instanceof \App\Models\User && $user->user_type === \App\Models\User::TYPE_FRA
            ? $monitoringService->getWorkersNeedingMonitoringForFra($user)
            : $monitoringService->getWorkersNeedingMonitoring();

        if ($workersNeedingMonitoring->isEmpty()) {
            return null;
        }

        return view('filament.resources.worker-resource.pages.monitoring-alert-banner', [
            'workersNeedingMonitoring' => $workersNeedingMonitoring,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
