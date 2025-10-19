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
        $supportedLocales = array_keys(config('locales.supported'));

        if (! in_array($normalizedLocale, $supportedLocales, true)) {
            $normalizedLocale = config('locales.default');
        }

        $cacheKey = sprintf('content:home:%s', $normalizedLocale);

        return $this->cache->remember($cacheKey, now()->addHour(), function () use ($normalizedLocale) {
            $path = resource_path('content/home/'.$normalizedLocale.'.json');

            if (! File::exists($path)) {
                throw new RuntimeException(sprintf('Content file not found for locale [%s].', $normalizedLocale));
            }

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

