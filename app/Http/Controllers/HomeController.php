<?php

namespace App\Http\Controllers;

use App\Services\LocalizedContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    public function __construct(private readonly LocalizedContent $content)
    {
    }

    public function landing(): RedirectResponse
    {
        $defaultLocale = config('locales.default', 'es-MX');
        $slug = $this->homeSlug($defaultLocale);

        $path = $slug ? sprintf('%s/%s', $defaultLocale, $slug) : $defaultLocale;

        return redirect('/' . ltrim($path, '/'));
    }

    public function show(string $locale)
    {
        $pageContent = $this->content->home($locale);

        $alternates = $this->buildAlternates($locale);
        $locales = $this->buildLocaleOptions($locale);
        $toolLinks = $this->buildToolLinks($locale, data_get($pageContent, 'nav.tools_items', []));

        $meta = [
            'title' => data_get($pageContent, 'meta.title') ?? data_get($pageContent, 'hero.brand', 'ChilamVPN'),
            'description' => data_get($pageContent, 'meta.description'),
        ];

        return response()
            ->view('home.index', [
                'locale' => $locale,
                'content' => $pageContent,
                'meta' => $meta,
                'alternates' => $alternates,
                'locales' => $locales,
                'toolLinks' => $toolLinks,
            ])
            ->header('Content-Language', $locale)
            ->header('Content-Security-Policy', "default-src 'self'; style-src 'self'; script-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self'; base-uri 'self'; frame-ancestors 'none';");
    }

    private function buildAlternates(string $activeLocale): array
    {
        $slugs = config('seo.home', []);

        return collect($slugs)->map(function ($slug, string $locale) use ($activeLocale) {
            $path = $slug ? sprintf('%s/%s', $locale, $slug) : $locale;

            return [
                'locale' => $locale,
                'hreflang' => str_replace('_', '-', $locale),
                'url' => URL::to($path),
                'active' => $locale === $activeLocale,
            ];
        })->push([
            'locale' => 'x-default',
            'hreflang' => 'x-default',
            'url' => URL::to($this->homeSlugPath('en')),
            'active' => false,
        ])->all();
    }

    private function buildLocaleOptions(string $activeLocale): array
    {
        $supported = config('locales.supported', []);

        return collect($supported)
            ->map(function (array $meta, string $locale) use ($activeLocale) {
                return [
                    'code' => $locale,
                    'label' => Arr::get($meta, 'label', $locale),
                    'active' => $locale === $activeLocale,
                    'url' => URL::to($this->homeSlugPath($locale)),
                ];
            })
            ->values()
            ->all();
    }

    private function buildToolLinks(string $locale, array $items): array
    {
        $ipSlug = Arr::get(config('seo.tools'), 'ip', []);
        $ipSlug = $ipSlug[$locale] ?? null;

        return collect($items)->map(function (array $item) use ($locale, $ipSlug) {
            $target = Arr::get($item, 'target');
            $href = Arr::get($item, 'href');

            if ($target === 'ip-tool' && $ipSlug) {
                $href = URL::to(sprintf('%s/%s', $locale, $ipSlug));
            }

            return [
                'label' => Arr::get($item, 'label', ''),
                'href' => $href ?? '#',
            ];
        })->all();
    }

    private function homeSlug(string $locale): ?string
    {
        $slugs = config('seo.home', []);

        return $slugs[$locale] ?? null;
    }

    private function homeSlugPath(string $locale): string
    {
        $slug = $this->homeSlug($locale);

        return $slug ? sprintf('%s/%s', $locale, $slug) : $locale;
    }
}
