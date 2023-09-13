<?php

namespace App\Actions;

class NewApplicationAction
{
    public static function process($request, $agency, $jobPostId = null)
    {
        $application = $agency->applications()
            ->create([
                'job_post_id' => $jobPostId,
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'middle_name' => $request->get('middle_name'),
                'contact_number' => $request->get('contact_number'),
                'email' => $request->get('email'),
                'cover_letter' => $request->get('cover_letter'),
                'accepted_terms_and_condition' => $request->get('accepted_terms_and_condition'),
            ]);

        $application->addMediaFromRequest('resume')->toMediaCollection('resumes');
    }
}
