<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintStoreRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function show(): View
    {
        return view('complaints.form');
    }

    public function store(ComplaintStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Honeypot: if filled, treat as bot and pretend success without saving
        if (! empty($request->input('website'))) {
            return redirect()->route('complaints.form')->with('success', __('Your complaint has been submitted.'));
        }

        $complaint = Complaint::create([
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
            'complaint' => $validated['complaint'],
        ]);

        if ($request->hasFile('image_evidences')) {
            foreach ($request->file('image_evidences') as $file) {
                $complaint->addMedia($file)->toMediaCollection('image_evidences');
            }
        }

        return redirect()->route('complaints.form')->with('success', __('Your complaint has been submitted successfully.'));
    }
}
