<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ToolRouterController;
use App\Http\Middleware\SetLocaleFromRoute;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landing'])->name('landing');

Route::middleware(SetLocaleFromRoute::class)->group(function () {
    $localePattern = '[A-Za-z]{2}(?:-[A-Za-z0-9]{2,8})?';

    Route::get('{locale}', [HomeController::class, 'show'])
        ->where('locale', $localePattern)
        ->name('home.localized');

    Route::get('{locale}/{slug}', ToolRouterController::class)
        ->where('locale', $localePattern)
        ->where('slug', '[a-z0-9-]+')
        ->name('tools.show');
});
