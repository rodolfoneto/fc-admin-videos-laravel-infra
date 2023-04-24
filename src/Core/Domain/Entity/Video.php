<?php

namespace Core\Domain\Entity;

use Core\Domain\Factory\VideoValidatorFactory;
use Core\Domain\Notification\NotificationException;
use DateTime;
use Core\Domain\Enum\Rating;
use Core\Domain\ValueObject\{
    Media,
    Uuid,
    Image,
};

class Video extends Entity
{
    protected array $categoriesId = [];
    protected array $genresId = [];
    protected array $castMembersId = [];

    public function __construct(
        protected string $title,
        protected string $description,
        protected int $yearLaunched,
        protected int $duration,
        protected bool $opened,
        protected Rating $rating,
        protected ?Uuid $id = null,
        protected bool $publish = false,
        protected ?Image $thumbFile = null,
        protected ?Image $thumbHalf = null,
        protected ?Image $bannerFile = null,
        protected ?Media $trailerFile = null,
        protected ?Media $videoFile = null,
        protected ?DateTime $createdAt = null,
    ) {
        parent::__construct();
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();
        $this->validate();
    }

    public function addCategoryId(string $categoryId): void
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategoryId(string $categoryId):void
    {
        $indexOf = array_search($categoryId, $this->categoriesId);
        if($indexOf === false) {
            return;
        }
        array_splice($this->categoriesId, $indexOf, 1);
    }

    public function addGenreId(string $genreId): void
    {
        array_push($this->genresId, $genreId);
    }

    public function removeGenreId(string $genreId):void
    {
        $indexOf = array_search($genreId, $this->genresId);
        if($indexOf === false) {
            return;
        }
        array_splice($this->genresId, $indexOf, 1);
    }

    public function addCastMemberId(string $castMemberId): void
    {
        array_push($this->castMembersId, $castMemberId);
    }

    public function removeCastMemberId(string $castMemberId): void
    {
        $indexOf = array_search($castMemberId, $this->castMembersId);
        if($indexOf === false) {
            return;
        }
        array_splice($this->castMembersId, $indexOf, 1);
    }

    public function thumbFile(): ?Image
    {
        return $this->thumbFile;
    }

    public function setThumbFile(Image $thumbFile): void
    {
        $this->thumbFile = $thumbFile;
    }

    public function thumbHalfFile(): ?Image
    {
        return $this->thumbHalf;
    }

    public function setThumbHalfFile(Image $thumbHalf): void
    {
        $this->thumbHalf = $thumbHalf;
    }

    public function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public function setBannerFile(Image $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }

    public function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public function setTrailerFile(Media $trailerFile): void
    {
        $this->trailerFile = $trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    public function setVideoFile(Media $videoFiles): void
    {
        $this->videoFile = $videoFiles;
    }

    protected function validate()
    {
        $validator = VideoValidatorFactory::create();
        $validator->validate($this);
        if($this->notification->hasErrors()) {
            throw new NotificationException($this->notification->getMessage('video'));
        }
    }
}
