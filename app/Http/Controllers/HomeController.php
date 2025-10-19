<?php

namespace App\Http\Controllers;

use App\Services\GeoService;
use App\Services\LocalizedContent;
use App\Support\IpResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{
    private LocalizedContent $content;
    private GeoService $geo;
    private IpResolver $ipResolver;

    public function __construct(LocalizedContent $content, GeoService $geo, IpResolver $ipResolver)
    {
        $this->content = $content;
        $this->geo = $geo;
        $this->ipResolver = $ipResolver;
    }

    public function index(Request $request, string $locale, string $slug)
    {
        $this->guardSlug($locale, $slug);

        $pageContent = $this->content->home($locale);

        $clientIp = $this->ipResolver->clientIp($request);
        $ipVersion = $this->ipResolver->ipVersion($clientIp);
        $geo = $clientIp ? $this->geo->lookup($clientIp, $locale) : [];

        $alternates = $this->buildAlternates($locale);
        $locales = $this->buildLocaleOptions($locale);

        $meta = [
            'title' => trans('home.title'),
            'description' => trans('home.desc'),
        ];

        $versionLabel = match ($ipVersion) {
            'IPv6' => trans('home.ipv6'),
            'IPv4' => trans('home.ipv4'),
            default => trans('home.unknown'),
        };

        $ipDetails = [
            'ip' => $clientIp,
            'version' => $versionLabel,
            'version_raw' => $ipVersion,
            'location' => $this->formatLocation($geo),
            'isp' => $this->formatAsn($geo),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()
            ->view('ip.index', [
                'locale' => $locale,
                'content' => $pageContent,
                'meta' => $meta,
                'ipDetails' => $ipDetails,
                'geo' => $geo,
                'alternates' => $alternates,
                'locales' => $locales,
            ])
            ->header('Content-Language', $locale)
            ->header('Content-Security-Policy', "default-src 'self'; style-src 'self'; script-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self'; base-uri 'self'; frame-ancestors 'none';");
    }

    private function guardSlug(string $locale, string $slug): void
    {
        $expected = $this->slugFor($locale);

        if ($expected !== $slug) {
            throw new NotFoundHttpException();
        }
    }

    private function slugFor(string $locale): string
    {
        $slugs = config('seo.slugs', []);

        if (! array_key_exists($locale, $slugs)) {
            throw new NotFoundHttpException();
        }

        return $slugs[$locale];
    }

    private function buildAlternates(string $activeLocale): array
    {
        $slugs = config('seo.slugs', []);

        $alternates = collect($slugs)->map(function (string $slug, string $locale) use ($activeLocale) {
            return [
                'locale' => $locale,
                'hreflang' => str_replace('_', '-', $locale),
                'url' => URL::to(sprintf('%s/%s', $locale, $slug)),
                'active' => $locale === $activeLocale,
            ];
        })->values()->all();

        $alternates[] = [
            'locale' => 'x-default',
            'hreflang' => 'x-default',
            'url' => URL::to(sprintf('en/%s', $slugs['en'] ?? 'what-is-my-ip')),
            'active' => false,
        ];

        return $alternates;
    }

    private function buildLocaleOptions(string $activeLocale): array
    {
        $supported = config('locales.supported', []);

        return collect($supported)
            ->map(function (array $meta, string $locale) use ($activeLocale) {
                $slug = $this->slugFor($locale);

                return [
                    'code' => $locale,
                    'label' => Arr::get($meta, 'label', $locale),
                    'active' => $locale === $activeLocale,
                    'url' => URL::to(sprintf('%s/%s', $locale, $slug)),
                ];
            })
            ->values()
            ->all();
    }

    private function formatLocation(array $geo): string
    {
        $parts = array_filter([
            Arr::get($geo, 'city'),
            Arr::get($geo, 'region'),
            Arr::get($geo, 'country'),
        ]);

        return $parts ? implode(', ', $parts) : trans('home.unknown');
    }

    private function formatAsn(array $geo): string
    {
        $asn = Arr::get($geo, 'asn');

        if (! is_array($asn)) {
            return trans('home.unknown');
        }

        $number = Arr::get($asn, 'number');
        $name = Arr::get($asn, 'name');

        if ($number && $name) {
            return sprintf('AS%d – %s', $number, $name);
        }

        if ($number) {
            return sprintf('AS%d', $number);
        }

        if ($name) {
            return $name;
        }

        return trans('home.unknown');
    }
}
