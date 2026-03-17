<?php

namespace App\Support\PathGenerator;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

/**
 * Path generator for Worker model.
 * For the 'passport' collection uses a stable path (no media id) so re-uploading
 * overwrites the same file and the original filename is preserved.
 */
class WorkerPathGenerator extends DefaultPathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    protected function getBasePath(Media $media): string
    {
        if ($media->collection_name === 'passport') {
            $prefix = config('media-library.prefix', '');
            $base = trim($media->model_type.'/'.$media->model_id.'/'.$media->collection_name, '/');

            return $prefix !== '' ? $prefix.'/'.$base : $base;
        }

        return parent::getBasePath($media);
    }
}
