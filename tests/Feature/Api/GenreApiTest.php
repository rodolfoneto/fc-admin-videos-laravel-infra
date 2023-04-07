<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Genre as GenreModel;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected string $endpoint = '/genre';

    public function test_list_empty_genre()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_paginate()
    {
        GenreModel::factory()->count(20)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'total',
                'per_page',
                'first_page',
                'last_page',
                'current_page',
                 'to',
                'from',
                'last_page',
            ]
        ]);
        $this->assertCount(15, $response['data']);
    }

    public function test_store()
    {
        $data = ['name' => 'New Genre'];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertDatabaseHas('genres', ['name' => 'New Genre']);
    }

    public function test_store_invalid_name()
    {
        $data = ['name' => 'N'];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure(['message', 'errors' => ['name']]);
        $this->assertDatabaseEmpty('genres');
    }

    public function test_store_with_categories_id()
    {
        $categoriesId = Category::factory()
            ->count(10)
            ->create()
            ->pluck('id')
            ->toArray();
        $data = [
            'name' => 'New Genre',
            'is_active' => true,
            'categories_id' => $categoriesId
        ];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('genres', ['name' => 'New Genre']);
        $this->assertDatabaseCount('category_genre', 10);
    }

    public function test_store_with_categories_id_invalid()
    {
        $categoriesId = ['FAKE'];
        $data = [
            'name' => 'New Genre',
            'is_active' => true,
            'categories_id' => $categoriesId
        ];
        $response = $this->postJson($this->endpoint, $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_show_not_found()
    {
        $response = $this->getJson("{$this->endpoint}/Uuid_FAKE");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
    }

    public function test_show()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->getJson("{$this->endpoint}/{$genre->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at',
            ]
        ]);
    }

    public function test_update_not_found()
    {
        $uuid = Uuid::uuid4()->toString();
        $response = $this->putJson("{$this->endpoint}/{$uuid}", ['name' => 'New updated']);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
    }

    public function test_update()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->putJson("{$this->endpoint}/{$genre->id}", ['name' => 'New updated']);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('genres', ['name' => 'New updated']);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
    }

    public function test_update_with_categories_ids()
    {
        $category = Category::factory()->create();
        $genre = GenreModel::factory()->create();
        $response = $this->putJson("{$this->endpoint}/{$genre->id}", [
            'name' => 'New updated',
            'categories_id' => [$category->id],
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('genres', ['name' => 'New updated']);
        $this->assertDatabaseCount('category_genre', 1);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
    }

    public function test_delete_not_found()
    {
        $uuid = Uuid::uuid4()->toString();
        $response = $this->deleteJson("{$this->endpoint}/{$uuid}");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
    }

    public function test_delete()
    {
        $genre = GenreModel::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$genre->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted($genre);
    }
}
