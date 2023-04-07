<?php

namespace App\Providers;

use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Transaction\DbTransaction;
use App\Repositories\Eloquent\{
    CategoryEloquentRepository,
    GenreEloquentRepository
};
use Core\Domain\Repository\{
    CategoryRepositoryInterface,
    GenreRepositoryInterface
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class
        );

        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class
        );

        /**
         * DB Transaction
         */
        $this->app->bind(
            TransactionInterface::class,
            DbTransaction::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
