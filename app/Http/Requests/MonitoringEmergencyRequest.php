<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringEmergencyRequest extends FormRequest
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
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    public function attributes(): array
    {
        return [
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Location is required for emergency alerts. Please enable location services.',
            'longitude.required' => 'Location is required for emergency alerts. Please enable location services.',
        ];
    }
}