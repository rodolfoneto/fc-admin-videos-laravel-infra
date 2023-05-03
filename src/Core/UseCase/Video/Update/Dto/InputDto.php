<?php

namespace Core\UseCase\Video\Update\Dto;

use Core\Domain\Enum\Rating;

class InputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
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
