<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Core\UseCase\DTO\CastMember\CastMemberUpdateInputDto;

class UpdateCastMemberUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository,
    ) {

    }

    public function execute(CastMemberUpdateInputDto $input): CastMemberOutputDto
    {
        if(!$castMember = $this->repository->findById($input->id)) {
            throw new NotFoundException("CastMember {$input->id} not founded");
        }
        $castMember->update(
            name: $input->name,
            type: CastMemberType::from($input->type),
        );
        $response = $this->repository->update($castMember);
        return new CastMemberOutputDto(
            id: $response->id(),
            name: $response->name,
            type: $response->type->value,
            created_at: $response->createdAt(),
        );
    }
}