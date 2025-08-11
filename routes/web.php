<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    if (!file_exists(storage_path('installed.lock'))) {
        return redirect('/install');
    }

    return app(HomeController::class)();
});

Route::get('/install', [InstallController::class, 'show'])->name('install');
Route::post('/install', [InstallController::class, 'store']);

Route::resource('clients', ClientController::class);
Route::post('clients/{client}/activities', [ClientController::class, 'storeActivity'])->name('clients.activities.store');
Route::resource('projects', ProjectController::class);
Route::resource('projects.tasks', TaskController::class)->except(['index', 'create', 'show']);
