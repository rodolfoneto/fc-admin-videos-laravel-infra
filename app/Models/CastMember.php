<?php

namespace App\Models;

use Core\Domain\Enum\CastMemberType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'name', 'type', 'created_at'];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'cast_member_video');
    }
}
