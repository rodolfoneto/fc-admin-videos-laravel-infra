<?php

namespace Core\UseCase\Video\List\Dto;

class ListVideosInputDto
{
    public function __construct(
        public string $filter = '',
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15,
    ) {}
}
