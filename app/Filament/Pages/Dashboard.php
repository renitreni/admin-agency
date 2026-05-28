<?php

namespace App\Filament\Pages;

use App\Services\AlertBannerService;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    /**
     * Render alert banners above the dashboard content.
     */
    public function getHeader(): ?View
    {
        $alertService = new AlertBannerService();
        $alerts = $alertService->getAllAlerts();

        if ($alerts['workerEmergencies']->isEmpty() && $alerts['workersNeedingMonitoring']->isEmpty()) {
            return null;
        }

        return view('filament.pages.dashboard-header', $alerts);
    }
}