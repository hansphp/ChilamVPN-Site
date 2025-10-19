<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class GeoService
{
    private CacheRepository $cache;
    private HttpFactory $http;

    public function __construct(CacheRepository $cache, HttpFactory $http)
    {
        $this->cache = $cache;
        $this->http = $http;
    }

    public function lookup(string $ip, string $locale): array
    {
        $cacheKey = $this->cacheKey($ip, $locale);

        $cached = $this->cache->get($cacheKey);

        if ($cached !== null) {
            return $this->formatResponse($ip, $cached);
        }

        $fresh = $this->lookupFresh($ip, $locale);

        $this->cache->put($cacheKey, $fresh, now()->addMinutes(10));

        return $this->formatResponse($ip, $fresh);
    }

    private function lookupFresh(string $ip, string $locale): array
    {
        if ($data = $this->lookupMaxMind($ip, $locale)) {
            return $data;
        }

        if ($data = $this->lookupHttp($ip, $locale)) {
            return $data;
        }

        return [];
    }

    private function lookupMaxMind(string $ip, string $locale): ?array
    {
        $path = $this->resolvePath(config('services.geo.maxmind_city_db'));

        if (! $path) {
            return null;
        }

        try {
            $languages = $this->preferredLanguages($locale);
            $reader = new Reader($path, $languages);
            $city = $reader->city($ip);
            $reader->close();

            $asnData = $this->asn($ip);

            return [
                'country' => $city->country->name,
                'country_iso_code' => $city->country->isoCode,
                'region' => optional($city->subdivisions[0] ?? null)->name,
                'city' => $city->city?->name,
                'lat' => $city->location?->latitude,
                'lon' => $city->location?->longitude,
                'accuracy_radius_km' => $city->location?->accuracyRadius,
                'timezone' => $city->location?->timeZone,
                'asn' => $asnData,
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function lookupHttp(string $ip, string $locale): ?array
    {
        $endpoint = config('services.geo.http_endpoint');

        if (blank($endpoint)) {
            return null;
        }

        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => $locale,
        ];

        if ($token = $this->httpToken()) {
            $headers['Authorization'] = $token;
        }

        $response = $this->http->timeout(3)->withHeaders($headers)->get($endpoint, [
            'ip' => $ip,
            'locale' => $locale,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return null;
        }

        return [
            'country' => Arr::get($payload, 'country'),
            'country_iso_code' => Arr::get($payload, 'country_iso_code'),
            'region' => Arr::get($payload, 'region'),
            'city' => Arr::get($payload, 'city'),
            'lat' => Arr::get($payload, 'lat'),
            'lon' => Arr::get($payload, 'lon'),
            'accuracy_radius_km' => Arr::get($payload, 'accuracy_radius_km'),
            'timezone' => Arr::get($payload, 'timezone'),
            'asn' => Arr::get($payload, 'asn'),
        ];
    }

    private function asn(string $ip): ?array
    {
        $path = $this->resolvePath(config('services.geo.maxmind_asn_db'));

        if (! $path) {
            return null;
        }

        try {
            $reader = new Reader($path);
            $asn = $reader->asn($ip);
            $reader->close();

            return [
                'number' => $asn->autonomousSystemNumber,
                'name' => $asn->autonomousSystemOrganization,
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function preferredLanguages(string $locale): array
    {
        $primary = Str::lower(substr($locale, 0, 2));

        $languages = [$primary];

        if ($primary !== 'en') {
            $languages[] = 'en';
        }

        return $languages;
    }

    private function resolvePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $fullPath = base_path($path);

        if (! file_exists($fullPath)) {
            return null;
        }

        return $fullPath;
    }

    private function cacheKey(string $ip, string $locale): string
    {
        $secret = config('services.geo.ip_hash_key') ?? config('app.key');

        if (blank($secret)) {
            throw new RuntimeException('IP_HASH_KEY is missing.');
        }

        $payload = sprintf('%s|%s', $ip, $locale);

        return 'geo:'.hash_hmac('sha256', $payload, $secret);
    }

    private function formatResponse(string $ip, array $data): array
    {
        return array_merge([
            'ip' => $ip,
            'country' => null,
            'country_iso_code' => null,
            'region' => null,
            'city' => null,
            'lat' => null,
            'lon' => null,
            'accuracy_radius_km' => null,
            'timezone' => null,
            'asn' => null,
        ], $data);
    }

    private function httpToken(): ?string
    {
        $token = config('services.geo.http_token');

        return blank($token) ? null : 'Bearer '.$token;
    }
}
