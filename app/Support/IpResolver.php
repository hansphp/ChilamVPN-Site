<?php

namespace App\Support;

use Illuminate\Http\Request;

class IpResolver
{
    public function clientIp(Request $request): ?string
    {
        foreach ($this->candidateIps($request) as $ip) {
            if ($this->isPublicIp($ip)) {
                return $ip;
            }
        }

        return null;
    }

    private function candidateIps(Request $request): array
    {
        $candidates = [];

        if ($forwarded = $request->headers->get('Forwarded')) {
            foreach ($this->extractForwardedIps($forwarded) as $ip) {
                $candidates[] = $ip;
            }
        }

        foreach (['CF-Connecting-IP', 'True-Client-IP'] as $header) {
            if ($ip = $request->headers->get($header)) {
                $candidates[] = $ip;
            }
        }

        if ($xff = $request->headers->get('X-Forwarded-For')) {
            foreach (explode(',', $xff) as $ip) {
                $ip = trim($ip);
                if ($ip !== '') {
                    $candidates[] = $ip;
                }
            }
        }

        if ($remote = $request->server->get('REMOTE_ADDR')) {
            $candidates[] = $remote;
        }

        return $candidates;
    }

    private function extractForwardedIps(string $forwardedHeader): array
    {
        $ips = [];

        foreach (explode(',', $forwardedHeader) as $part) {
            $pairs = explode(';', trim($part));

            foreach ($pairs as $pair) {
                if (str_starts_with(strtolower($pair), 'for=')) {
                    $value = trim(substr($pair, 4));
                    $value = trim($value, '"');

                    if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                        $value = trim($value, '[]');
                    } elseif (($pos = strpos($value, ':')) !== false && substr_count($value, ':') === 1) {
                        // IPv4 with port
                        $value = substr($value, 0, $pos);
                    } elseif (str_contains($value, ']')) {
                        // IPv6 with port: [2001:db8::1]:1234
                        $value = preg_replace('/^\[(.*)\](?::\d+)?$/', '$1', $value);
                    }

                    $ips[] = $value;
                }
            }
        }

        return $ips;
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    public function ipVersion(?string $ip): ?string
    {
        if ($ip === null) {
            return null;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? 'IPv6' : 'IPv4';
    }
}
