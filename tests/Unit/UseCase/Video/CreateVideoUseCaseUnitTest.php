<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface,
};
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\Dto\{
    CreateVideoInputDto,
    CreateVideoOutputDto
};
use Core\UseCase\Interface\{FileStorageInterface, TransactionInterface};
use Core\UseCase\Video\Create\CreateVideoUseCase as UseCase;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    protected CreateVideoUseCase $usaCease;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new UseCase(
            repository: $this->createMockRepository(),
            transaction: $this->createMockTransaction(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),

            categoryRepository: $this->createMockRepositoryCategory(),
            genreRepository: $this->createMockRepositoryGenre(),
            castMemberRepository: $this->createMockRepositoryCastMember(),
        );
    }

    public function test_execute_input_output()
    {
        $useCase = $this->useCase;
        $output = $useCase->execute(
            input: $this->createMockInputDto(),
        );
        $this->assertInstanceOf(CreateVideoOutputDto::class, $output);
    }

    protected function createMockRepository()
    {
        $repository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $repository->shouldReceive('insert')->andReturn($this->createMockEntity());
        $repository->shouldReceive('updateMedia');
        return $repository;
    }

    protected function createMockTransaction()
    {
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit');
        $transaction->shouldReceive('rollback');
        return $transaction;
    }

    protected function createMockFileStorage()
    {
        $fileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $fileStorage->shouldReceive('store')->andReturn('path/fileReceived.png');
        return $fileStorage;
    }

    protected function createMockEventManager()
    {
        $eventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $eventManager->shouldReceive('dispatch');
        return $eventManager;
    }

    protected function createMockRepositoryCategory(array $categoriesId = [])
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')
            ->andReturn($categoriesId);
        return $mockCategoryRepository;
    }

    protected function createMockRepositoryGenre(array $genresId = [])
    {
        $mockGenreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockGenreRepository->shouldReceive('getIdsListIds')
            ->andReturn($genresId);
        return $mockGenreRepository;
    }

    protected function createMockRepositoryCastMember(array $castMembersId = [])
    {
        $mockCastMemberRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockCastMemberRepository->shouldReceive('getIdsListIds')
            ->andReturn($castMembersId);
        return $mockCastMemberRepository;
    }

    protected function createMockInputDto()
    {
        return Mockery::mock(CreateVideoInputDto::class, [
            'title',
            'desc',
            2023,
            12,
            true,
            Rating::RATE10,
            [],
            [],
            [],
        ]);
    }

    protected function createMockEntity()
    {
        $entity = Mockery::mock(Video::class, [
            "New Video",
            "Video Description",
            2023,
            10,
            true,
            Rating::RATE10,
        ]);
        return $entity;
    }
}
