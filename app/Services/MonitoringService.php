<?php

namespace App\Services;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Collection;

class MonitoringService
{
    public function getWorkersNeedingMonitoring(): Collection
    {
        return Worker::query()
            ->tenant()
            ->with('agency')
            ->get()
            ->filter(fn ($worker) => $worker->needsMonitoringAlertBasedOnConfig())
            ->sortBy([
                ['last_name', 'asc'],
                ['first_name', 'asc'],
            ])
            ->values();
    }

    public function getWorkersNeedingMonitoringForFra(User $user): Collection
    {
        $foreignAgencyIds = $user->foreignAgencies->pluck('id');

        if ($foreignAgencyIds->isEmpty()) {
            return collect();
        }

        return Worker::query()
            ->tenant()
            ->whereHas('deployments', function ($query) use ($foreignAgencyIds) {
                $query->whereIn('foreign_agency_id', $foreignAgencyIds);
            })
            ->with('agency')
            ->get()
            ->filter(fn ($worker) => $worker->needsMonitoringAlertBasedOnConfig())
            ->sortBy([
                ['last_name', 'asc'],
                ['first_name', 'asc'],
            ])
            ->values();
    }

    public function getDaysUntilNextDeadline(Worker $worker): int
    {
        $hasPreviousReports = $worker->monitorings()
            ->where('agency_id', $worker->agency_id)
            ->exists();
            
        $daysSinceLastReport = $worker->getDaysSinceLastReport();
        
        if (!$hasPreviousReports) {
            // First report threshold
            $threshold = config('monitoring.first_report_threshold_days', 3);
        } else {
            // Subsequent report threshold
            $threshold = config('monitoring.subsequent_report_threshold_days', 15);
        }
        
        return max(0, $threshold - $daysSinceLastReport);
    }
}
