<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'country_name',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }
}
