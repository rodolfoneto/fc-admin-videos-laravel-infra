<?php

namespace Tests\Unit\UseCase\CastMember;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\{
    CastMemberDeleteInputDto,
    CastMemberDeleteOutputDto,
};

class DeleteCastMemberUseCaseUnitTest extends BaseCastMemberTestUnit
{
    public function test_delete()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $this->mockRepository($uuid, 1);
        $input = new CastMemberDeleteInputDto(id: $uuid);
        $useCase = new DeleteCastMemberUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertTrue($output->success);
    }

    protected function mockRepository($uuid, $timesCall = 1)
    {
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('delete')->with($uuid)->times($timesCall)->andReturn(true);
        $this->repository->shouldReceive('findById')
            ->with($uuid)
            ->times(1)
            ->andReturn($this->mockEntity($uuid));
    }
}
