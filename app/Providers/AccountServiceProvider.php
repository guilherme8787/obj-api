<?php

namespace App\Providers;

use App\Services\Accounts\AccountService;
use App\Services\Accounts\Contracts\AccountServiceContract;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AccountServiceContract::class,
            AccountService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): array
    {
        return [
            AccountServiceContract::class,
        ];
    }
}
