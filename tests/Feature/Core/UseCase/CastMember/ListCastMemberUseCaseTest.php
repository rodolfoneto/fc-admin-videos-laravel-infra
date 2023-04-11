<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use App\Models\CastMember as CastMemberModel;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberListInputDto;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ListCastMemberUseCaseTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_find_by_id()
    {
        $castMember = CastMemberModel::factory()->create();
        $useCase = new ListCastMemberUseCase($this->repository);
        $output = $useCase->execute(new CastMemberListInputDto(id: $castMember->id));
        $this->assertInstanceOf(CastMemberOutputDto::class, $output);
        $this->assertEquals($castMember->id, $output->id);
    }

    public function test_find_by_id_not_found()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new ListCastMemberUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $useCase->execute(new CastMemberListInputDto(id: $uuid));
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
        parent::setUp();
    }
}
