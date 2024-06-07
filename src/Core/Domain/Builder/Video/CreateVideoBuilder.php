<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;

class CreateVideoBuilder implements BuilderInterface
{
    protected ?Video $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    private function reset(): void
    {
        $this->entity = null;
    }

    public function createEntity(object $input): BuilderInterface
    {
        $this->entity = new Video(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
        );
        $this->addIds($input);
        return $this;
    }

    public function addIds(object $input): BuilderInterface
    {
        foreach ($input->categories as $categoryID) {
            $this->entity->addCategoryId($categoryID);
        }

        foreach ($input->genres as $genreId) {
            $this->entity->addGenreId($genreId);
        }

        foreach ($input->castMembers as $castMemberId) {
            $this->entity->addCastMemberId($castMemberId);
        }

        return $this;
    }

    public function addMediaVideo(string $path, MediaStatus $mediaStatus, string $encodedPath = ''): BuilderInterface
    {
        $this->entity->setVideoFile(new Media(
            path: $path,
            mediaStatus: $mediaStatus,
            encodedPath: $encodedPath,
        ));
        return $this;
    }

    public function addTrailer(
        string $path,
        MediaStatus $mediaStatus = MediaStatus::COMPLETE,
        string $encodedPath = ''
    ): BuilderInterface
    {
        $this->entity->setTrailerFile(new Media(
            path: $path,
            mediaStatus: $mediaStatus,
            encodedPath: $encodedPath,
        ));
        return $this;
    }

    public function addThumb(string $path): BuilderInterface
    {
        $this->entity->setThumbFile(new Image(path: $path));
        return $this;
    }

    public function addThumbHalf(string $path): BuilderInterface
    {
        $this->entity->setThumbHalfFile(new Image(path: $path));
        return $this;
    }

    public function addBanner(string $path): BuilderInterface
    {
        $this->entity->setBannerFile(new Image(path: $path));
        return $this;
    }

    public function getEntity(): Video
    {
        return $this->entity;
    }
}
