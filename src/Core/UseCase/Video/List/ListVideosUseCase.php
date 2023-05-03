<?php

namespace Core\UseCase\Video\List;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\Dto\{ListVideosInputDto, ListVideosOutputDto};

class ListVideosUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository,
    ) {}

    public function execute(ListVideosInputDto $input): ListVideosOutputDto
    {
        $response = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPerPage: $input->totalPerPage,
        );

        return new ListVideosOutputDto(
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
