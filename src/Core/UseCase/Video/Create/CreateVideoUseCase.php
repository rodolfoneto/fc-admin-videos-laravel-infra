<?php

namespace Core\UseCase\Video\Create;

use Throwable;
use Core\UseCase\Video\Create\Dto\CreateVideoInputDto;
use Core\UseCase\Video\Create\Dto\CreateVideoOutputDto;


class CreateVideoUseCase extends BaseVideoUseCase
{
    public function execute(CreateVideoInputDto $input): CreateVideoOutputDto
    {
        $this->validateAllIds($input);
        $this->builderVideo->createEntity($input);

        try {
            $this->repository->insert(entity: $this->builderVideo->getEntity());
            $this->storageFiles($input);
            $this->repository->updateMedia(entity: $this->builderVideo->getEntity());
            $this->transaction->commit();
            return $this->createOutput();
        } catch (Throwable $th) {
            $this->transaction->rollback();
            //if(isset($pathMedia)) $this->storage->delete($pathMedia);
            throw $th;
        }
    }

    private function createOutput(): CreateVideoOutputDto
    {
        $video = $this->builderVideo->getEntity();
        return new CreateVideoOutputDto(
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
