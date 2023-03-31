<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use Tests\TestCase;

abstract class BaseGenreTestUnit extends TestCase
{
    public GenreRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
