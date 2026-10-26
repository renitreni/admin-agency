<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintStoreRequest;
use App\Models\Agency;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function show(Request $request, string $agency): View
    {
        $agencyModel = Agency::where('uuid', $agency)->firstOrFail();

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');

        return view('complaints.form', [
            'agency' => $agencyModel,
            'latitude' => $latitude !== null && $latitude !== '' ? (float) $latitude : null,
            'longitude' => $longitude !== null && $longitude !== '' ? (float) $longitude : null,
        ]);
    }

    public function store(ComplaintStoreRequest $request, string $agency): RedirectResponse
    {
        $validated = $request->validated();

        // Honeypot: if filled, treat as bot and pretend success without saving
        if (! empty($request->input('website'))) {
            return redirect()->route('complaints.form', ['agency' => $agency])->with('success', __('Your complaint has been submitted.'));
        }

        // Override agency_uuid from route parameter (more secure than form input)
        $validated['agency_uuid'] = $agency;

        $complaint = Complaint::create([
            'agency_uuid' => $validated['agency_uuid'],
            'foreign_recruitment_agency' => $validated['foreign_recruitment_agency'],
            'ofw_full_name' => $validated['ofw_full_name'],
            'gender' => $validated['gender'],
            'birthdate' => $validated['birthdate'],
            'occupation' => $validated['occupation'],
            'nation_id' => $validated['nation_id'],
            'passport_no' => $validated['passport_no'],
            'email' => $validated['email'],
            'contact_person' => $validated['contact_person'],
            'primary_contact' => $validated['primary_contact'],
            'secondary_contact' => $validated['secondary_contact'] ?? null,
            'address_abroad' => $validated['address_abroad'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'complaint' => $validated['complaint'],
        ]);

        if ($request->hasFile('image_evidences')) {
            foreach ($request->file('image_evidences') as $file) {
                $complaint->addMedia($file)->toMediaCollection('image_evidences');
            }
        }

        return redirect()->route('complaints.form', ['agency' => $agency])->with('success', __('Your complaint has been submitted successfully.'));
    }
}
