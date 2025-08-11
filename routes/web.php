<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    if (!file_exists(storage_path('installed.lock'))) {
        return redirect('/install');
    }

    return app(HomeController::class)();
});

Route::get('/install', [InstallController::class, 'show'])->name('install');
Route::post('/install', [InstallController::class, 'store']);

Route::get('clients/{client}/export', [ClientController::class, 'export'])->name('clients.export');
Route::resource('clients', ClientController::class);
Route::post('clients/{client}/activities', [ClientController::class, 'storeActivity'])->name('clients.activities.store');
Route::get('projects/{project}/export', [ProjectController::class, 'export'])->name('projects.export');
Route::resource('projects', ProjectController::class);
Route::resource('projects.tasks', TaskController::class)->except(['index', 'create', 'show']);
Route::post('projects/{project}/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('projects.tasks.comments.store');

Route::middleware('auth')->group(function () {
    Route::get('files/upload', [FileController::class, 'create'])->name('files.create');
    Route::post('files', [FileController::class, 'store'])->name('files.store');
    Route::get('files/{file}/download', [FileController::class, 'download'])->name('files.download')->middleware('signed');
});
