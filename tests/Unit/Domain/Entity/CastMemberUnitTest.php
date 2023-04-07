<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;
use Core\Domain\Entity\CastMember;
use DateTime;

class CastMemberUnitTest extends TestCase
{
    public function test_create()
    {
        $entity = new CastMember(
            name: "New CastMember",
            type: CastMemberType::ACTOR,
        );

        $this->assertInstanceOf(CastMember::class, $entity);
        $this->assertEquals("New CastMember", $entity->name);
        $this->assertEquals(CastMemberType::ACTOR, $entity->type);
    }

    public function test_attributes()
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $entity = new CastMember(
            name: "New CastMember",
            type: CastMemberType::ACTOR,
            id: new Uuid($uuid),
            createdAt: new DateTime(),
        );

        $this->assertInstanceOf(CastMember::class, $entity);
        $this->assertEquals($uuid, $entity->id());
        $this->assertEquals("New CastMember", $entity->name);
        $this->assertInstanceOf(DateTime::class, $entity->createdAt);
    }

    public function test_attributes_with_invalid_name()
    {
        $this->expectException(EntityValidationException::class);
        new CastMember(
            name: "N",
            type: CastMemberType::DIRECTOR
        );
    }

    public function test_update()
    {
        $uuid = Uuid::random();
        $entity = new CastMember(
            name: "New CastMember",
            type: CastMemberType::ACTOR,
            id: $uuid,
        );
        $entity->update(name: "Updated", type: CastMemberType::DIRECTOR);
        $this->assertEquals("Updated", $entity->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $entity->type);
    }
}
