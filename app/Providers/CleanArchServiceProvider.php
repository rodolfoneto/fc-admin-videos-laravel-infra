<?php

namespace App\Providers;

use App\Events\VideoEvent;
use App\Services\Storage\FileStorage;
use App\Repositories\Eloquent\{CastMemberEloquentRepository,
    CategoryEloquentRepository,
    GenreEloquentRepository,
    VideoEloquentRepository};
use Core\Domain\Repository\{CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface};
use App\Repositories\Transaction\DbTransaction;
use Core\UseCase\Interface\FileStorageInterface;
use Core\UseCase\Interface\TransactionInterface;

use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Illuminate\Support\ServiceProvider;

class CleanArchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->bindRepositories();

        $this->app->singleton(
            abstract: FileStorageInterface::class,
            concrete: FileStorage::class
        );

        $this->app->singleton(
            abstract: VideoEventManagerInterface::class,
            concrete: VideoEvent::class
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
     * Bind of Repositories
     * @return void
     */
    private function bindRepositories(): void
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class
        );

        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class
        );

        $this->app->singleton(
            CastMemberRepositoryInterface::class,
            CastMemberEloquentRepository::class
        );

        $this->app->singleton(
            abstract: VideoRepositoryInterface::class,
            concrete: VideoEloquentRepository::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
