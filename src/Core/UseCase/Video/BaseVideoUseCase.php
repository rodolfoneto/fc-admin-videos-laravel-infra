<?php

namespace Core\UseCase\Video;

use Core\Domain\Builder\Video\BuilderInterface;
use Core\Domain\Builder\Video\CreateVideoBuilder;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    EntityRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface};
use Core\UseCase\Interface\{
    FileStorageInterface,
    TransactionInterface
};
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;

abstract class BaseVideoUseCase
{
    protected BuilderInterface $builderVideo;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected TransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,

        protected CategoryRepositoryInterface $categoryRepository,
        protected GenreRepositoryInterface $genreRepository,
        protected CastMemberRepositoryInterface $castMemberRepository,
    ) {
        $this->builderVideo = $this->getBuilderInstance();
    }

    abstract protected function getBuilderInstance(): BuilderInterface;

    protected function storageFiles(object $input): void
    {
        $path = $this->builderVideo->getEntity()->id();
        if($pathVideoFile = $this->storageMedia(path: $path,  media: $input->videoFile)) {
            $this->builderVideo->addMediaVideo(
                path: $pathVideoFile,
                mediaStatus: MediaStatus::PROCESSING,
            );
            $this->eventManager->dispatch(
                event: new VideoCreatedEvent($this->builderVideo->getEntity())
            );
        }

        if($pathBannerFile = $this->storageMedia(path: $path,  media: $input->bannerFile)) {
            $this->builderVideo->addBanner(
                path: $pathBannerFile,
            );
        }

        if($pathThumbFile = $this->storageMedia(path: $path,  media: $input->thumbFile)) {
            $this->builderVideo->addThumb(
                path: $pathThumbFile,
            );
        }

        if($pathThumbHalfFile = $this->storageMedia(path: $path,  media: $input->thumbHalfFile)) {
            $this->builderVideo->addThumbHalf(
                path: $pathThumbHalfFile,
            );
        }

        if($pathTrailerFile = $this->storageMedia(path: $path,  media: $input->trailerFile)) {
            $this->builderVideo->addTrailer(
                path: $pathTrailerFile,
            );
        }
    }

    protected function storageMedia(string $path, ?array $media = null): ?string
    {
        if($media) {
            return $this->storage->store(
                path: $path,
                file: $media
            );
        }
        return null;
    }

    protected function validateAllIds(object $input): void
    {
        $this->validateEntityId(
            ids: $input->categories,
            repository: $this->categoryRepository,
            singleLabel: 'Category',
            pluralLabel: 'Categories'
        );
        $this->validateEntityId(
            ids: $input->genres,
            repository: $this->genreRepository,
            singleLabel: 'Genre'
        );
        $this->validateEntityId(
            ids: $input->castMembers,
            repository: $this->castMemberRepository,
            singleLabel: 'CastMember'
        );
    }

    /**
     * @param array $ids
     * @param EntityRepositoryInterface $repository
     * @return void
     * @throws NotFoundException
     */
    protected function validateEntityId(
        array $ids,
        EntityRepositoryInterface $repository,
        string $singleLabel,
        string $pluralLabel = null,
    ): void {
        $idsDb = $repository->getIdsListIds($ids);
        $idsDiff = array_diff($ids, $idsDb);
        if (count($idsDiff) > 0) {
            $msg = sprintf(
                '%s %s not found',
                count($idsDiff) > 1 ? $pluralLabel ?? "{$singleLabel}s" : $singleLabel,
                implode(', ', $idsDiff)
            );
            throw new NotFoundException($msg);
        }
    }
}
