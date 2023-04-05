<?php

namespace Tests\Feature\Core\UseCase\Genre;

use Core\Domain\Exception\NotFoundException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\UseCase\Genre\DeleteGenreUseCase;
use App\Models\{
    Category as CategoryModel,
    Genre as GenreModel,
};
use Core\UseCase\DTO\Genre\{
    GenreDeleteInputDto,
    GenreDeleteOutputDto,
};

class DeleteGenreUseCaseTest extends BaseGenreUseCaseRepository
{

    public function test_delete()
    {
        $genre = GenreModel::factory()->create();
        $useCase = new DeleteGenreUseCase($this->repository);
        $output = $useCase->execute(new GenreDeleteInputDto(id: $genre->id));
        $this->assertInstanceOf(GenreDeleteOutputDto::class, $output);
        $this->assertTrue($output->success);
    }

    public function test_delete_with_invalid_id()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $useCase = new DeleteGenreUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $useCase->execute(new GenreDeleteInputDto(id: $uuid));
    }
}
