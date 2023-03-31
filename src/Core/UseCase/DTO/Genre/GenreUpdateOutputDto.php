<?php

namespace Core\UseCase\DTO\Genre;

class GenreUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public array $categoriesId = [],
        public bool $is_active = true,
        public string $created_at = '',
    ) {

    }
}
