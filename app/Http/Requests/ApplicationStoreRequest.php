<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'max:100|required',
            'last_name' => 'max:100|required',
            'middle_name' => 'max:100|required',
            'contact_number' => 'max:100|required',
            'email' => 'max:100|email|required',
            'cover_letter' => 'max:300|required',
            'accepted_terms_and_condition' => 'max:1|required|in:1',
            'resume' => 'required|file',
            'job_id' => 'required',
        ];
    }
}
