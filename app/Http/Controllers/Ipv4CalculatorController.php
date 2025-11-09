<?php

namespace App\Http\Controllers;

use App\Services\LocalizedContent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Ipv4CalculatorController extends Controller
{
    public function __construct(private readonly LocalizedContent $content)
    {
    }

    public function show(string $locale, string $slug)
    {
        $this->guardSlug($locale, $slug);

        $pageContent = $this->content->home($locale);
        $alternates = $this->buildAlternates($locale);
        $locales = $this->buildLocaleOptions($locale);
        $toolLinks = $this->buildToolLinks($locale, data_get($pageContent, 'nav.tools_items', []));

        $meta = [
            'title' => __('ipv4.title'),
            'description' => __('ipv4.desc'),
        ];

        return response()
            ->view('tools.ipv4-calculator', [
                'locale' => $locale,
                'content' => $pageContent,
                'meta' => $meta,
                'alternates' => $alternates,
                'locales' => $locales,
                'toolLinks' => $toolLinks,
            ])
            ->header('Content-Language', $locale)
            ->header('Content-Security-Policy', static::CONTENT_SECURITY_POLICY);
    }

    private function guardSlug(string $locale, string $slug): void
    {
        $expected = Arr::get(config('seo.tools.ipv4-calculator'), $locale);

        if ($expected !== $slug) {
            throw new NotFoundHttpException();
        }
    }

    private function buildAlternates(string $activeLocale): array
    {
        $slugs = Arr::get(config('seo.tools'), 'ipv4-calculator', []);

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
            'url' => URL::to(sprintf('en/%s', $slugs['en'] ?? 'ipv4-calculator')),
            'active' => false,
        ])->all();
    }

    private function buildLocaleOptions(string $activeLocale): array
    {
        $supported = config('locales.supported', []);
        $slugs = Arr::get(config('seo.tools'), 'ipv4-calculator', []);

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
}
