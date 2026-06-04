<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetMonitoringLocale;
use App\Http\Requests\MonitoringEmergencyRequest;
use App\Http\Requests\MonitoringLoginRequest;
use App\Http\Requests\MonitoringStoreRequest;
use App\Models\Deployment;
use App\Models\Monitoring;
use App\Models\Worker;
use App\Models\WorkerEmergency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function showLogin(): View
    {
        return view('monitoring.login');
    }

    public function authenticate(MonitoringLoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $worker = Worker::query()
            ->with(['agency', 'workerInformation'])
            ->whereHas('workerInformation', function ($query) use ($validated): void {
                $query->where('passport_number', $validated['passport_number']);
            })
            ->where('code', $validated['secret_code'])
            ->first();

        if (! $worker) {
            return back()
                ->withErrors(['credentials' => __('monitoring.errors.invalid_credentials')])
                ->withInput();
        }

        $hasActiveDeployment = Deployment::query()
            ->where('worker_id', $worker->id)
            ->where('status', 'DEPLOYED')
            ->exists();

        if (! $hasActiveDeployment) {
            return back()
                ->withErrors(['credentials' => __('monitoring.errors.not_deployed')])
                ->withInput();
        }

        Session::put('monitoring.worker_id', $worker->id);

        return redirect()->route('monitoring.form.show');
    }

    public function showForm(Request $request): View|RedirectResponse
    {
        $worker = $this->getAuthenticatedWorker();

        if (! $worker) {
            return redirect()->route('monitoring.login.show');
        }

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');

        return view('monitoring.form', [
            'worker' => $worker,
            'agency' => $worker->agency,
            'latitude' => $latitude !== null && $latitude !== '' ? (float) $latitude : null,
            'longitude' => $longitude !== null && $longitude !== '' ? (float) $longitude : null,
        ]);
    }

    public function store(MonitoringStoreRequest $request): RedirectResponse
    {
        $worker = $this->getAuthenticatedWorker();

        if (! $worker) {
            return redirect()->route('monitoring.login.show');
        }

        $validated = $request->validated();

        if (! empty($request->input('website'))) {
            return redirect()->route('monitoring.form.show')->with('success', __('monitoring.messages.report_submitted'));
        }

        Monitoring::create([
            'agency_id' => $worker->agency_id,
            'worker_id' => $worker->id,
            'passport_number' => (string) optional($worker->workerInformation)->passport_number,
            'secret_code' => $worker->code,
            'report' => $validated['report'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        return redirect()->route('monitoring.form.show')->with('success', __('monitoring.messages.report_submitted'));
    }

    public function switchLocale(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (in_array($locale, SetMonitoringLocale::ALLOWED_LOCALES, true)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back();
    }

    public function storeEmergency(MonitoringEmergencyRequest $request): RedirectResponse
    {
        $worker = $this->getAuthenticatedWorker();

        if (! $worker) {
            return redirect()->route('monitoring.login.show');
        }

        // Check if worker already has an unresolved emergency
        if (WorkerEmergency::hasUnresolvedEmergency($worker->id)) {
            return redirect()->route('monitoring.form.show')->with('success', __('monitoring.messages.emergency_already_active'));
        }

        $validated = $request->validated();

        WorkerEmergency::create([
            'agency_id' => $worker->agency_id,
            'worker_id' => $worker->id,
            'passport_number' => (string) optional($worker->workerInformation)->passport_number,
            'secret_code' => $worker->code,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return redirect()->route('monitoring.form.show')->with('success', __('monitoring.messages.emergency_sent'));
    }

    public function logout(): RedirectResponse
    {
        Session::forget('monitoring.worker_id');

        return redirect()->route('monitoring.login.show');
    }

    protected function getAuthenticatedWorker(): ?Worker
    {
        $workerId = Session::get('monitoring.worker_id');

        if (! $workerId) {
            return null;
        }

        return Worker::query()
            ->with(['agency', 'workerInformation'])
            ->find($workerId);
    }
}
