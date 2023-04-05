<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\{
    GenresListOutputDto,
    GenresListInputDto
};
use Mockery;
use Core\UseCase\Genre\ListGenresUseCase;
use stdClass;

class ListGenresUseCaseUnitTest extends BaseGenreTestUnit
{
    public function test_paginate_genre_with_empty_items()
    {
        $pagination = $this->mockPagination();
        $this->repository->shouldReceive('paginate')->times(1)->andReturn($pagination);
        $inputDto = Mockery::mock(GenresListInputDto::class, ['', 'DESC', 1, 15]);
        $useCase = new ListGenresUseCase($this->repository);
        $output = $useCase->execute($inputDto);
        $this->assertInstanceOf(GenresListOutputDto::class, $output);
        $this->assertCount(0, $output->items);
        $this->assertEquals(15, $output->per_page);
        $this->assertEquals(0, $output->total);
    }

    public function test_paginate_genre_with_3_entity()
    {
        $items = array(
            Mockery::mock(Genre::class, ["New Name 01", Uuid::random()]),
            Mockery::mock(Genre::class, ["New Name 02", Uuid::random()]),
            Mockery::mock(Genre::class, ["New Name 03", Uuid::random()]),
        );

        $pagination = $this->mockPagination($items);
        $this->repository->shouldReceive('paginate')->times(1)->andReturn($pagination);
        $inputDto = Mockery::mock(GenresListInputDto::class, ['', 'DESC', 1, 15]);
        $useCase = new ListGenresUseCase($this->repository);
        $output = $useCase->execute($inputDto);
        $this->assertInstanceOf(GenresListOutputDto::class, $output);
        $this->assertCount(3, $output->items);
        $this->assertEquals(15, $output->per_page);
        $this->assertEquals(3, $output->total);
        $this->assertEquals(1, $output->current_page);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive('paginate')->once()->withArgs(['', 'DESC', 1, 15])->andReturn($pagination);
        $useCaseSpy = new ListGenresUseCase($spy);
        $useCaseSpy->execute($inputDto);
    }

    private function mockPagination(array $items = [])
    {
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive('items')->andReturn($items);
        $mockPagination->shouldReceive('total')->andReturn(count($items));
        $mockPagination->shouldReceive('lastPage')->andReturn(0);
        $mockPagination->shouldReceive('firstPage')->andReturn(0);
        $mockPagination->shouldReceive('currentPage')->andReturn(1);
        $mockPagination->shouldReceive('perPage')->andReturn(15);
        $mockPagination->shouldReceive('to')->andReturn(0);
        $mockPagination->shouldReceive('from')->andReturn(0);
        return $mockPagination;
    }
}
