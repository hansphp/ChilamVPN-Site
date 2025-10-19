<?php

use App\Http\Controllers\Api\GeoController;
use App\Http\Controllers\Api\IpController;
use Illuminate\Support\Facades\Route;

Route::middleware('noindex')->group(function () {
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('ip', [IpController::class, 'show'])->name('api.ip.show');
        Route::get('geo', [GeoController::class, 'show'])->name('api.geo.show');
    });

    Route::middleware('throttle:geo-lookup')->group(function () {
        Route::get('lookup', [GeoController::class, 'lookup'])->name('api.geo.lookup');
    });
});
