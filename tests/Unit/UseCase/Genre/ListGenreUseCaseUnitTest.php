<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\{
    GenreOutputDto,
    GenreInputDto
};
use Mockery;
use Core\UseCase\Genre\ListGenreUseCase;
use stdClass;

class ListGenreUseCaseUnitTest extends BaseGenreTestUnit
{
    public function test_list_genre()
    {
        $uuid = Uuid::random();
        $genre = Mockery::mock(Genre::class, [
            "new genre test",
            $uuid,
        ]);
        $genre->shouldReceive('id')->andReturn($uuid);
        $genre->shouldReceive('createdAt')->andReturn('');
        $this->repository->shouldReceive('findById')->times(1)->andReturn($genre);
        $inputDto = Mockery::mock(GenreInputDto::class, [$uuid]);
        $useCase = new ListGenreUseCase($this->repository);
        $output = $useCase->execute($inputDto);
        $this->assertInstanceOf(GenreOutputDto::class, $output);
        $this->assertEquals($uuid, $output->id);
        $this->assertEquals("new genre test", $output->name);
    }
}
