<?php

namespace Core\UseCase\Video\Get\Dto;

class GetVideoInputDto
{
    public function __construct(
        public string $id,
    ) {}
}
