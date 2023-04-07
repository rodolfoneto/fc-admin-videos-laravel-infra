<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\CastMembersListInputDto;
use Mockery;
use Ramsey\Uuid\Uuid as RamseyUuid;

class ListCastMembersUseCaseUnitTest extends BaseCastMemberTestUnit
{
    public function test_find_all()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $entity = $this->mockEntity($uuid);
        $this->mockRepository(1, [$entity, $entity, $entity]);
        $input = Mockery::mock(CastMembersListInputDto::class, []);
        $useCase = new ListCastMembersUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertCount(3, $output->items);
        $this->assertEquals(15, $output->per_page);
        $this->assertEquals(3, $output->total);
        $this->assertEquals(1, $output->current_page);
    }

    public function test_find_all_empty()
    {
        $this->mockRepository(1);
        $input = Mockery::mock(CastMembersListInputDto::class, []);
        $useCase = new ListCastMembersUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertCount(0, $output->items);
    }

    protected function mockRepository($timesCall = 1, array $items = [])
    {
        $pagination = $this->mockPagination($items);
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('paginate')->times($timesCall)->andReturn($pagination);
    }

    protected function mockPagination(array $items = [])
    {
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive('items')->andReturn($items);
        $mockPagination->shouldReceive('total')->andReturn(count($items));
        $mockPagination->shouldReceive('lastPage')->andReturn(1);
        $mockPagination->shouldReceive('firstPage')->andReturn(1);
        $mockPagination->shouldReceive('currentPage')->andReturn(1);
        $mockPagination->shouldReceive('perPage')->andReturn(15);
        $mockPagination->shouldReceive('to')->andReturn(1);
        $mockPagination->shouldReceive('from')->andReturn(1);
        return $mockPagination;
    }
}
