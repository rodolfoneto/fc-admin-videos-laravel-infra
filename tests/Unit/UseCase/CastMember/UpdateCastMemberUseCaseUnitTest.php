<?php

namespace Tests\Unit\UseCase\CastMember;


use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Mockery;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\UseCase\DTO\CastMember\{
    CastMemberUpdateInputDto,
};

class UpdateCastMemberUseCaseUnitTest extends BaseCastMemberTestUnit
{
    public function test_update()
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $entity = $this->mockEntity($uuid);
        $entityUpdated = $this->mockEntity($uuid, "updated", CastMemberType::ACTOR);
        $this->mockRepository(1, $entity, $entityUpdated);
        $input = new CastMemberUpdateInputDto(
            id: $uuid,
            name: "updated",
        );
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $output = $useCase->execute($input);
        $this->assertEquals($uuid, $output->id);
        $this->assertEquals("updated", $output->name);
    }

    protected function mockRepository($timesCall = 1, $entity, $entityUpdated)
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $entity = $this->mockEntity($uuid);
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('update')->times($timesCall)->andReturn($entityUpdated);
        $this->repository->shouldReceive('findById')->times(1)->andReturn($entity);
    }

    protected function mockEntity(
        string $uuid,
        string $name = "New Entity",
        CastMemberType $type = CastMemberType::DIRECTOR
    ): CastMemberEntity
    {
        $entity = Mockery::mock(CastMemberEntity::class, [
            $name,
            $type,
            new Uuid($uuid),
        ]);
        $entity->shouldReceive('id')->andReturn($uuid);
        $entity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $entity->shouldReceive('update');
        return $entity;
    }
}
