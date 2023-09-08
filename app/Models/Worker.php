<?php

namespace App\Models;

use App\Services\WorkerService;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Worker extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'agency_id',
        'first_name',
        'last_name',
        'code',
    ];

    protected static function booted(): void
    {
        static::created(function (Model $model) {
            $model->code = WorkerService::generateCode();
            $model->save();
        });
    }

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

    public function scopeTenant($query)
    {
        return $query->where('agency_id', Filament::getTenant()->id);
    }

    public function document()
    {
        return $this->hasMany(WorkerDocuments::class);
    }
}
