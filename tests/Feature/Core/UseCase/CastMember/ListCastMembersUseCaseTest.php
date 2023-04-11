<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use App\Models\CastMember as CastMemberModel;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\CastMembersListInputDto;
use Core\UseCase\DTO\CastMember\CastMembersListOutputDto;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListCastMembersUseCaseTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_list_empty()
    {
        $useCase = new ListCastMembersUseCase($this->repository);
        $result = $useCase->execute(new CastMembersListInputDto());
        $this->assertInstanceOf(CastMembersListOutputDto::class, $result);
        $this->assertCount(0, $result->items);
        $this->assertEquals(0, $result->total);
    }

    public function test_paginate_all()
    {
        CastMemberModel::factory()->count(30)->create();
        $useCase = new ListCastMembersUseCase($this->repository);
        $result = $useCase->execute(new CastMembersListInputDto());
        $this->assertInstanceOf(CastMembersListOutputDto::class, $result);
        $this->assertCount(15, $result->items);
        $this->assertEquals(30, $result->total);
    }

    public function test_paginate_with_filter()
    {
        CastMemberModel::factory()->count(20)->create();
        CastMemberModel::factory()->count(20)->create(['name' => 'Test' . Str::random(5)]);
        $useCase = new ListCastMembersUseCase($this->repository);
        $result = $useCase->execute(new CastMembersListInputDto(
            filter: 'Test'
        ));
        $this->assertInstanceOf(CastMembersListOutputDto::class, $result);
        $this->assertCount(15, $result->items);
        $this->assertEquals(20, $result->total);
    }

    public function test_paginate_with_10_per_page()
    {
        CastMemberModel::factory()->count(40)->create();
        $useCase = new ListCastMembersUseCase($this->repository);
        $result = $useCase->execute(new CastMembersListInputDto(
            totalPerPage: 10
        ));
        $this->assertInstanceOf(CastMembersListOutputDto::class, $result);
        $this->assertCount(10, $result->items);
        $this->assertEquals(40, $result->total);
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
        parent::setUp();
    }
}
