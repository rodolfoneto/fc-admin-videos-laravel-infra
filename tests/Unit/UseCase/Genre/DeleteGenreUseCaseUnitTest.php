<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\GenreDeleteInputDto;
use Core\UseCase\DTO\Genre\GenreDeleteOutputDto;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Ramsey\Uuid\Uuid;
use Mockery;

class DeleteGenreUseCaseUnitTest extends BaseGenreTestUnit
{
    public function test_delete_genre()
    {
        $uuid = Uuid::uuid4()->toString();
        $this->prepareRepository($uuid, true);
        $useCase = new DeleteGenreUseCase($this->repository);
        $input = $this->mockInput($uuid);
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreDeleteOutputDto::class, $output);
        $this->assertTrue($output->success);
    }

    public function test_delete_genre_with_result_false()
    {
        $uuid = Uuid::uuid4()->toString();
        $this->prepareRepository(uuid: $uuid, result: false);
        $useCase = new DeleteGenreUseCase($this->repository);
        $input = $this->mockInput($uuid);
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreDeleteOutputDto::class, $output);
        $this->assertFalse($output->success);
    }

    private function mockInput(string $uuid): GenreDeleteInputDto
    {
        return new GenreDeleteInputDto(
            id: $uuid,
        );
    }

    private function prepareRepository(string $uuid, bool $result = true)
    {
        $entity = Mockery::mock(Genre::class, [
            "new name",
            new ValueObjectUuid($uuid),
            true,
            [],
        ]);
        $entity->shouldReceive('id')->andReturn($uuid);

        $this->
            repository->
            shouldReceive('findById')
            ->andReturn($entity);
        $this->repository->shouldReceive('delete')->times(1)->andReturn($result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
