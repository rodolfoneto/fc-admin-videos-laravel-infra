<?php
namespace Core\UseCase\Interface;


interface FileStorageInterface
{
    /**
     * @param string $path
     * @param array $_FILE[file]
     * @return string
     */
    public function store(string $path, array $file): string;

    /**
     * @param string $path
     * @return void
     */
    public function delete(string $path): void;
}
