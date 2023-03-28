<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;
class CategoryApiTest extends TestCase
{

    protected $endpoint = '/categories';

    public function testListEmptyCategories()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllCategories()
    {
        ModelCategory::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from'
            ],
        ]);
        $response->assertJsonCount(15, 'data');
        $this->assertEquals(30, $response['meta']['total']);
    }

    public function testPaginateCategories()
    {
        $categories = ModelCategory::factory()->count(25)->create();

        $response = $this->getJson("{$this->endpoint}?page=2");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from'
            ],
        ]);
        $response->assertJsonCount(10, 'data');
        $this->assertEquals($categories['15']->id, $response['data'][0]['id']);
        $this->assertEquals(2, $response['meta']['current_page']);
    }

    public function testListCategoryNotFound()
    {
        $response = $this->getJson("{$this->endpoint}/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListCategory()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$category->id}");
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_active', 'created_at']
        ]);
        $this->assertEquals($category->id, $response['data']['id']);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testValidationStore()
    {
        $data = [
            'name' => ''
        ];
        $response = $this->postJson($this->endpoint, $data);

        $response->assertJsonStructure([
            'message',
            'errors' => ['name'],
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseEmpty('categories');
    }

    public function testValidationIsActiveStore()
    {
        $data = [
            'name' => 'new name',
            'is_active' => 'aaaa',
        ];
        $response = $this->postJson($this->endpoint, $data);

        $response->assertJsonStructure([
            'message',
            'errors' => ['is_active'],
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseEmpty('categories');
    }

    public function testValidationDescriptionTooLongStore()
    {
        $data = [
            'name' => 'new name',
            'description' => Str::random(256),
        ];
        $response = $this->postJson($this->endpoint, $data);

        $response->assertJsonStructure([
            'message',
            'errors' => ['description'],
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseEmpty('categories');
    }

    public function testStoreCategory()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'new cat',
            'description' => 'new desc',
            'is_active' => false,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'created_at'],
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => 'new cat',
            'description' => 'new desc',
            'is_active' => false
        ]);
    }

    public function testUpdateNotFoundCategory()
    {
        $response = $this->putJson("{$this->endpoint}/fake_id", [
            'name' => 'new name',
        ]);
        $this->assertEquals("Entity not founded", $response['message']);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNameNotValidCategory()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$category->id}", [
            'name' => 'n',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name'],
        ]);
    }

    public function testUpdateNameTooLargeCategory()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$category->id}", [
            'name' => Str::random(300),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name'],
        ]);
    }

    public function testUpdateDescriptionTooLargeCategory()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$category->id}", [
            'name' => 'New Name',
            'description' => Str::random(300),
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['description'],
        ]);
    }

    public function testUpdateIsActiveNotBooleanCategory()
    {
        $category = ModelCategory::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$category->id}", [
            'name' => 'New Name',
            'description' => Str::random(100),
            'is_active' => 'not valid',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['is_active'],
        ]);
    }

    public function testUpdateCategory()
    {
        $category = ModelCategory::factory()->create(['is_active' => true]);

        $response = $this->putJson("{$this->endpoint}/{$category->id}", [
            'name' => 'New Name',
            'description' => 'new description',
            'is_active' => false,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('categories', [
            'name' => 'New Name',
            'description' => 'new description',
            // 'is_active' => false
        ]);
    }

    public function testDeleteNotFoundCategory()
    {
        $response = $this->deleteJson("{$this->endpoint}/fake_id");
        $this->assertEquals("Entity not founded", $response['message']);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteCategory()
    {
        $category = ModelCategory::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$category->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted($category);
    }
}
