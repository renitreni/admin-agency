# Public CV submissions

The Joseline static landing site submits CVs to this Laravel application. Recruiters review them in the existing tenant-scoped Applications resource. General applications have a null `job_post_id`; no migration is required.

## API contract

Send `multipart/form-data` with `Accept: application/json` to:

- `POST /api/v1/application/{agency_uuid}` for a general CV or an optional published `job_id` UUID.
- `POST /api/v1/application/{agency_uuid}/job/{job_uuid}` for a particular published opening. If `job_id` is also supplied, it must match.

Required fields: `first_name`, `last_name`, `email`, `contact_number`, `accepted_terms_and_condition=1`, and `resume`. Optional fields: `middle_name`, `cover_letter` (300 characters), and `job_id`. Contact/name fields allow 100 characters. The CV must have a PDF/DOC/DOCX extension and matching recognized file content, at most 5120 KB. Only published, undeleted jobs belonging to the route's agency are accepted.

Success is HTTP 201 with `{ "message": "…", "application_id": 123 }`. Validation failures are HTTP 422 with Laravel's `message` and field `errors`. Missing route resources return 404. Submission endpoints share a limit of three requests per minute per IP and return 429 when exceeded. No CV URL or applicant details are returned.

`GET /api/v1/job-post/{agency_uuid}` remains paginated and adds `description_excerpt` to each opening. The static site loads the pages and displays six cards per page, with country filters.

## Storage and downloads

New CVs are stored on `private_cv`, rooted at `storage/app/private/cvs`, outside the public storage symlink. Each upload has its own UUID directory. Database creation and media registration run in one transaction; staged and partially written files are cleaned on failure.

The admin Download CV action uses `GET /admin/{agency_uuid}/applications/{application_id}/resume`. This route requires a signed-in agency user belonging to the matching agency and rejects FRA users. Responses are attachments with `nosniff` and `private, no-store` headers. Existing resumes retain their recorded disk/path and can be downloaded through the same authorized route. Previously public resume URLs remain public until legacy files are separately migrated; this change does not move or break those files.

## Release checks

1. Configure the static site's `site-config.js` with the HTTPS API base URL ending in `/api/v1` and Joseline's agency UUID. The UUID can be read from the agency record; it is not a credential. A blank configuration disables CV submission.
2. Create/publish the intended openings under that agency in Laravel. The previous six static cards are replaced by these records.
3. Deploy backend changes before the website. No database migration is needed. Clear/rebuild Laravel configuration and route caches through the normal deployment process.
4. Ensure the PHP-FPM user can write `storage/app/private/cvs`. Do not symlink or expose this directory through nginx. Existing Docker permissions cover the storage tree.
5. Ensure PHP `upload_max_filesize` is at least 5M and `post_max_size` and the proxy body limit allow multipart overhead (at least 8M). The checked-in Docker image uses 200M/100M and nginx permits 500M; application validation still enforces 5 MB. The inspected host CLI PHP has a 2M upload limit; configure the serving PHP runtime before accepting 5 MB files.
6. Existing API CORS permits cross-origin requests without credentials. Verify a POST/preflight from the actual landing-site origin against the deployed API.
7. Submit a test CV, verify the general/job label in the correct agency's Applications area, download as a recruiter, and verify anonymous/FRA/other-agency requests fail. The automated tests exercise this flow with isolated SQLite and fake storage; production is not changed by them.

## Verification

Run `php artisan test --filter=CvSubmissionTest` for API validation, storage cleanup, recruiter download access, published jobs, and CORS. Frontend checks live in the Joseline repository (`npm ci && npm test`) and use DOM assertions and mocked HTTP responses. Do not perform visual QA on live pages.
