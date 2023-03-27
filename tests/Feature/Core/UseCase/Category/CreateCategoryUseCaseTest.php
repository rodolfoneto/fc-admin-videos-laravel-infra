<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CategoryCreateOutputDto;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{
    public function test_create()
    {
        $model = new Model();
        $repository = new CategoryEloquentRepository($model);
        $useCase = new CreateCategoryUseCase($repository);
        
        $inputDto = new CategoryCreateInputDto(
            name: "new cat",
        );
        $response = $useCase->execute($inputDto);
        
        $this->assertEquals("new cat", $response->name);
        $this->assertNotEmpty($response->id);
        $this->assertDatabaseHas(Model::class, ['id' => $response->id]);
    }
}
