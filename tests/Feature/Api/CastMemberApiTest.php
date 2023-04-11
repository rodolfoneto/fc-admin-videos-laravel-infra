<?php

namespace Tests\Feature\Api;

use Core\Domain\Enum\CastMemberType;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use App\Models\CastMember as CastMemberModel;

class CastMemberApiTest extends TestCase
{
    protected string $endpoint = '/cast-member';

    public function test_index_with_empty()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_index()
    {
        CastMemberModel::factory()->count(20)->create();
        $response = $this->getJson($this->endpoint);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
    }

    public function test_index_page_two()
    {
        CastMemberModel::factory()->count(20)->create();
        $response = $this->getJson("{$this->endpoint}?page=2");
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(16, $response['meta']['to']);
        $this->assertEquals(20, $response['meta']['from']);
    }

    public function test_index_with_filter_and_paginate()
    {
        CastMemberModel::factory()->count(20)->create();
        CastMemberModel::factory()->count(20)->create(['name' => 'Test ' . Str::random(5)]);
        $response = $this->getJson("{$this->endpoint}?filter=Test&page=2");
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(16, $response['meta']['to']);
        $this->assertEquals(20, $response['meta']['from']);
        $this->assertEquals(20, $response['meta']['total']);
    }

    public function test_store_invalid_params()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => "n",
        ]);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'type',
            ]
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_store()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => "new cast member",
            'type' => CastMemberType::ACTOR->value,
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'type', 'created_at'
            ]
        ]);
        $this->assertDatabaseHas('cast_members', [
            'name' => "new cast member",
            'type' => CastMemberType::ACTOR->value,
        ]);
    }

    public function test_update()
    {
        $castMember = CastMemberModel::factory()->create([
            'type' => CastMemberType::DIRECTOR->value,
        ]);
        $response = $this->putJson("{$this->endpoint}/{$castMember->id}", [
            'name' => "updated cast member",
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('cast_members', [
            'name' => "updated cast member",
        ]);
    }

    public function test_update_invalid_params()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->putJson("{$this->endpoint}/{$castMember->id}", [
            'name' => "u",
        ]);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_show()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->getJson("{$this->endpoint}/{$castMember->id}");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'type', 'created_at'
            ]
        ]);
    }

    public function test_show_invalid_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $response = $this->getJson("{$this->endpoint}/{$uuid}");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
    }

    public function test_delete_invalid_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $response = $this->deleteJson("{$this->endpoint}/{$uuid}");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
    }

    public function test_delete()
    {
        $castMember = CastMemberModel::factory()->create();
        $response = $this->deleteJson("{$this->endpoint}/{$castMember->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted($castMember);
    }
}
