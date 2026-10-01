<?php

namespace App\Http\Requests;

use App\Models\JobPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ApplicationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'contact_number' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'cover_letter' => 'nullable|string|max:300',
            'accepted_terms_and_condition' => 'required|in:1',
            'resume' => 'required|file|mimes:pdf,doc,docx|extensions:pdf,doc,docx|max:5120',
            'job_id' => 'nullable|uuid',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $agency = $this->route('agency');
            $routeJob = $this->route('jobPost');
            if ($routeJob && ($routeJob->agency_id !== $agency->id || ! $routeJob->is_published)) {
                $validator->errors()->add('job_id', 'This opening is no longer available. Please select another opening or submit a general CV.');
            }
            if ($routeJob && $this->filled('job_id') && $this->input('job_id') !== $routeJob->uuid) {
                $validator->errors()->add('job_id', 'The selected opening does not match this application.');
            }
            if (! $routeJob && $this->filled('job_id') && ! $validator->errors()->has('job_id')) {
                $exists = JobPost::where('agency_id', $agency->id)->published()
                    ->where('uuid', $this->input('job_id'))->exists();
                if (! $exists) {
                    $validator->errors()->add('job_id', 'This opening is no longer available. Please select another opening or submit a general CV.');
                }
            }
        });
    }
}
