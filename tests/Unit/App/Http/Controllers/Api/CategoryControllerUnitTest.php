<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

use App\Http\Controllers\Api\CategoryController;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\CategoriesListOutputDto;
use Mockery;
use stdClass;

class CategoryControllerUnitTest extends TestCase
{
    public function test_index()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('get')->andReturn('');

        $outputDtoMock = Mockery::mock(CategoriesListOutputDto::class, [[], 1, 1, 1, 15, 1, 1]);

        $useCaseMock = Mockery::mock(ListCategoriesUseCase::class);
        $useCaseMock->shouldReceive('execute')->andReturn($outputDtoMock);

        $controller = $this->createController();
        $response = $controller->index($requestMock, $useCaseMock);

        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        /**
         * Spies
         */
        $useCaseSpy = Mockery::spy(ListCategoriesUseCase::class);
        $useCaseSpy->shouldReceive('execute')->andReturn($outputDtoMock);
        $response = $controller->index($requestMock, $useCaseSpy);
        $useCaseSpy->shouldHaveReceived('execute');
    }

    private function createController(): CategoryController
    {
        return new CategoryController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
