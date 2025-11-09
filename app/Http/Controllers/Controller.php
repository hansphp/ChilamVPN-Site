<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected const CONTENT_SECURITY_POLICY = "default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self' data:; base-uri 'self'; frame-ancestors 'none';";
}
