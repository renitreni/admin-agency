<?php

namespace App\Filament\Resources\JobPostResource\Pages;

use App\Filament\Resources\JobPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobPost extends CreateRecord
{
    protected static string $resource = JobPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['posted_by'] = auth()->user()->name;

        return $data;
    }
}
