<?php

namespace App\Providers;

use App\Repositories\Conta\ContaRepository;
use App\Repositories\Conta\ContaRepositoryContract;
use Illuminate\Support\ServiceProvider;

class ContaRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ContaRepositoryContract::class,
            ContaRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): array
    {
        return [
            ContaRepositoryContract::class,
        ];
    }
}
