<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;

Route::get('/', function () {
    if (!file_exists(storage_path('installed.lock'))) {
        return redirect('/install');
    }

    return app(HomeController::class)();
});

Route::get('/install', [InstallController::class, 'show'])->name('install');
Route::post('/install', [InstallController::class, 'store']);
