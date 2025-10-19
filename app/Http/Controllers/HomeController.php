<?php

namespace App\Http\Controllers;

use App\Services\LocalizedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke(Request $request, LocalizedContent $content)
    {
        $supported = config('locales.supported', []);
        $locale = $this->resolveLocale($request, $supported);

        $request->session()->put('app_locale', $locale);

        $pageContent = $content->home($locale);

        $availableLocales = $this->buildLocaleList($locale, $supported);

        return response()
            ->view('home', [
                'locale' => $locale,
                'content' => $pageContent,
                'locales' => $availableLocales,
            ])
            ->header('Content-Language', $locale);
    }

    private function resolveLocale(Request $request, array $supported): string
    {
        $queryLocale = $request->query('lang');
        if ($queryLocale && $canonical = $this->canonicalLocale($queryLocale, $supported)) {
            return $canonical;
        }

        $sessionLocale = $request->session()->get('app_locale');
        if ($sessionLocale && $canonical = $this->canonicalLocale($sessionLocale, $supported)) {
            return $canonical;
        }

        foreach ($this->parseAcceptLanguage($request->header('Accept-Language')) as $browserLocale) {
            if ($canonical = $this->canonicalLocale($browserLocale, $supported)) {
                return $canonical;
            }
        }

        return config('locales.default', 'es');
    }

    private function canonicalLocale(string $locale, array $supported): ?string
    {
        $normalized = $this->normalize($locale);

        foreach ($supported as $code => $meta) {
            $aliases = Collection::make($meta['aliases'] ?? [])
                ->push($code)
                ->map(fn ($alias) => $this->normalize($alias));

            if ($aliases->contains($normalized)) {
                return $code;
            }
        }

        return null;
    }

    private function parseAcceptLanguage(?string $header): array
    {
        if (empty($header)) {
            return [];
        }

        return collect(explode(',', $header))
            ->map(fn ($part) => trim(Str::before($part, ';')))
            ->filter()
            ->all();
    }

    private function buildLocaleList(string $activeLocale, array $supported): array
    {
        return collect($supported)
            ->map(function (array $meta, string $code) use ($activeLocale) {
                return [
                    'code' => $code,
                    'label' => Arr::get($meta, 'label', strtoupper($code)),
                    'active' => $code === $activeLocale,
                    'url' => route('home', ['lang' => $code]),
                ];
            })
            ->values()
            ->all();
    }

    private function normalize(string $locale): string
    {
        return Str::lower(Str::replace('_', '-', $locale));
    }
}

