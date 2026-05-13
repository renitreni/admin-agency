<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonitoringLoginRequest;
use App\Http\Requests\MonitoringStoreRequest;
use App\Models\Monitoring;
use App\Models\Worker;
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
                ->withErrors(['credentials' => 'Invalid passport number or secret code.'])
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
            return redirect()->route('monitoring.form.show')->with('success', __('Report submitted successfully.'));
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

        return redirect()->route('monitoring.form.show')->with('success', __('Report submitted successfully.'));
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

