<?php

namespace Tests\Feature\App\Repositories;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{

    protected CategoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new Model());
    }

    public function testInsert()
    {
        $entity = new EntityCategory(
            name: 'Test'
        );
        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', ['name' => $entity->name]);
    }

    public function testFindById()
    {
        $category = Model::factory()->create();
        $categoryEntity = $this->repository->findById($category->id);
        $this->assertInstanceOf(EntityCategory::class, $categoryEntity);
        $this->assertEquals($category->id, $categoryEntity->id());
    }

    public function testFindByIdWithNotFound()
    {
        $uuidNotExists = "hjgjwehgrjwhrgw";
        $this->expectException(NotFoundException::class);
        $categoryEntity = $this->repository->findById($uuidNotExists);
    }

    public function testFindAll()
    {
        $qty = 10;
        Model::factory()->count($qty)->create();
        $response = $this->repository->findAll();
        $this->assertCount($qty, $response);
    }

    public function testPaginate()
    {
        $qty = 50;
        Model::factory()->count($qty)->create();
        $paginate = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $paginate);
        $this->assertCount(15, $paginate->items());
        $this->assertEquals($qty, $paginate->total());
    }

    public function testPaginateWithEmptyResult()
    {
        $paginate = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $paginate);
        $this->assertCount(0, $paginate->items());
        $this->assertEquals(0, $paginate->total());
    }

    public function testUpdateCategoryIdNotFound(): void
    {
        $model = Model::factory()->create();
        $entity = new EntityCategory(
            id: Str::uuid(),
            name: "new name",
            description: "new description",
        );
        $this->expectException(NotFoundException::class);
        $this->repository->update($entity);
    }

    public function testUpdateCategory(): void
    {
        $model = Model::factory()->create([
            'is_active' => true,
        ]);
        $entity = new EntityCategory(
            id: $model->id,
            name: "new name",
            description: "new description",
            isActive: false,
        );
        $response = $this->repository->update($entity);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', ['name' => 'new name']);
        $this->assertDatabaseHas('categories', ['description' => 'new description']);
        $this->assertDatabaseHas('categories', ['is_active' => '0']);
    }

    public function testUpdateCategoryInvalidName(): void
    {
        $nameCategory = "n";
        $model = Model::factory()->create();
        $this->expectException(EntityValidationException::class);
        $entity = new EntityCategory(
            id: $model->id,
            name: $nameCategory,
            description: "new description",
        );
        $this->repository->update($entity);
    }


    public function testUpdateCategoryInvalidLengthDescription(): void
    {
        $nameCategory = "new name";
        $descriptionCategory = Str::random(256);
        $model = Model::factory()->create();
        $this->expectException(EntityValidationException::class);
        $entity = new EntityCategory(
            id: $model->id,
            name: $nameCategory,
            description: $descriptionCategory,
        );
        $this->repository->update($entity);
    }

    public function testDeleteCategoryNotFound()
    {
        $id = Str::uuid();
        $this->expectException(NotFoundException::class);
        $this->repository->delete($id);
    }


    public function testDeleteCategory()
    {
        $categoryModel = Model::factory()->create();
        $result = $this->repository->delete($categoryModel->id);
        $this->assertTrue($result);
    }
}
