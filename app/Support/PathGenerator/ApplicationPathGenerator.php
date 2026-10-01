<?php

namespace App\Support\PathGenerator;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class ApplicationPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $directory = $media->getCustomProperty('upload_directory');

        return $directory ? str_replace('cv-staging/', 'resumes/', $directory) : parent::getBasePath($media);
    }
}
