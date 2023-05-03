<?php

namespace Tests\Unit\UseCase;

use Core\Domain\Repository\PaginationInterface;
use Mockery;

trait UseCaseTrait
{
    protected function mockPagination(array $items = [])
    {
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive('items')->andReturn($items);
        $mockPagination->shouldReceive('total')->andReturn(count($items));
        $mockPagination->shouldReceive('lastPage')->andReturn(1);
        $mockPagination->shouldReceive('firstPage')->andReturn(1);
        $mockPagination->shouldReceive('currentPage')->andReturn(1);
        $mockPagination->shouldReceive('perPage')->andReturn(15);
        $mockPagination->shouldReceive('to')->andReturn(1);
        $mockPagination->shouldReceive('from')->andReturn(1);
        return $mockPagination;
    }
}
