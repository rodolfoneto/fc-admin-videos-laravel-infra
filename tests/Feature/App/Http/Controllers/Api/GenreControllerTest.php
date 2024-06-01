<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DbTransaction;
use Core\Domain\Repository\GenreRepositoryInterface;
use App\Models\Genre as GenreModel;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    protected GenreRepositoryInterface $repository;
    protected GenreController $controller;
    protected TransactionInterface $transaction;

    public function test_index()
    {
        $useCase = new ListGenresUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new GenreController();
        $this->repository = new GenreEloquentRepository(new GenreModel());
    }
}
