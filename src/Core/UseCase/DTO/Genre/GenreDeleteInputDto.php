<?php

namespace Core\UseCase\DTO\Genre;

class GenreDeleteInputDto
{
    public function __construct(
        public string $id,
    ) {

    }
}
