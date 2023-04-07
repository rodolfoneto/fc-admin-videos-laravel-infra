<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\CastMemberCreateOutputDto;

class CreateCastMemberUseCase
{
    protected CastMemberRepositoryInterface $repository;

    public function __construct(
        CastMemberRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(CastMemberCreateInputDto $input): CastMemberCreateOutputDto
    {
        $castMember = new CastMember(
            name: $input->name,
            type: CastMemberType::from($input->type),
        );

        $response = $this->repository->insert($castMember);

        return new CastMemberCreateOutputDto(
            id: $response->id(),
            name: $response->name,
            type: $response->type->value,
            created_at: $response->createdAt(),
        );
    }
}
