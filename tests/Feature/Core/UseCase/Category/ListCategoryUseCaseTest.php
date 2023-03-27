<?php

namespace Tests\Feature\Core\UseCase\Category;


use Tests\TestCase;
use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\{
    CategoryInputDto,
    CategoryOutputDto
};

class ListCategoryUseCaseTest extends TestCase
{

    public function test_list()
    {
        $categoryModel = Model::factory()->create();
        $useCase = $this->createRepository();
        $inputDto = new CategoryInputDto(id: $categoryModel->id);
        $result = $useCase->execute($inputDto);
        $this->assertModelExists($categoryModel);
        $this->assertEquals($categoryModel->id, $result->id);
        $this->assertEquals($categoryModel->name, $result->name);
        $this->assertEquals($categoryModel->description, $result->description);
        $this->assertEquals($categoryModel->created_at, $result->created_at);
    }
    

    private function createRepository()
    {
        $model = new Model();
        $repository = new CategoryEloquentRepository($model);
        return new ListCategoryUseCase($repository);
    }
}
