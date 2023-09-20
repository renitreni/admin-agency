<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'position',
        'country',
        'date_deployed',
        'status',
        'agency_id',
        'worker_id',
        'foreign_agency_id',
        'handler_id',
    ];

    protected $casts = [
        'date_deployed' => 'date',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function foreignAgency(): BelongsTo
    {
        return $this->belongsTo(ForeignAgency::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(Handler::class);
    }

    public function scopeTenant($query)
    {
        return $query->where('agency_id', Filament::getTenant()->id);
    }
}
