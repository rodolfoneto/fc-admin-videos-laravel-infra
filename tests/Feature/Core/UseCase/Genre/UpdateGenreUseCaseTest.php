<?php

namespace Tests\Feature\Core\UseCase\Genre;


use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\GenreUpdateInputDto;
use Core\UseCase\DTO\Genre\GenreUpdateOutputDto;
use Core\UseCase\Genre\UpdateGenreUseCase;
use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;

class UpdateGenreUseCaseTest extends BaseGenreUseCaseRepository
{
    public function test_update()
    {
        $genre = GenreModel::factory()->create();
        $input = new GenreUpdateInputDto(
            id: $genre->id,
            name: "New Genre",
            is_active: false,
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreUpdateOutputDto::class, $output);
        $this->assertEquals($input->name, $output->name);
        $this->assertDatabaseHas('genres', ['name' => $input->name]);
    }

    public function test_update_invalid_id()
    {
        $uuid = Uuid::random();
        GenreModel::factory()->create();
        $input = new GenreUpdateInputDto(
            id: $uuid,
            name: "New Genre",
            is_active: false,
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $this->expectException(NotFoundException::class);
        $useCase->execute($input);
    }

    public function test_update_invalid_name()
    {
        $genre = GenreModel::factory()->create();
        $input = new GenreUpdateInputDto(
            id: $genre->id,
            name: "N",
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $this->expectException(EntityValidationException::class);
        $useCase->execute($input);
    }

    public function test_update_with_categories_id()
    {
        $genre = GenreModel::factory()->create();
        $categoriesId = CategoryModel::factory()
            ->count(5)
            ->create()
            ->pluck('id')
            ->toArray();

        $input = new GenreUpdateInputDto(
            id: $genre->id,
            name: "New Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreUpdateOutputDto::class, $output);
        $this->assertEquals($input->name, $output->name);
        $this->assertDatabaseHas('genres', ['name' => $input->name]);
        $this->assertDatabaseCount('category_genre', 5);
    }

    public function test_update_with_invalid_categories_id()
    {
        $genre = GenreModel::factory()->create();
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $categoriesId = CategoryModel::factory()
            ->count(5)
            ->create()
            ->pluck('id')
            ->toArray();
        array_push($categoriesId, $uuid);
        $input = new GenreUpdateInputDto(
            id: $genre->id,
            name: "Updated Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $this->expectException(NotFoundException::class);
        $useCase->execute($input);
    }

    public function test_transaction_update_rollback()
    {
        $genre = GenreModel::factory()->create();
        $categoriesId = CategoryModel::factory()
            ->count(5)
            ->create()
            ->pluck('id')
            ->toArray();
        $categoriesId[] = 'FAKE';
        $input = new GenreUpdateInputDto(
            id: $genre->id,
            name: "New Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new UpdateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        try{
            $output = $useCase->execute($input);
            $this->assertFalse($output);
        } catch (NotFoundException $th) {
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }

    protected function setUp(): void
    {
        $this->categoryModel = new CategoryModel();
        $this->categoryRepository = new CategoryEloquentRepository($this->categoryModel);
        parent::setUp();
    }
}
