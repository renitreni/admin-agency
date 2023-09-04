<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'first_name',
        'last_name',
    ];

    public function fullname(): Attribute
    {
        return Attribute::make(get: fn ($v, $attr) => $attr['first_name'].' '.$attr['last_name']);
    }

    public function agency(): BelongsTo
    {
        return $this->BelongsTo(Agency::class);
    }

    public function workerInformation(): HasOne
    {
        return $this->hasOne(WorkerInformation::class);
    }
}
