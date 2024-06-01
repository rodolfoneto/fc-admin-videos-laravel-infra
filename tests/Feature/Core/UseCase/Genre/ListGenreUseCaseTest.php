<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Ramsey\Uuid\Uuid;

class ListGenreUseCaseTest extends BaseNoTransactionGenreUseCaseRepository
{
    public function test_get_genre()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new ListGenreUseCase($this->repository);
        $output = $useCase->execute(new GenreInputDto(id: $genre->id));
        $this->assertInstanceOf(GenreOutputDto::class, $output);
        $this->assertEquals($genre->id, $output->id);
        $this->assertEquals($genre->name, $output->name);
    }

    public function test_get_genre_with_invalid_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new ListGenreUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $useCase->execute(new GenreInputDto(id: $uuid));
    }
}
