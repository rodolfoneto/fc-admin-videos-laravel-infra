<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CategoryDeleteInputDto;
use Tests\TestCase;

class DeleteCategoryUseCaseTest extends TestCase
{
    public function test_delete()
    {
        $model = new Model();
        $repository = new CategoryEloquentRepository($model);
        $useCase = new DeleteCategoryUseCase($repository);
        
        $categoryModel = Model::factory()->create();

        $inputDto = new CategoryDeleteInputDto(
            id: $categoryModel->id,
        );
        $useCase->execute($inputDto);
        $this->assertSoftDeleted($categoryModel);
    }
}
