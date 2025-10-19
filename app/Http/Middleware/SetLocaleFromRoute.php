<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetLocaleFromRoute
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');

        if (! $this->isLocaleSupported($locale)) {
            throw new NotFoundHttpException();
        }

        App::setLocale($locale);

        return $next($request);
    }

    private function isLocaleSupported(?string $locale): bool
    {
        if ($locale === null) {
            return false;
        }

        return in_array($locale, config('app.supported_locales', []), true);
    }
}

