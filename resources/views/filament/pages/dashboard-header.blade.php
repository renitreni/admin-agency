<!-- Dashboard Alert Banners: Emergency and Monitoring Alerts -->
@php
/**
 * @var \Illuminate\Support\Collection $workerEmergencies
 * @var \Illuminate\Support\Collection $workersNeedingMonitoring
 */
@endphp


<div class="space-y-4 mb-6">
    {{-- Emergency Alert Banner --}}
    @if($workerEmergencies->isNotEmpty())
        <x-alert-banner
            type="emergency"
            title="🚨 EMERGENCY ALERT"
            badge="ACTIVE"
            :items="$workerEmergencies->map(function($emergency) {
                $locationLink = $emergency->hasLocation()
                    ? '<a href=\'' . $emergency->getGoogleMapsUrl() . '\' target=\'_blank\' class=\'underline hover:text-red-800\'>View on Map</a>'
                    : 'Not available';

                return '<strong>' . $emergency->worker->fullname . '</strong> (' . $emergency->passport_number . ')
                    under <strong>' . ($emergency->agency?->name ?? 'N/A') . '</strong>
                    sent an EMERGENCY alert
                    <span class=\'text-red-700\'>(' . $emergency->created_at->diffForHumans() . ')</span>.
                    Location: ' . $locationLink . '
                    <a href=\'' . \App\Filament\Resources\WorkerEmergencyResource::getUrl('view', ['record' => $emergency]) . '\'
                       class=\'ml-2 inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 hover:bg-red-200\'>
                        View Details →
                    </a>';
            })->toArray()" />
    @endif

    {{-- Monitoring Alert Banner --}}
    @if($workersNeedingMonitoring->isNotEmpty())
        <x-alert-banner
            type="monitoring"
            title="⚠ Monitoring Alert"
            badge="ACTION REQUIRED"
            :items="$workersNeedingMonitoring->map(function($worker) {
                $hasPreviousReports = $worker->monitorings()
                    ->where('agency_id', $worker->agency_id)
                    ->exists();

                $threshold = $hasPreviousReports
                    ? config('monitoring.subsequent_report_threshold_days', 15)
                    : config('monitoring.first_report_threshold_days', 3);

                $daysSinceLastReport = $worker->getDaysSinceLastReport();

                $message = '<strong>' . $worker->fullname . '</strong> is currently DEPLOYED under <strong>'
                    . ($worker->agency?->name ?? 'N/A') . '</strong> ';

                if ($hasPreviousReports) {
                    $message .= 'and has not submitted a monitoring report for ' . $daysSinceLastReport . ' days (threshold: ' . $threshold . ' days).';
                } else {
                    $message .= 'and has not submitted any monitoring report for ' . $daysSinceLastReport . ' days since deployment (threshold: ' . $threshold . ' days).';
                }

                $message .= ' Please follow up immediately.
                    <a href=\'' . \App\Filament\Resources\WorkerResource::getUrl('edit', ['record' => $worker]) . '\'
                       class=\'ml-2 inline-flex items-center rounded bg-danger-100 px-2 py-0.5 text-xs font-medium text-danger-700 hover:bg-danger-200\'>
                        View Worker →
                    </a>';

                // Show Submit Report button only for non-FRA users
                $user = auth()->user();
                $isNonFraUser = $user instanceof \App\Models\User && $user->user_type !== \App\Models\User::TYPE_FRA;

                if ($isNonFraUser) {
                    $encodedArgs = htmlspecialchars(json_encode(['worker_id' => $worker->id]), ENT_QUOTES, 'UTF-8');
                    $message .= ' <button type="button" wire:click="mountAction(\'submitMonitoringReport\', ' . $encodedArgs . ')" class="mt-2 inline-flex items-center gap-1 rounded-md bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg> Submit Report</button>';
                }

                return $message;
            })->toArray()" />
    @endif
</div>