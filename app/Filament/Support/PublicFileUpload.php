<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Forms\Components\BaseFileUpload;
use Illuminate\Support\Facades\File;

final class PublicFileUpload
{
    /**
     * @param  string|array<string, string>|null  $storedFileNames
     * @return array{name: string, size: int, type: ?string, url: string}
     */
    public static function uploadedFileMeta(BaseFileUpload $component, string $file, string|array|null $storedFileNames): array
    {
        $name = basename(parse_url($file, PHP_URL_PATH) ?: $file);

        if ($component->isMultiple() && is_array($storedFileNames)) {
            $name = $storedFileNames[$file] ?? $name;
        } elseif (is_string($storedFileNames)) {
            $name = $storedFileNames;
        }

        [$size, $type] = self::fileInfo($component, $file);

        return [
            'name' => $name,
            'size' => $size,
            'type' => $type,
            'url' => self::url($file) ?? '#',
        ];
    }

    public static function url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }

    /**
     * @return array{0: int, 1: ?string}
     */
    private static function fileInfo(BaseFileUpload $component, string $file): array
    {
        if (! $component->shouldFetchFileInformation()) {
            return [0, null];
        }

        $path = trim($file);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, 'data:')) {
            return [0, null];
        }

        if (str_starts_with($path, '/') && ! str_starts_with($path, '/storage/')) {
            $publicPath = public_path(ltrim(parse_url($path, PHP_URL_PATH) ?: $path, '/'));

            if (is_file($publicPath)) {
                return [(int) filesize($publicPath), File::mimeType($publicPath) ?: null];
            }

            return [0, null];
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        try {
            $storage = $component->getDisk();

            if (! $storage->exists($path)) {
                return [0, null];
            }

            return [(int) $storage->size($path), $storage->mimeType($path)];
        } catch (\Throwable) {
            return [0, null];
        }
    }
}
