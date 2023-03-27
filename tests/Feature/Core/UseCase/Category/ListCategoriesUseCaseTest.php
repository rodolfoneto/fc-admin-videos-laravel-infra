<?php

namespace Tests\Feature\Core\UseCase\Category;


use Tests\TestCase;
use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\CategoriesListInputDto;
use Core\UseCase\DTO\Category\CategoriesListOutputDto;

class ListCategoriesUseCaseTest extends TestCase
{
    public function test_list_empty()
    {
        $inputDto = new CategoriesListInputDto(
            totalPerPage: 30
        );
        $useCase = $this->createRepository();
        $result = $useCase->execute($inputDto);
        $this->assertInstanceOf(CategoriesListOutputDto::class, $result);
        $this->assertCount(0, $result->items);
        $this->assertEquals(0, $result->total);
    }


    public function test_list_all()
    {
        Model::factory()->count(30)->create();
        $useCase = $this->createRepository();
        $inputDto = new CategoriesListInputDto(
            totalPerPage: 30
        );
        $result = $useCase->execute($inputDto);
        $this->assertInstanceOf(CategoriesListOutputDto::class, $result);
        $this->assertCount(30, $result->items);
    }
    

    private function createRepository()
    {
        $model = new Model();
        $repository = new CategoryEloquentRepository($model);
        return new ListCategoriesUseCase($repository);
    }
}
