<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IpToolController;
use App\Http\Middleware\SetLocaleFromRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landing'])->name('landing');

Route::middleware(SetLocaleFromRoute::class)->group(function () {
    Route::get('{locale}', [HomeController::class, 'show'])
        ->where('locale', '[A-Za-z]{2}(?:-[A-Za-z0-9]{2,8})?')
        ->name('home.localized');

    Route::get('{locale}/{slug}', [IpToolController::class, 'show'])
        ->where('locale', '[A-Za-z]{2}(?:-[A-Za-z0-9]{2,8})?')
        ->where('slug', '[a-z0-9-]+')
        ->name('tools.ip');
});
