<?php

return [
    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => env('TIMEZONE', 'UTC'),

    'locale' => env('DEFAULT_LOCALE', 'en'),

    'providers' => [
        App\Providers\RouteServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
    ],

    'aliases' => [
        'App' => Illuminate\Support\Facades\App::class,
        'Route' => Illuminate\Support\Facades\Route::class,
    ],
];
