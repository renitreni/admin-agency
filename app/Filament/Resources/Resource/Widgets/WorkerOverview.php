<?php

namespace App\Filament\Resources\Resource\Widgets;

use App\Models\Application;
use App\Models\Deployment;
use App\Models\Worker;
use Filament\Widgets\Widget;

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
        return [
            'workerCount' => Worker::tenant()->count(),
            'deploymentCount' => Deployment::tenant()->count(),
            'applicationCount' => Application::tenant()->count(),
        ];
    }
}
