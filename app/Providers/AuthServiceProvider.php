<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('admin', fn (User $user) => $user->role === 'admin');
        Gate::define('manager', fn (User $user) => in_array($user->role, ['admin', 'manager']));
        Gate::define('client', fn (User $user) => in_array($user->role, ['admin', 'manager', 'client']));
    }
}
