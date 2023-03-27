<?php

namespace Core\UseCase\DTO\Category;

class CategoriesListInputDto
{
    public function __construct(
        public string $filter = '',
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15
    ) {
        
    }
}
