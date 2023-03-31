<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Category;
use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Genre\{
    CreateGenreUseCase,
    ListGenresUseCase,
};
use Core\UseCase\Dto\Genre\{
    GenreCreateInputDto,
    GenreOutputDto,
};
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Interface\TransactionInterface;
use Mockery;
use stdClass;

class CreateGenreUseCaseUnitTest extends BaseGenreTestUnit
{
    public function test_create_a_genre()
    {
        $uuidCategory = Uuid::random();

        $categoryUseCaseMock = $this->mockCategoryUseCase($uuidCategory);
        $transaction = $this->mockTrasaction();
        $uuid = Uuid::random();
        $entity = $this->createEntity($uuid);

        $this->repository->shouldReceive('insert')->andReturn($entity);
        $input = Mockery::mock(GenreCreateInputDto::class, [
            "new entity",
            [(string) $uuidCategory],
            true
        ]);
        $useCase = new CreateGenreUseCase($this->repository, $transaction, $categoryUseCaseMock);
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreOutputDto::class, $output);
        $this->assertEquals("new entity", $output->name);
        $this->assertTrue($output->is_active);
    }

    public function test_create_a_genre_with_invalid_category()
    {
        $uuidCategory = Uuid::random();
        $categoryUseCaseMock = $this->mockCategoryUseCase($uuidCategory);
        $transaction = $this->mockTrasaction();
        $uuid = Uuid::random();
        $entity = $this->createEntity($uuid);

        $this->repository->shouldReceive('insert')->andReturn($entity);
        $input = Mockery::mock(GenreCreateInputDto::class, [
            "new entity",
            [(string) $uuidCategory,'Cat_ID_FAKE_1', 'Cat_ID_FAKE_2'],
            true
        ]);
        $useCase = new CreateGenreUseCase($this->repository, $transaction, $categoryUseCaseMock);
        $this->expectException(NotFoundException::class);
        $useCase->execute($input);
    }

    private function createEntity($uuid)
    {
        $entity = Mockery::mock(Genre::class, [
            "new entity",
            Uuid::random(),
            true,
            []
        ]);
        $entity->shouldReceive('id')->andReturn((string) $uuid);
        $entity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        return $entity;
    }

    private function mockCategoryUseCase($uuidCategory)
    {
        $categoryUseCaseMock = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryUseCaseMock->shouldReceive('getIdsListIds')->andReturn([(string) $uuidCategory]);
        return $categoryUseCaseMock;
    }

    private function mockTrasaction(): TransactionInterface
    {
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit');
        $transaction->shouldReceive('rollback');
        return $transaction;
    }
}
