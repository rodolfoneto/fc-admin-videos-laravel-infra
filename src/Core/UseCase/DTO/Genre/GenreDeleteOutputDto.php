<?php

namespace Core\UseCase\DTO\Genre;

class GenreDeleteOutputDto
{
    public function __construct(
        public bool $success,
    ) {

    }
}
