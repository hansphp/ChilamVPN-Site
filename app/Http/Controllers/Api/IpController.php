<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\IpResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IpController extends Controller
{
    public function show(Request $request, IpResolver $resolver): JsonResponse
    {
        $ip = $resolver->clientIp($request);
        $version = $resolver->ipVersion($ip);

        return response()
            ->json([
                'ip' => $ip,
                'version' => $version,
            ])
            ->withHeaders([
                'Cache-Control' => 'public, s-maxage=30, stale-while-revalidate=60',
            ]);
    }
}
