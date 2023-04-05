<?php

namespace Tests\Unit\App\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenreUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new Genre();
    }

    protected function traits(): array
    {
        return [
            HasFactory::class,
            SoftDeletes::class,
        ];
    }

    protected function fillable(): array
    {
        return [
            'id',
            'name',
            'is_active',
        ];
    }

    protected function incrementing(): bool
    {
        return false;
    }

    protected function casting(): array
    {
        return [
            'id' => 'string',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
