<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeoService;
use App\Support\IpResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GeoController extends Controller
{
    public function show(Request $request, IpResolver $resolver, GeoService $geoService): JsonResponse
    {
        $ip = $resolver->clientIp($request);

        $data = $ip
            ? $geoService->lookup($ip, app()->getLocale())
            : [];

        return $this->response($ip, $data);
    }

    public function lookup(Request $request, GeoService $geoService): JsonResponse
    {
        $ip = $this->validatedIp($request);

        $data = $geoService->lookup($ip, app()->getLocale());

        return $this->response($ip, $data);
    }

    private function validatedIp(Request $request): string
    {
        $ip = $request->query('ip');

        if (
            ! filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            )
        ) {
            throw ValidationException::withMessages([
                'ip' => __('The IP address provided is not valid or not public.'),
            ]);
        }

        return $ip;
    }

    private function response(?string $ip, array $data): JsonResponse
    {
        return response()
            ->json(array_merge([
                'ip' => $ip,
                'country' => null,
                'region' => null,
                'city' => null,
                'lat' => null,
                'lon' => null,
                'accuracy_radius_km' => null,
                'timezone' => null,
                'asn' => null,
            ], $data))
            ->withHeaders([
                'Cache-Control' => 'public, s-maxage=300, stale-while-revalidate=600',
            ]);
    }
}
