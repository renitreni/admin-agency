<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\InquiryStoreRequest;
use App\Models\Agency;
use App\Models\Inquiry;
use Illuminate\Http\Request;

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
            'agency_id' => $agency->id
        ]);

        return response()->json($inquiry);
    }
}
