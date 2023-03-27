<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Controller;
use App\Http\Requests\{
    StoreCategoryRequest,
    UpdateCategoryRequest
};
use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\{
    CreateCategoryUseCase,
    DeleteCategoryUseCase,
    ListCategoriesUseCase,
    ListCategoryUseCase,
    UpdateCategoryUseCase,
};
use Illuminate\Http\{
    JsonResponse,
    Request,
    Response,
};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    protected CategoryRepositoryInterface $repository;
    protected Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new CategoryController();
        $this->repository = new CategoryEloquentRepository(new CategoryModel());
    }

    public function testIndex()
    {
        $useCase = new ListCategoriesUseCase($this->repository);
        $this->controller = new CategoryController();
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $useCase = new CreateCategoryUseCase($this->repository);

        $request = new StoreCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => "new cat",
            'desc' => "new desc",
            'is_active' => true
        ]));

        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
        $this->assertDatabaseHas('categories', ['name' => "new cat"]);
    }

    public function testShow()
    {
        $useCase = new ListCategoryUseCase($this->repository);
        $category = CategoryModel::factory()->create();
        $id = $category->id;
        $response = $this->controller->show($id, $useCase);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new UpdateCategoryUseCase($this->repository);
        $request = new UpdateCategoryRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'updated',
        ]));
        $response = $this->controller->update(
            request: $request,
            id: $category->id,
            useCase: $useCase,
        );
        $this->assertDatabaseHas('categories', ['name' => 'updated']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testDelete()
    {
        $category = CategoryModel::factory()->create();
        $useCase = new DeleteCategoryUseCase($this->repository);
        $response = $this->controller->delete(
            id: $category->id,
            useCase: $useCase,
        );
        $this->assertSoftDeleted($category);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
