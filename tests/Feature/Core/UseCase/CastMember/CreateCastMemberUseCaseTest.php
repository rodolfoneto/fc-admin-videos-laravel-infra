<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use App\Models\CastMember as CastMemberModel;
use Core\UseCase\DTO\CastMember\{
    CastMemberCreateOutputDto,
    CastMemberCreateInputDto,
};
use Tests\TestCase;

class CreateCastMemberUseCaseTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    public function test_create()
    {
        $useCase = new CreateCastMemberUseCase($this->repository);
        $output = $useCase->execute(new CastMemberCreateInputDto(
            name: "New CastMember",
            type: CastMemberType::ACTOR->value
        ));
        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $output);
        $this->assertNotEmpty($output->id);
        $this->assertDatabaseHas('cast_members', [
            'id' => $output->id,
            'name' => $output->name,
            'type'=> $output->type,
        ]);
    }

    public function test_create_invalid_params()
    {
        $useCase = new CreateCastMemberUseCase($this->repository);
        $this->expectException(EntityValidationException::class);
        $useCase->execute(new CastMemberCreateInputDto(
            name: "N",
            type: CastMemberType::ACTOR->value
        ));
    }

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMemberModel());
        parent::setUp();
    }
}
