<?php

namespace App\Filament\Resources\Resource\Widgets;

use App\Models\Application;
use App\Models\Deployment;
use App\Models\User;
use App\Models\Worker;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WorkerOverview extends Widget
{
    protected static string $view = 'filament.resources.resource.widgets.worker-overview';

    protected array|string|int $columnSpan = 'full';

    protected static ?int $sort = -4;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Auth::user();

        if ($user instanceof User && $user->user_type === User::TYPE_FRA) {
            $foreignAgencyIds = $user->foreignAgencies->pluck('id');

            if ($foreignAgencyIds->isEmpty()) {
                return [
                    'workerCount' => 0,
                    'deploymentCount' => 0,
                    'applicationCount' => 0,
                ];
            }

            return [
                'workerCount' => Worker::query()
                    ->tenant()
                    ->whereHas('deployments', function ($query) use ($foreignAgencyIds) {
                        $query->whereIn('foreign_agency_id', $foreignAgencyIds);
                    })
                    ->count(),
                'deploymentCount' => Deployment::query()
                    ->tenant()
                    ->whereIn('foreign_agency_id', $foreignAgencyIds)
                    ->count(),
                'applicationCount' => 0,
            ];
        }

        return [
            'workerCount' => Worker::tenant()->count(),
            'deploymentCount' => Deployment::tenant()->count(),
            'applicationCount' => Application::tenant()->count(),
        ];
    }
}
