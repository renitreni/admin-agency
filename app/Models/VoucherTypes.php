<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'agency_id',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeTenant($query)
    {
        return $query->where('agency_id', Filament::getTenant()->id);
    }
}
