<?php

namespace Tests\Feature\App\Repositories;

use App\Models\Category;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DbTransaction;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use App\Models\Genre as ModelGenre;
use Core\Domain\ValueObject\Uuid;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{
    protected GenreRepositoryInterface $repository;

    public function test_create_genre()
    {
        $genre = new EntityGenre(name: "Test");
        $this->repository->insert($genre);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id(),
        ]);
    }

    public function test_create_genre_deactivated()
    {
        $genre = new EntityGenre(name: "Test", isActive: true);
        $genre->deactivate();
        $genreResponse = $this->repository->insert($genre);
        $this->assertInstanceOf(EntityGenre::class, $genreResponse);
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id(),
            'is_active' => false
        ]);
    }

    public function test_insert_with_relationship()
    {
        $genre = new EntityGenre(name: "New Genre");
        $categories = Category::factory()->count(4)->create();
        foreach ($categories as $category) {
            $genre->addCategory($category->id);
        }

        $response = $this->repository->insert($genre);
        $this->assertDatabaseHas('genres', ['id' => $response->id]);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertCount(4, $response->categoriesId);
        $this->assertDatabaseCount('category_genre', 4);
    }

    public function test_not_found_by_findbyid()
    {
        $id = 'FAKE';
        $this->expectException(NotFoundException::class);
        $this->repository->findById($id);
    }

    public function test_find_by_id()
    {
        $genreDb = ModelGenre::factory()->create();
        $response = $this->repository->findById($genreDb->id);
        $this->assertInstanceOf(EntityGenre::class, $response);
        $this->assertEquals($genreDb->id, $response->id());
        $this->assertEquals($genreDb->name, $response->name);
    }

    public function test_find_all()
    {
        ModelGenre::factory()->count(10)->create();
        $response = $this->repository->findAll();
        $this->assertCount(10, $response);
    }

    public function test_find_all_with_empty_result()
    {
        $response = $this->repository->findAll();
        $this->assertCount(0, $response);
    }

    public function test_implementation_interface()
    {
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
    }

    public function test_find_all_with_filter()
    {
        $genres = ModelGenre::factory()->count(10)->create();
        $response = $this->repository->findAll($genres[5]->name);
        $this->assertCount(1, $response);
        $this->assertEquals($genres[5]->name, $response[0]->name);
    }

    public function test_find_all_with_filter_with_many_results()
    {
        ModelGenre::factory()->count(10)->create(['name' => 'Test']);
        ModelGenre::factory()->count(10)->create();
        $response10 = $this->repository->findAll('Test');
        $response20 = $this->repository->findAll();
        $this->assertCount(10, $response10);
        $this->assertCount(20, $response20);
    }

    public function test_paginate()
    {
        $total = 60;
        ModelGenre::factory()->count($total)->create();
        $response = $this->repository->paginate();
        $this->assertCount(15, $response->items());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals($total, $response->total());
        $this->assertEquals(4, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(15, $response->from());
    }

    public function test_paginate_with_empty_result()
    {
        $response = $this->repository->paginate();
        $this->assertCount(0, $response->items());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(0, $response->total());
        $this->assertEquals(1, $response->lastPage());
        $this->assertEquals(0, $response->to());
        $this->assertEquals(0, $response->from());
    }

//    public function test_paginate_second_page()
//    {
//        ModelGenre::factory()->count(20)->create();
//        $response = $this->repository->paginate(
//            page: 2,
//        );
//        dd($response->currentPage());
//        $this->assertCount(5, $response->items());
//        $this->assertEquals(2, $response->currentPage());
//        $this->assertEquals(15, $response->perPage());
//        $this->assertEquals(10, $response->total());
//        $this->assertEquals(1, $response->lastPage());
//        $this->assertEquals(1, $response->to());
//        $this->assertEquals(10, $response->from());
//    }

    public function test_update()
    {
        $genre = ModelGenre::factory()->create(['is_active' => true]);
        $entity = new EntityGenre(
            name: 'New Name',
            id: new Uuid($genre->id),
            isActive: $genre->is_active,
            createdAt: $genre->createdAt,
        );
        $result = $this->repository->update($entity);
        $this->assertEquals('New Name', $result->name);
        $this->assertDatabaseHas('genres', [
            'name' => 'New Name',
        ]);
    }

    public function test_update_not_found()
    {
        $entity = new EntityGenre(
            name: 'New Name',
            id: Uuid::random(),
        );
        $this->expectException(NotFoundException::class);
        $this->repository->update($entity);
    }

    public function test_delete_with_not_exists()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('FAKE');
    }

    public function test_delete()
    {
        $genre = ModelGenre::factory()->create();
        $result = $this->repository->delete($genre->id);
        $this->assertSoftDeleted($genre);
        $this->assertTrue($result);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $model = new ModelGenre();
        $transaction = new DbTransaction();
        $this->repository = new GenreEloquentRepository($model, $transaction);
    }
}
