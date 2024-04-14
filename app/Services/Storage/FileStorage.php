<?php

namespace App\Services\Storage;

use Core\UseCase\Interface\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorage implements FileStorageInterface
{

    /**
     * @param string path
     * @param array file[name, type, tmp_name, error, size]
     */
    public function store(string $path, array $file): string
    {
        $contents = $this->convertFileToLaravelFile($file);
        return Storage::put($path, $contents);
    }

    public function delete(string $path): void
    {
        Storage::delete($path);
    }

    protected function convertFileToLaravelFile(array $file, bool $test = false): UploadedFile
    {
        return new UploadedFile(
            path: $file['tmp_name'],
            originalName: $file['name'],
            mimeType: $file['type'],
            error: $file['error'],
            test: $test,
        );
    }
}
