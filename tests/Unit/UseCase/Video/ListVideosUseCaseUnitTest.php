<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\Dto\{ListVideosInputDto, ListVideosOutputDto};
use PHPUnit\Framework\TestCase;
use Core\UseCase\Video\List\ListVideosUseCase;
use Mockery;
use Tests\Unit\UseCase\UseCaseTrait;

class ListVideosUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;
    public function test_list_paginate()
    {
        $useCase = new ListVideosUseCase(repository: $this->createMockRepository());
        $output = $useCase->execute(input: $this->createMockInput());
        $this->assertInstanceOf(ListVideosOutputDto::class, $output);
        $this->assertCount(0, $output->items);
        $this->assertEquals(0, $output->total);
        $this->assertEquals(1, $output->last_page);
        $this->assertEquals(1, $output->first_page);
        $this->assertEquals(1, $output->current_page);
        $this->assertEquals(15, $output->per_page);
        $this->assertEquals(1, $output->to);
        $this->assertEquals(1, $output->from);
    }

    protected function createMockRepository()
    {
        $repository = Mockery::mock(VideoRepositoryInterface::class);
        $repository->shouldReceive('paginate')
            ->once()
            ->andReturn($this->mockPagination([]));
        return $repository;
    }

    protected function createMockInput()
    {
        return Mockery::mock(ListVideosInputDto::class, ['', 'DESC', 1, 15]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
