<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Models\Worker;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    /**
     * Render a red monitoring alert banner above the data table.
     */
    public function getHeader(): ?View
    {
        $workersNeedingMonitoring = Worker::query()
            ->tenant()
            ->with('agency')
            ->whereHas('deployments', function (Builder $query): void {
                $query->whereColumn('agency_id', 'workers.agency_id')
                    ->where('status', 'DEPLOYED');
            })
            ->whereDoesntHave('monitorings', function (Builder $query): void {
                $query->whereColumn('agency_id', 'workers.agency_id');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

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
