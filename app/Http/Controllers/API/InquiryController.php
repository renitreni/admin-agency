<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryStoreRequest;
use App\Models\Agency;
use App\Models\Inquiry;

class InquiryController extends Controller
{
    public function store(InquiryStoreRequest $inquiryStoreRequest, Agency $agency)
    {
        $validated = $inquiryStoreRequest->validated();

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'company_no' => $validated['company_no'],
            'description' => $validated['description'],
            'is_read' => 0,
            'agency_id' => $agency->id,
        ]);

        $documentTypes = ['inquiry_document','company_registration', 'national_id', 'other_document'];

        foreach ($documentTypes as $documentType) {
            if ($inquiryStoreRequest->has($documentType)) {
                $inquiry->addMediaFromRequest($documentType)->toMediaCollection($documentType);
            }
        }


        return response()->json($inquiry);
    }
}
