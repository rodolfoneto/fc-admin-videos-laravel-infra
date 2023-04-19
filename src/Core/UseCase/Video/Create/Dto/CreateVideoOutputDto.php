<?php

namespace Core\UseCase\Video\Create\Dto;

use Core\Domain\Enum\Rating;

class CreateVideoOutputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public bool $opened,
        public Rating $rating,
        public array $categories = [],
        public array $genres = [],
        public array $castMembers = [],
        protected ?string $thumbFile = null,
        protected ?string $thumbHalfFile = null,
        protected ?string $bannerFile = null,
        protected ?string $trailerFile = null,
        protected ?string $videoFile = null,
    ) {}
}
