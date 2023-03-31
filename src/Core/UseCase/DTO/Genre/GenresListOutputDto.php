<?php

namespace Core\UseCase\DTO\Genre;

class GenresListOutputDto
{
    public function __construct(
        public array $items = [],
        public int $total = 0,
        public int $last_page = 1,
        public int $first_page = 1,
        public int $current_page = 1,
        public int $per_page = 15,
        public int $to = 1,
        public int $from = 1,
    ) {

    }
}
