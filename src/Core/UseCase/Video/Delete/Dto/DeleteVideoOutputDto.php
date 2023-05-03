<?php

namespace Core\UseCase\Video\Delete\Dto;

class DeleteVideoOutputDto
{
    public function __construct(
        public bool $success,
    ) {}
}
