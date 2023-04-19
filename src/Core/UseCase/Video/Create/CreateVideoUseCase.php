<?php

namespace Core\UseCase\Video\Create;

use Core\Domain\Entity\Entity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;
use http\Exception\UnexpectedValueException;
use Throwable;
use Core\Domain\Entity\Video;
use Core\UseCase\Video\Create\Dto\CreateVideoInputDto;
use Core\UseCase\Video\Create\Dto\CreateVideoOutputDto;
use Core\Domain\Repository\{CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    EntityRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface};
use Core\UseCase\Interface\{FileStorageInterface, TransactionInterface};
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;

class CreateVideoUseCase
{
    protected Video $entity;

    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected TransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,
        protected CategoryRepositoryInterface $categoryRepository,
        protected GenreRepositoryInterface $genreRepository,
        protected CastMemberRepositoryInterface $castMemberRepository,
    ) {}

    public function execute(CreateVideoInputDto $input): CreateVideoOutputDto
    {
        $this->entity = $this->createEntity(input: $input);

        try {
            $this->repository->insert(entity: $this->entity);
            $this->storageFiles($input);
            $this->repository->updateMedia($this->entity);
            $this->transaction->commit();
            return $this->createOutput($this->entity);
        } catch (Throwable $th) {
            $this->transaction->rollback();
//            if(isset($pathMedia)) $this->storage->delete($pathMedia);
            throw $th;
        }
    }

    private function createEntity(CreateVideoInputDto $input): Video
    {
        $this->validateAllIds($input);

        $entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
        );

        foreach ($input->categories as $categoryID) {
            $entity->addCategoryId($categoryID);
        }

        foreach ($input->genres as $genreId) {
            $entity->addGenreId($genreId);
        }

        foreach ($input->castMembers as $castMemberId) {
            $entity->addCastMemberId($castMemberId);
        }
        return $entity;
    }

    private function storageFiles(object $input): void
    {
        if($pathVideoFile = $this->storageMedia(path: $this->entity->id(),  media: $input->videoFile)) {
            $this->entity->setVideoFile($media = new Media(
                path: $pathVideoFile,
                mediaStatus: MediaStatus::PROCESSING,
            ));
            $this->eventManager->dispatch(
                event: new VideoCreatedEvent($this->entity)
            );
        }

        if($pathBannerFile = $this->storageMedia(path: $this->entity->id(),  media: $input->bannerFile)) {
            $this->entity->setBannerFile(new Image(
                path: $pathBannerFile,
            ));
        }

        if($pathThumbFile = $this->storageMedia(path: $this->entity->id(),  media: $input->thumbFile)) {
            $this->entity->setThumbFile(new Image(
                path: $pathThumbFile,
            ));
        }

        if($pathThumbHalfFile = $this->storageMedia(path: $this->entity->id(),  media: $input->thumbHalfFile)) {
            $this->entity->setThumbHalfFile(new Image(
                path: $pathThumbHalfFile,
            ));
        }

        if($pathTrailerFile = $this->storageMedia(path: $this->entity->id(),  media: $input->trailerFile)) {
            $this->entity->setTrailerFile(new Media(
                path: $pathTrailerFile,
                mediaStatus: MediaStatus::PROCESSING,
            ));
        }
    }

    private function storageMedia(string $path, ?array $media = null): ?string
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
            repository: $this->categoryRepository
        );
        $this->validateEntityId(
            ids: $input->genres,
            repository: $this->genreRepository
        );
        $this->validateEntityId(
            ids: $input->castMembers,
            repository: $this->castMemberRepository
        );
    }

    /**
     * @param array $ids
     * @param EntityRepositoryInterface $repository
     * @return void
     * @throws NotFoundException
     */
    private function validateEntityId(array $ids = [], EntityRepositoryInterface $repository): void
    {
        $idsDb = $repository->getIdsListIds($ids);
        $idsDiff = array_diff($ids, $idsDb);
        if (count($idsDiff) > 0) {
            $msg = sprintf(
                '%s %s not found',
                count($idsDiff) > 1 ? 'Entities' : 'Entity',
                implode(', ', $idsDiff)
            );
            throw new NotFoundException($msg);
        }
    }

    private function createOutput(Video $video): CreateVideoOutputDto
    {
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
