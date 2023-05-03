<?php

namespace Core\UseCase\Video\Get;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Get\Dto\{
    GetVideoInputDto,
    GetVideoOutputDto
};

class GetVideoUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository
    ) {}

    public function execute(GetVideoInputDto $input): GetVideoOutputDto
    {
        $video = $this->repository->findById(uuid: $input->id);
        return new GetVideoOutputDto(
            id: $video->id(),
            title: $video->title,
            description: $video->description,
            yearLaunched: $video->yearLaunched,
            duration: $video->duration,
            opened: $video->opened,
            rating: $video->rating,
            categories: $video->categoriesId,
            genres: $video->genresId,
            castMembers: $video->castMembersId,
            thumbFile: $video->thumbFile()?->path(),
            thumbHalfFile: $video->thumbHalfFile()?->path(),
            bannerFile: $video->bannerFile()?->path(),
            trailerFile: $video->trailerFile()?->path(),
            videoFile: $video->videoFile()?->path(),
        );
    }
}
