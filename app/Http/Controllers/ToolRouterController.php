<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ToolRouterController extends Controller
{
    public function __construct(
        private readonly IpToolController $ipTool,
        private readonly Ipv4CalculatorController $ipv4Calculator
    ) {
    }

    public function __invoke(Request $request, string $locale, string $slug)
    {
        if (Arr::get(config('seo.tools.ip-tool'), $locale) === $slug) {
            return $this->ipTool->show($request, $locale, $slug);
        }

        if (Arr::get(config('seo.tools.ipv4-calculator'), $locale) === $slug) {
            return $this->ipv4Calculator->show($locale, $slug);
        }

        throw new NotFoundHttpException();
    }
}
