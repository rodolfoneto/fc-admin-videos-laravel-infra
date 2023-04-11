<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberDeleteInputDto;
use App\Models\CastMember as CastMemberModel;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class DeleteCastMemberUseCaseTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_delete_invalid_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new DeleteCastMemberUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $useCase->execute(new CastMemberDeleteInputDto(id: $uuid));
    }

    public function test_delete()
    {
        $castMemberDb = CastMemberModel::factory()->create();
        $uuid = $castMemberDb->id;
        $useCase = new DeleteCastMemberUseCase($this->repository);
        $response = $useCase->execute(new CastMemberDeleteInputDto(id: $uuid));
        $this->assertSoftDeleted($castMemberDb);
        $this->assertTrue($response->success);
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
        parent::setUp();
    }
}
