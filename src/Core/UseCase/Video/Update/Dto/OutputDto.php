<?php

namespace Core\UseCase\Video\Update\Dto;

use Core\Domain\Enum\Rating;

class OutputDto
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
        public ?string $thumbFile = null,
        public ?string $thumbHalfFile = null,
        public ?string $bannerFile = null,
        public ?string $trailerFile = null,
        public ?string $videoFile = null,
    ) {}
}
