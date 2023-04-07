<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Mockery;
use stdClass;
use PHPUnit\Framework\TestCase;
use Core\UseCase\DTO\CastMember\{
    CastMemberCreateInputDto,
    CastMemberCreateOutputDto,
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;

class CreateCastMemberUseCaseUnitTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

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

    protected function setUp(): void
    {
        Mockery::close();
        parent::setUp();
    }

    protected function mockRepository($timesCall = 1)
    {
        $uuid = RamseyUuid::uuid4()->toString();
        $entity = $this->mockEntity($uuid);
        $this->repository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->repository->shouldReceive('insert')->times($timesCall)->andReturn($entity);
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
