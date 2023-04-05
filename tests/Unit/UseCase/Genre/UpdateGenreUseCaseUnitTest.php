<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreUpdateInputDto;
use Core\UseCase\DTO\Genre\GenreUpdateOutputDto;
use Core\UseCase\Interface\TransactionInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Mockery;
use Ramsey\Uuid\Uuid;
use stdClass;
use Core\UseCase\Genre\UpdateGenreUseCase;

class UpdateGenreUseCaseUnitTest extends BaseGenreTestUnit
{
    protected $transaction;
    protected $categoryRepository;
    protected $genre;

    public function test_update_genre()
    {
        $uuid = Uuid::uuid4()->toString();
        $this->prepareRepository($uuid);
        $this->mockCategoryRepository([$uuid]);
        $useCase = new UpdateGenreUseCase(
            $this->repository,
            $this->transaction,
            $this->categoryRepository,
        );
        $input = $this->mockInput($uuid, [$uuid]);
        $outputDto = $useCase->execute($input);
        $this->assertInstanceOf(GenreUpdateOutputDto::class, $outputDto);
    }

    public function test_update_genre_with_invalid_categories_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $this->prepareRepository($uuid, 0);
        $this->mockCategoryRepository([$uuid]);
        $useCase = new UpdateGenreUseCase(
            $this->repository,
            $this->transaction,
            $this->categoryRepository,
        );
        $input = $this->mockInput($uuid, ['FAKE']);
        $this->expectException(NotFoundException::class);
        $useCase->execute($input);
    }

    protected function mockInput($uuid, $categoriesId = [])
    {
        return Mockery::mock(GenreUpdateInputDto::class, [
            $uuid, 'name updated name', $categoriesId
        ]);
    }

    protected function prepareRepository($uuid, $timesCall = 1)
    {
        $this->repository->shouldReceive('findById')->once()->andReturn($this->mockEntity($uuid));
        $this->repository->shouldReceive('update')->times($timesCall)->andReturn();
    }

    protected function mockEntity(string $uuid)
    {
        $id = new ValueObjectUuid($uuid);
        $entity = Mockery::mock(Genre::class, [
            "new Genre", $id, true, []
        ]);
        $entity->shouldReceive('id')->andReturn($id);
        $entity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $entity->shouldReceive('update')->once();
        $entity->shouldReceive('addCategory');
        return $entity;
    }

    protected function mockTransaction()
    {
        $this->transaction = Mockery::mock(\stdClass::class, TransactionInterface::class);
        $this->transaction->shouldReceive('commit');
        $this->transaction->shouldReceive('rollback');
    }

    protected function mockCategoryRepository($catIds = [])
    {
        $this->categoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->categoryRepository->shouldReceive('getIdsListIds')->andReturn($catIds);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockTransaction();
    }
}
