<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcernReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'concern_id',
        'worker_id',
        'feedback',
        'status',
    ];

    public function concern(): BelongsTo
    {
        return $this->belongsTo(ConcernReport::class);
    }
}
