<?php

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\CategoriesListInputDto;
use Core\UseCase\DTO\Category\CategoriesListOutputDto;
use PHPUnit\Framework\TestCase;

class ListCategoriesUseCaseUnitTest extends TestCase
{

    private $mockRepo;
    private $mockInputDto;

    public function testListCategoryEmpty()
    {
        $mockPagination = $this->mockPagination();

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('paginate')->andReturn($mockPagination);

        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $this->mockInputDto = Mockery::mock(CategoriesListInputDto::class, ['filter', 'order']);

        $responseUserCase = $useCase->execute($this->mockInputDto);
        $this->assertCount(0, $responseUserCase->items);
        $this->assertEquals(0, $responseUserCase->total);
        $this->assertInstanceOf(CategoriesListOutputDto::class, $responseUserCase);
    }

    public function testFunctionPaginateWasCalled()
    {
        $mockPagination = $this->mockPagination();

        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('paginate')->andReturn($mockPagination);

        $useCase = new ListCategoriesUseCase($spy);
        $this->mockInputDto = Mockery::mock(CategoriesListInputDto::class, ['filter', 'order']);

        $responseUserCase = $useCase->execute($this->mockInputDto);
        $spy->shouldHaveReceived('paginate');
        $this->assertInstanceOf(CategoriesListOutputDto::class, $responseUserCase);
    }

    public function testListCategories()
    {
        $catFake = new stdClass();
        $catFake->id = 'asd';
        $catFake->name = 'asd';
        $catFake->description = 'asd';
        $catFake->isActive = true;
        $catFake->createdAt = 'asd';
        $catFake->updatedAt = 'asd';
        $catFake->deletedAt = 'asd';
        $mockPagination = $this->mockPagination([$catFake]);

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('paginate')->andReturn($mockPagination);

        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $this->mockInputDto = Mockery::mock(CategoriesListInputDto::class, ['filter', 'order']);

        $responseUserCase = $useCase->execute($this->mockInputDto);
        $this->assertCount(1, $responseUserCase->items);
        $this->assertInstanceOf(stdClass::class, $responseUserCase->items[0]);
        $this->assertInstanceOf(CategoriesListOutputDto::class, $responseUserCase);
    }

    protected function mockPagination(array $items = [])
    {
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive('items')->andReturn($items);
        $mockPagination->shouldReceive('total')->andReturn(count($items));
        $mockPagination->shouldReceive('lastPage')->andReturn(0);
        $mockPagination->shouldReceive('firstPage')->andReturn(0);
        $mockPagination->shouldReceive('currentPage')->andReturn(0);
        $mockPagination->shouldReceive('perPage')->andReturn(15);
        $mockPagination->shouldReceive('to')->andReturn(0);
        $mockPagination->shouldReceive('from')->andReturn(0);
        return $mockPagination;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
