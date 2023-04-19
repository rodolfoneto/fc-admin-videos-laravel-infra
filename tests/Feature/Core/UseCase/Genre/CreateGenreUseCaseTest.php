<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Notification\NotificationException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Ramsey\Uuid\Uuid;


class CreateGenreUseCaseTest extends BaseGenreUseCaseRepository
{
    protected CategoryModel $categoryModel;
    protected CategoryRepositoryInterface $categoryRepository;

    public function test_create()
    {
        $input = new GenreCreateInputDto(
            name: "New Genre",
            is_active: false,
        );
        $useCase = new CreateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreOutputDto::class, $output);
        $this->assertEquals($input->name, $output->name);
        $this->assertDatabaseCount('genres', 1);
    }

    public function test_create_invalid_name()
    {
        $input = new GenreCreateInputDto(
            name: "N",
        );
        $useCase = new CreateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $this->expectException(NotificationException::class);
        $useCase->execute($input);
    }

    public function test_create_with_categories_id()
    {
        $categories = CategoryModel::factory()->count(2)->create();
        $categoriesId = [];
        foreach ($categories as $category) {
            array_push($categoriesId, $category->id);
        }
        $input = new GenreCreateInputDto(
            name: "New Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new CreateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $output = $useCase->execute($input);
        $this->assertInstanceOf(GenreOutputDto::class, $output);
        $this->assertDatabaseCount('genres', 1);
        $this->assertDatabaseCount('category_genre', 2);
    }

    public function test_create_with_invalid_categories_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $categoriesId = [$uuid];
        $input = new GenreCreateInputDto(
            name: "New Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new CreateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        $this->expectException(NotFoundException::class);
        $useCase->execute($input);
    }

    public function test_transaction_insert_rollback()
    {
        $uuid = Uuid::uuid4()->toString();
        $categoriesId = [$uuid];
        $input = new GenreCreateInputDto(
            name: "New Genre",
            categoriesId: $categoriesId,
        );
        $useCase = new CreateGenreUseCase(
            repository: $this->repository,
            transaction: $this->transaction,
            categoryRepository: $this->categoryRepository
        );
        try{
            $output = $useCase->execute($input);
            $this->assertFalse($output);
        } catch (\Throwable $th) {
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
