<?php

namespace Core\UseCase\Video\Delete;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\Dto\{
    DeleteVideoInputDto,
    DeleteVideoOutputDto,
};

class DeleteVideoUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository,
    ){}

    public function execute(DeleteVideoInputDto $input): DeleteVideoOutputDto
    {
        $video = $this->repository->findById(uuid: $input->id);
        $response = $this->repository->delete($video->id());
        return new DeleteVideoOutputDto(
            success: $response,
        );
    }
}
