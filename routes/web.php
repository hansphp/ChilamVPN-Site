<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocaleFromRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $path = public_path('index.html');

    abort_unless(file_exists($path), 404);

    return response()->file($path, [
        'Content-Type' => 'text/html; charset=UTF-8',
    ]);
})->name('landing');

Route::middleware(SetLocaleFromRoute::class)->group(function () {
    Route::get('{locale}/{slug}', [HomeController::class, 'index'])
        ->where('locale', '[A-Za-z]{2}(?:-[A-Za-z0-9]{2,8})?')
        ->where('slug', '[a-z0-9-]+')
        ->name('ip.localized');
});
