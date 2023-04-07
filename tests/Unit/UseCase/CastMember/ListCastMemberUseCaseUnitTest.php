<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\UseCase\DTO\CastMember\{
    CastMemberListInputDto,
    CastMemberOutputDto,
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Mockery;
use Ramsey\Uuid\Uuid as RamseyUuid;

class ListCastMemberUseCaseUnitTest extends BaseCastMemberTestUnit
{
    public function test_find_by_id()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $input = Mockery::mock(CastMemberListInputDto::class, [$uuid]);
        $this->mockRepository($uuid, 1);
        $useCase = New ListCastMemberUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertInstanceOf(CastMemberOutputDto::class, $output);
        $this->assertEquals($uuid, $output->id);
        $this->assertNotEmpty($output->type);
        $this->assertNotEmpty($output->name);
        $this->assertNotEmpty($output->created_at);
    }

    protected function mockRepository(string $uuid = '', $timesCall = 1)
    {
        $entity = $this->mockEntity($uuid);
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('findById')->times($timesCall)->andReturn($entity);
    }
}
