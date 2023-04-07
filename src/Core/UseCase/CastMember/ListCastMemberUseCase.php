<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\{
    CastMemberListInputDto,
    CastMemberOutputDto
};

class ListCastMemberUseCase
{
    protected CastMemberRepositoryInterface $repository;

    public function __construct(CastMemberRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CastMemberListInputDto $input): CastMemberOutputDto
    {
        $result = $this->repository->findById($input->id);
        return new CastMemberOutputDto(
            id: $result->id(),
            name: $result->name,
            type: $result->type->value,
            created_at: $result->createdAt(),
        );
    }
}
