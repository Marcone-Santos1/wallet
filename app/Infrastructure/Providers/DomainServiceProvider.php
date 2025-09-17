<?php

namespace Infrastructure\Providers;

use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Database\Repositories\EloquentTransactionRepository;
use Infrastructure\Database\Repositories\EloquentWalletRepository;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WalletRepositoryInterface::class,
            EloquentWalletRepository::class
        );

        $this->app->bind(
            TransactionRepositoryInterface::class,
            EloquentTransactionRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
