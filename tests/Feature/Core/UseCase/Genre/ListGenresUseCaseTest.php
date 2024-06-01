<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use Core\UseCase\DTO\Genre\GenresListInputDto;
use Core\UseCase\DTO\Genre\GenresListOutputDto;
use Core\UseCase\Genre\ListGenresUseCase;

class ListGenresUseCaseTest extends BaseNoTransactionGenreUseCaseRepository
{
    public function test_get_all()
    {
        GenreModel::factory()->count(100)->create();
        $useCase = new ListGenresUseCase($this->repository);
        $output = $useCase->execute(new GenresListInputDto());
        $this->assertInstanceOf(GenresListOutputDto::class, $output);
        $this->assertCount(15, $output->items);
        $this->assertEquals(100, $output->total);
    }
}
