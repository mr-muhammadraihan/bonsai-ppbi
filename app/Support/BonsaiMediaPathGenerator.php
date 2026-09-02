<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class BonsaiMediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return 'bonsais/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'bonsais/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'bonsais/';
    }
}
