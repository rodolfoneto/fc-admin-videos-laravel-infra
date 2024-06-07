<?php

namespace Core\UseCase\Video\Update;

use Core\Domain\Builder\Video\{
    BuilderInterface,
    UpdateVideoBuilder,
};
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\Video\BaseVideoUseCase;
use Core\UseCase\Video\Update\Dto\{InputDto, OutputDto,};
use Throwable;

class UseCase extends BaseVideoUseCase
{
    public function execute(InputDto $input): OutputDto
    {
        $this->validateAllIds($input);
        if(!$entity = $this->repository->findById($input->id)) {
            throw new NotFoundException('Video not founded');
        }
        $entity->update(
            title: $input->title,
            description: $input->description,
        );
        $this->builderVideo->setEntity($entity);
        $this->builderVideo->addIds($input);
        try {
            $this->repository->update(entity: $this->builderVideo->getEntity());
            $this->storageFiles(input: $input);
            $this->repository->updateMedia(entity: $this->builderVideo->getEntity());
            $this->transaction->commit();
            return $this->createOutput();
        } catch (Throwable $th) {
            $this->transaction->rollback();
        }
    }

    protected function getBuilderInstance(): BuilderInterface
    {
        return new UpdateVideoBuilder();
    }

    private function createOutput(): OutputDto
    {
        $video = $this->builderVideo->getEntity();
        return new OutputDto(
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
