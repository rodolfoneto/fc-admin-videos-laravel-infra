<?php

namespace Core\UseCase\Video\Create\Dto;

use Core\Domain\Enum\Rating;

class CreateVideoInputDto
{
    public function __construct(
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public bool $opened,
        public Rating $rating,
        public array $categories,
        public array $genres,
        public array $castMembers,
        public ?array $thumbFile = null,
        public ?array $thumbHalfFile = null,
        public ?array $bannerFile = null,
        public ?array $trailerFile = null,
        public ?array $videoFile = null,
    ) {}
}
