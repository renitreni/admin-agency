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
        'address',
        'country',
        'date_deployed',
        'end_of_contract_date',
        'has_left_country',
        'flight_number',
        'flight_date',
        'airline',
        'status',
        'agency_id',
        'worker_id',
        'foreign_agency_id',
        'handler_id',
        'identification_no',
    ];

    protected $casts = [
        'date_deployed' => 'date',
        'end_of_contract_date' => 'date',
        'flight_date' => 'date',
        'has_left_country' => 'boolean',
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
