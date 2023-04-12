<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Video;

interface VideoRepositoryInterface extends EntityRepositoryInterface
{
    public function updateMedia(Video $entity): Video;
}
