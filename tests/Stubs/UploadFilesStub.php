<?php

namespace Tests\Stubs;

use Core\UseCase\Interface\FileStorageInterface;

class UploadFilesStub implements FileStorageInterface
{
    public function store(string $path, array $file): string
    {
        return $path . '/test.mp4';
    }

    public function delete(string $path): void
    {
        //
    }
}