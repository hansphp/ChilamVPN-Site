<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class LocalizedContent
{
    private CacheRepository $cache;

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Load home page content for the requested locale.
     */
    public function home(string $locale): array
    {
        $normalizedLocale = $this->normalizeLocale($locale);
        $supportedLocales = collect(array_keys(config('locales.supported', [])))
            ->map(fn ($code) => $this->normalizeLocale($code))
            ->all();
        $defaultLocale = $this->normalizeLocale(config('locales.default', 'en'));

        if (! in_array($normalizedLocale, $supportedLocales, true)) {
            $normalizedLocale = $defaultLocale;
        }

        $path = resource_path('content/home/'.$normalizedLocale.'.json');

        if (! File::exists($path)) {
            throw new RuntimeException(sprintf('Content file not found for locale [%s].', $normalizedLocale));
        }

        $version = File::lastModified($path);
        $cacheKey = sprintf('content:home:%s:%s', $normalizedLocale, $version);

        return $this->cache->remember($cacheKey, now()->addHour(), function () use ($path) {
            $decoded = json_decode(File::get($path), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException(sprintf(
                    'Invalid JSON in %s: %s',
                    $path,
                    json_last_error_msg()
                ));
            }

            return $decoded;
        });
    }

    private function normalizeLocale(string $locale): string
    {
        $locale = Str::replace('_', '-', $locale);

        return Str::lower($locale);
    }
}
