<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
use Illuminate\Http\Response;
use Tests\TestCase;

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
        $categories = ModelCategory::factory()->count(30)->create();

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
        $response->assertJsonCount(15, 'data');
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
}
