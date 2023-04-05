<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'is_active',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
