<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Mockery;
use stdClass;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\UseCase\DTO\CastMember\{
    CastMemberCreateInputDto,
    CastMemberCreateOutputDto,
};

class CreateCastMemberUseCaseUnitTest extends BaseCastMemberTestUnit
{
    public function test_create()
    {
        $this->mockRepository(1);
        $input = Mockery::mock(CastMemberCreateInputDto::class, ["New Entity", 1]);
        $useCase = new CreateCastMemberUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $output);
        $this->assertEquals("New Entity", $output->name);
        $this->assertNotEmpty($output->id);
        $this->assertEquals(1, $output->type);
    }

    public function test_create_with_invalid_name()
    {
        $this->mockRepository(0);
        $input = Mockery::mock(CastMemberCreateInputDto::class, ["N", 1]);
        $useCase = new CreateCastMemberUseCase($this->repository);
        $this->expectException(EntityValidationException::class);
        $useCase->execute($input);
    }

    protected function mockRepository($timesCall = 1)
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $entity = $this->mockEntity($uuid);
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('insert')->times($timesCall)->andReturn($entity);
    }
}
