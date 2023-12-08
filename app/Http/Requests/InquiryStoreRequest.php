<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InquiryStoreRequest extends FormRequest
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
            'name' => 'required|max:200',
            'phone' => 'required|max:200',
            'email' => 'required|max:200|email',
            'company_no' => 'required|max:200',
            'description' => 'required|max:300',
            'inquiry_document' => 'nullable|file',
            'national_id' => 'nullable|file',
            'company_registration' => 'nullable|file',
            'other_document' => 'nullable|file',
        ];
    }
}
