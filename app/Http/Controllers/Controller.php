<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected const CONTENT_SECURITY_POLICY = "default-src 'self'; connect-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://static.cloudflareinsights.com; img-src 'self' data:; font-src 'self' data:; base-uri 'self'; frame-ancestors 'none';";
}
