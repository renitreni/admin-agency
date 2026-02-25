<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'foreign_recruitment_agency' => 'required|string|max:255',
            'ofw_full_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'birthdate' => 'required|date|before:today',
            'occupation' => 'required|string|max:255',
            'nation_id' => 'required|string|max:255',
            'passport_no' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'primary_contact' => 'required|string|max:255',
            'secondary_contact' => 'nullable|string|max:255',
            'address_abroad' => 'required|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'complaint' => 'required|string|max:10000',
            'image_evidences' => 'nullable|array|max:3',
            'image_evidences.*' => 'image|mimes:jpeg,png,gif,webp|max:5120', // 5MB per file
            'website' => 'nullable|max:0', // honeypot
        ];
    }

    public function attributes(): array
    {
        return [
            'foreign_recruitment_agency' => 'Foreign Recruitment Agency',
            'ofw_full_name' => "OFW's Full Name",
            'nation_id' => 'Nation ID',
            'passport_no' => 'Passport No.',
            'contact_person' => 'Contact Person',
            'primary_contact' => 'Primary Contact',
            'secondary_contact' => 'Secondary Contact',
            'address_abroad' => 'Address Abroad',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'image_evidences' => 'Image Evidences',
        ];
    }
}
