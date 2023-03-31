<?php

namespace Core\UseCase\DTO\Genre;

class GenresListInputDto
{
    public function __construct(
        public string $filter = '',
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15,
    ) {

    }
}
