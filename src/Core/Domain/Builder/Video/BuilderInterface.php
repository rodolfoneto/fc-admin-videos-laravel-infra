<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\MediaStatus;

interface BuilderInterface
{
    public function createEntity(object $input): BuilderInterface;
    public function addMediaVideo(string $path, MediaStatus $mediaStatus, string $encodedPath = ''): BuilderInterface;
    public function addTrailer(string $path, MediaStatus $mediaStatus, string $encodedPath = ''): BuilderInterface;
    public function addThumb(string $path): BuilderInterface;
    public function addThumbHalf(string $path): BuilderInterface;
    public function addBanner(string $path): BuilderInterface;
    public function getEntity(): Video;
}
