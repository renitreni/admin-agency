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
            'latitude' => __('monitoring.attributes.latitude'),
            'longitude' => __('monitoring.attributes.longitude'),
        ];
    }

    public function messages(): array
    {
        $message = __('monitoring.messages.location_required_emergency');

        return [
            'latitude.required' => $message,
            'longitude.required' => $message,
        ];
    }
}