<?php

namespace Core\Domain\ValueObject;

use Core\Domain\Enum\MediaStatus;

class Media
{
    public function __construct(
        protected string $path,
        protected MediaStatus $mediaStatus,
        protected string $encodedPath = '',
    ) {}

    public function path(): string
    {
        return $this->path;
    }

    public function mediaStatus(): MediaStatus
    {
        return $this->mediaStatus;
    }

    public function encodedPath(): string
    {
        return $this->encodedPath;
    }

}
