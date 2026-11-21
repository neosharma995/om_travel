<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function resolve(?string $path, string $fallback = '/images/seed/hero-coast.jpg'): string
    {
        if ($path === null || trim($path) === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public static function storeUpload(
        ?UploadedFile $file,
        string $directory,
        ?string $existingPath = null,
        ?string $fallbackUrl = null,
    ): ?string {
        if ($file instanceof UploadedFile) {
            self::deleteIfStored($existingPath);

            return $file->store($directory, 'public');
        }

        if ($fallbackUrl !== null && trim($fallbackUrl) !== '') {
            return trim($fallbackUrl);
        }

        return $existingPath;
    }

    public static function deleteIfStored(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete(ltrim($path, '/'));
    }
}
