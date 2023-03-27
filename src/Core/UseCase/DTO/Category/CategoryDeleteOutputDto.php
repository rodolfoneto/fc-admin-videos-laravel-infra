<?php

namespace Core\UseCase\DTO\Category;

class CategoryDeleteOutputDto
{
    public function __construct(
        public bool $success,
    ) {

    }
}
