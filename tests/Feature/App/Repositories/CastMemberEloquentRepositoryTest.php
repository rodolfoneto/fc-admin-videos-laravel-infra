<?php

namespace Tests\Feature\App\Repositories;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;
use Core\Domain\Entity\CastMember as Entity;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected CastMemberEloquentRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new Model());
        parent::setUp();
    }

    public function test_check_implements_interface()
    {
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
    }

    public function test_insert()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $castMember = new Entity(
            name: 'new castmember',
            type: CastMemberType::ACTOR,
            id: new Uuid($uuid),
        );
        $response = $this->repository->insert($castMember);
        $this->assertDatabaseHas('cast_members', [
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);
        $this->assertInstanceOf(Entity::class, $response);
        $this->assertEquals($castMember->id(), $response->id());
        $this->assertEquals($castMember->name, $response->name);
        $this->assertEquals($castMember->type, $response->type);
    }

    public function test_update()
    {
        $castMember = Model::factory()->create([
            'type' => CastMemberType::DIRECTOR->value
        ]);
        $castMemberUpdate = new Entity(
            name: "updated",
            type: CastMemberType::ACTOR,
            id: new Uuid($castMember->id),
        );
        $response = $this->repository->update($castMemberUpdate);
        $this->assertInstanceOf(Entity::class, $response);
        $this->assertEquals("updated", $response->name);
        $this->assertEquals(CastMemberType::ACTOR, $response->type);
    }

    public function test_update_id_not_founded()
    {
        $castMember = Model::factory()->create();
        $castMemberUpdate = new Entity(
            name: "updated",
            type: CastMemberType::ACTOR,
            id: Uuid::random(),
        );
        $this->expectException(NotFoundException::class);
        $this->repository->update($castMemberUpdate);
    }

    public function test_delete_id_not_founded()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete(RamseyUuid::uuid4()->toString());
    }

    public function test_delete()
    {
        $castMember = Model::factory()->create();
        $result = $this->repository->delete(uuid: $castMember->id);
        $this->assertTrue($result);
        $this->assertSoftDeleted($castMember);
    }

    public function test_find_by_id()
    {
        $model = Model::factory()->create();
        $response = $this->repository->findById($model->id);
        $this->assertInstanceOf(Entity::class, $response);
    }

    public function test_find_by_id_not_funded()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $this->expectException(NotFoundException::class);
        $this->repository->findById($uuid);
    }

    public function test_paginate()
    {
        $total = 60;
        Model::factory()->count($total)->create();
        $response = $this->repository->paginate();
        $this->assertCount(15, $response->items());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals($total, $response->total());
        $this->assertEquals(4, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(15, $response->from());
    }

    public function test_paginate_total_page()
    {
        $total = 20;
        Model::factory()->count($total)->create();
        $response = $this->repository->paginate(totalPerPage: 10);
        $this->assertCount(10, $response->items());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(10, $response->perPage());
        $this->assertEquals($total, $response->total());
        $this->assertEquals(2, $response->lastPage());
        $this->assertEquals(1, $response->to());
        $this->assertEquals(10, $response->from());
    }

    public function test_paginate_with_filter()
    {
        Model::factory()->count(15)->create();
        Model::factory()->count(16)->create(['name' => 'Test ' . Str::random(10)]);
        $response = $this->repository->paginate(
            filter: 'Test',
        );
        $this->assertCount(15, $response->items());
        $this->assertEquals(1, $response->currentPage());
        $this->assertEquals(15, $response->perPage());
        $this->assertEquals(16, $response->total());
        $this->assertEquals(2, $response->lastPage());
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

    public function test_get_all()
    {
        $total = 60;
        Model::factory()->count($total)->create();
        $response = $this->repository->findAll();
        $this->assertCount($total, $response);
    }
}
