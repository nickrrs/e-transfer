<?php

namespace App\Providers;

use App\Models\Seller;
use App\Models\User;
use App\Observers\SellerObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Seller::observe(SellerObserver::class);
    }
}
