<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DbTransaction;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interface\TransactionInterface;
use Tests\TestCase;

class BaseGenreUseCaseRepository extends TestCase
{
    protected GenreRepositoryInterface $repository;
    protected TransactionInterface $transaction;
    protected GenreModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transaction = new DbTransaction();
        $this->model = new GenreModel();


        $this->repository = new GenreEloquentRepository(
            model: $this->model,
            transaction: $this->transaction,
        );
    }
}
