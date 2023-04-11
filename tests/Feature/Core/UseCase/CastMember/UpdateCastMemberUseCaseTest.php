<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Core\UseCase\DTO\CastMember\CastMemberUpdateInputDto;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class UpdateCastMemberUseCaseTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_update()
    {
        $castMemberDb = CastMemberModel::factory()->create();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $output = $useCase->execute(new CastMemberUpdateInputDto(
            id: $castMemberDb->id,
            name: "New CastMember",
            type: CastMemberType::ACTOR->value
        ));
        $this->assertInstanceOf(CastMemberOutputDto::class, $output);
        $this->assertNotEmpty($output->id);
        $this->assertDatabaseHas('cast_members', [
            'id' => $output->id,
            'name' => $output->name,
            'type'=> $output->type,
        ]);
    }

    public function test_update_invalid_param()
    {
        $castMemberDb = CastMemberModel::factory()->create();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $this->expectException(EntityValidationException::class);
        $useCase->execute(new CastMemberUpdateInputDto(
            id: $castMemberDb->id,
            name: "N",
            type: CastMemberType::ACTOR->value
        ));
    }

    public function test_update_invalid_id()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new UpdateCastMemberUseCase($this->repository);
        $this->expectException(NotFoundException::class);
        $useCase->execute(new CastMemberUpdateInputDto(
            id: $uuid,
            name: "New CastMember",
            type: CastMemberType::ACTOR->value
        ));
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
        parent::setUp();
    }
}
