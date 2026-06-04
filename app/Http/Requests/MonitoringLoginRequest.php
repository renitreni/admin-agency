<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonitoringLoginRequest extends FormRequest
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
            'passport_number' => 'required|string|max:255',
            'secret_code' => 'required|digits:5',
        ];
    }

    public function attributes(): array
    {
        return [
            'passport_number' => __('monitoring.attributes.passport_number'),
            'secret_code' => __('monitoring.attributes.secret_code'),
        ];
    }
}

