<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\{
    CastMemberDeleteInputDto,
    CastMemberDeleteOutputDto,
};

class DeleteCastMemberUseCase
{

    public function __construct(
        protected CastMemberRepositoryInterface $repository,
    ) {

    }

    public function execute(CastMemberDeleteInputDto $input): CastMemberDeleteOutputDto
    {
        if(!$castMember = $this->repository->findById(uuid: $input->id)) {
            throw new NotFoundException("CastMember {$input->id} not founded");
        }
        $response = $this->repository->delete($input->id);
        return new CastMemberDeleteOutputDto(success: $response);
    }
}
