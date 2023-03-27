<?php

namespace Core\UseCase\DTO\Category;

class CategoryDeleteInputDto
{
    public function __construct(
        public string $id,
    ) {

    }
}
