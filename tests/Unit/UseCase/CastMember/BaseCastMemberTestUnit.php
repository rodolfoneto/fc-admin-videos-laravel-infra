<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\ValueObject\Uuid;
use stdClass;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Tests\TestCase;
use Mockery;

class BaseCastMemberTestUnit extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    protected function setUp(): void
    {
        Mockery::close();
        parent::setUp();
    }

    protected function mockEntity(string $uuid): CastMemberEntity
    {
        $entity = Mockery::mock(CastMemberEntity::class, [
            "New Entity",
            CastMemberType::DIRECTOR,
            new Uuid($uuid),
        ]);
        $entity->shouldReceive('id')->andReturn($uuid);
        $entity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        return $entity;
    }
}
