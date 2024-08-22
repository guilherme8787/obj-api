<?php

namespace App\Providers;

use App\Repositories\Transacao\TransacaoRepository;
use App\Repositories\Transacao\TransacaoRepositoryContract;
use Illuminate\Support\ServiceProvider;

class TransacaoRepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransacaoRepositoryContract::class,
            TransacaoRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): array
    {
        return [
            TransacaoRepositoryContract::class,
        ];
    }
}
