<?php

namespace App\Models;

use App\Enums\ConcernStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concern extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => ConcernStatusEnum::class,
    ];

    public function concernReport(): HasMany
    {
        return $this->hasMany(ConcernReport::class);
    }

    public function agency(): BelongsTo
    {
        return $this->BelongsTo(Agency::class);
    }
}
