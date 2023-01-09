<?php

namespace App\Providers;

use App\Models\Movement;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user-product', function (User $user, Product $product) {
            return $user->id === $product->user_id;
        });

        Gate::define('user-movement', function (User $user, Movement $movement) {
            return $user->id === $movement->user_id;
        });
    }
}
