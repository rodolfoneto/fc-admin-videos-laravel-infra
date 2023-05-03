<?php

namespace Core\UseCase\Video\Delete\Dto;

class DeleteVideoInputDto
{
    public function __construct(
        public string $id,
    ) {}
}
