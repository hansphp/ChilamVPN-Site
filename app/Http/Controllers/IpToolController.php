<?php

namespace App\Http\Controllers;

use App\Services\GeoService;
use App\Services\LocalizedContent;
use App\Support\IpResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IpToolController extends Controller
{
    public function __construct(
        private readonly LocalizedContent $content,
        private readonly GeoService $geo,
        private readonly IpResolver $ipResolver
    ) {
    }

    public function show(Request $request, string $locale, string $slug)
    {
        $this->guardSlug($locale, $slug);

        $pageContent = $this->content->home($locale);
        $alternates = $this->buildAlternates($locale);
        $locales = $this->buildLocaleOptions($locale);
        $toolLinks = $this->buildToolLinks($locale, data_get($pageContent, 'nav.tools_items', []));

        $clientIp = $this->ipResolver->clientIp($request);
        $ipVersionRaw = $this->ipResolver->ipVersion($clientIp);
        $geo = $clientIp ? $this->geo->lookup($clientIp, $locale) : [];

        $ipDetails = [
            'ip' => $clientIp,
            'version' => $this->translatedVersion($ipVersionRaw),
            'location' => $this->formatLocation($geo),
            'isp' => $this->formatAsn($geo),
            'country_code' => strtoupper((string) Arr::get($geo, 'country_iso_code')),
            'updated_at' => now()->toIso8601String(),
        ];

        $meta = [
            'title' => __('ip.title'),
            'description' => __('ip.desc'),
        ];

        return response()
            ->view('tools.ip', [
                'locale' => $locale,
                'content' => $pageContent,
                'meta' => $meta,
                'alternates' => $alternates,
                'locales' => $locales,
                'toolLinks' => $toolLinks,
                'ipDetails' => $ipDetails,
            ])
            ->header('Content-Language', $locale)
            ->header('Content-Security-Policy', static::CONTENT_SECURITY_POLICY);
    }

    private function guardSlug(string $locale, string $slug): void
    {
        $expected = Arr::get(config('seo.tools.ip-tool'), $locale);

        if ($expected !== $slug) {
            throw new NotFoundHttpException();
        }
    }

    private function buildAlternates(string $activeLocale): array
    {
        $slugs = Arr::get(config('seo.tools'), 'ip-tool', []);

        return collect($slugs)->map(function (string $slug, string $locale) use ($activeLocale) {
            return [
                'locale' => $locale,
                'hreflang' => str_replace('_', '-', $locale),
                'url' => URL::to(sprintf('%s/%s', $locale, $slug)),
                'active' => $locale === $activeLocale,
            ];
        })->push([
            'locale' => 'x-default',
            'hreflang' => 'x-default',
            'url' => URL::to(sprintf('en/%s', $slugs['en'] ?? 'what-is-my-ip')),
            'active' => false,
        ])->all();
    }

    private function buildLocaleOptions(string $activeLocale): array
    {
        $supported = config('locales.supported', []);
        $slugs = Arr::get(config('seo.tools'), 'ip-tool', []);

        return collect($supported)
            ->map(function (array $meta, string $locale) use ($activeLocale, $slugs) {
                $slug = $slugs[$locale] ?? null;

                return [
                    'code' => $locale,
                    'label' => Arr::get($meta, 'label', $locale),
                    'active' => $locale === $activeLocale,
                    'url' => $slug ? URL::to(sprintf('%s/%s', $locale, $slug)) : URL::to($locale),
                ];
            })
            ->values()
            ->all();
    }

    private function buildToolLinks(string $locale, array $items): array
    {
        $toolSlugs = config('seo.tools', []);

        return collect($items)->map(function (array $item) use ($locale, $toolSlugs) {
            $target = Arr::get($item, 'target');
            $href = Arr::get($item, 'href');

            if ($target && isset($toolSlugs[$target][$locale])) {
                $href = URL::to(sprintf('%s/%s', $locale, $toolSlugs[$target][$locale]));
            }

            return [
                'label' => Arr::get($item, 'label', ''),
                'href' => $href ?? '#',
            ];
        })->all();
    }

    private function translatedVersion(?string $version): string
    {
        return match ($version) {
            'IPv6' => __('ip.ipv6'),
            'IPv4' => __('ip.ipv4'),
            default => __('ip.unknown'),
        };
    }

    private function formatLocation(array $geo): string
    {
        $parts = array_filter([
            Arr::get($geo, 'city'),
            Arr::get($geo, 'region'),
            Arr::get($geo, 'country'),
        ]);

        return $parts ? implode(', ', $parts) : __('ip.unknown');
    }

    private function formatAsn(array $geo): string
    {
        $asn = Arr::get($geo, 'asn');

        if (! is_array($asn)) {
            return __('ip.unknown');
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

        return __('ip.unknown');
    }
}
