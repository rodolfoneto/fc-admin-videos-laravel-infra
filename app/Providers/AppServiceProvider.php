<?php

namespace App\Providers;

use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Transaction\DbTransaction;
use App\Repositories\Eloquent\{
    CastMemberEloquentRepository,
    CategoryEloquentRepository,
    GenreEloquentRepository
};
use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
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
        //
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
