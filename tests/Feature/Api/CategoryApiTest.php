<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
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
        $categories = ModelCategory::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
    }
}
