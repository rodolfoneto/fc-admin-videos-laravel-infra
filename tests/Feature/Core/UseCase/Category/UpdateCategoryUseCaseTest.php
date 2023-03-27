<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryUpdateInputDto;
use Tests\TestCase;

class UpdateCategoryUseCaseTest extends TestCase
{
    public function test_update()
    {
        $categoryModel = Model::factory()->create(['is_active' => true]);

        $model = new Model();
        $repository = new CategoryEloquentRepository($model);
        $useCase = new UpdateCategoryUseCase($repository);
        
        $inputDto = new CategoryUpdateInputDto(
            id: $categoryModel->id,
            name: "new cat",
            description: null,
            is_active: false,
        );

        $response = $useCase->execute($inputDto);
        
        $this->assertEquals("new cat", $response->name);
        $this->assertEquals($categoryModel->description, $response->description);
        $this->assertDatabaseHas(Model::class, [
            'id' => $response->id,
            'name' => $response->name,
            'description' => $response->description,
        ]);
    }
}
