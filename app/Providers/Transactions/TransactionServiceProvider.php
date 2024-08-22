<?php

namespace App\Providers\Transactions;

use App\Services\Transactions\Contracts\TransactionServiceContract;
use App\Services\Transactions\TransactionService;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionServiceContract::class,
            TransactionService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): array
    {
        return [
            TransactionServiceContract::class,
        ];
    }
}
