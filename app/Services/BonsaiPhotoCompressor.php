<?php

namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BonsaiPhotoCompressor
{
    private const MAX_BYTES = 1_048_576;

    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $this->compress($event->media);
    }

    public function compress(Media $media): void
    {
        if ($media->collection_name !== 'bonsai-photos') {
            return;
        }

        $path = $media->getPath();

        if (! is_file($path) || filesize($path) <= self::MAX_BYTES) {
            return;
        }

        $imageInfo = @getimagesize($path);

        if (! $imageInfo) {
            return;
        }

        $source = match ($imageInfo['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $source) {
            return;
        }

        $temporaryPath = $path.'.compressed';
        $scales = [1.0, 0.9, 0.8, 0.7, 0.6, 0.5, 0.4, 0.3, 0.2];
        $qualities = [100, 95, 90, 85, 80, 75, 70, 65, 60, 55, 50];
        $compressed = false;

        foreach ($scales as $scale) {
            $width = max(1, (int) round(imagesx($source) * $scale));
            $height = max(1, (int) round(imagesy($source) * $scale));
            $resized = imagecreatetruecolor($width, $height);

            if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/webp') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

            foreach ($qualities as $quality) {
                $written = match ($imageInfo['mime']) {
                    'image/jpeg' => imagejpeg($resized, $temporaryPath, $quality),
                    'image/png' => imagepng($resized, $temporaryPath, 9),
                    'image/webp' => imagewebp($resized, $temporaryPath, $quality),
                    default => false,
                };

                if ($written && filesize($temporaryPath) <= self::MAX_BYTES) {
                    imagedestroy($resized);
                    $compressed = true;
                    break 2;
                }
            }

            imagedestroy($resized);
        }

        imagedestroy($source);

        if (! $compressed) {
            @unlink($temporaryPath);

            return;
        }

        rename($temporaryPath, $path);
        $media->size = filesize($path);
        $media->saveQuietly();
    }
}
