<?php

namespace Core\UseCase\DTO\Genre;

class GenreUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public array $categoriesId = [],
        public string $created_at = '',
        public bool $is_active = true,
    ) {

    }
}
