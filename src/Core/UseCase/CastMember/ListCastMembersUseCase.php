<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\CastMember\CastMembersListInputDto;
use Core\UseCase\DTO\CastMember\CastMembersListOutputDto;

class ListCastMembersUseCase
{
    protected CastMemberRepositoryInterface $repository;

    public function __construct(
        CastMemberRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(CastMembersListInputDto $input): CastMembersListOutputDto
    {
        $response = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPerPage: $input->totalPerPage
        );
        return new CastMembersListOutputDto(
            items: $response->items(),
            total: $response->total(),
            last_page: $response->lastPage(),
            first_page: $response->firstPage(),
            current_page: $response->currentPage(),
            per_page: $response->perPage(),
            to: $response->to(),
            from: $response->from(),
        );
    }
}
