<?php

namespace App\Models;

use App\Services\WorkerService;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Worker extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'first_name',
        'last_name',
        'middle_name',
        'position',
        'code',
    ];

    protected $with = ['workerInformation'];

    protected static function booted(): void
    {
        static::creating(function (Model $model) {
            if (! $model->code) {
                $model->code = WorkerService::generateCode();
            }
        });
    }

    public function fullname(): Attribute
    {
        return Attribute::make(get: fn ($v, $attr) => $attr['first_name'].' '.$attr['last_name']);
    }

    public function workerInformation(): HasOne
    {
        return $this->hasOne(WorkerInformation::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeTenant($query)
    {
        return $query->where('agency_id', Filament::getTenant()->id);
    }

    public function document(): HasMany
    {
        return $this->hasMany(WorkerDocuments::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function workHistory(): HasMany
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function monitorings(): HasMany
    {
        return $this->hasMany(Monitoring::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function hasActiveDeployment(): bool
    {
        return $this->deployments()
            ->where('agency_id', $this->agency_id)
            ->where('status', 'DEPLOYED')
            ->exists();
    }

    public function hasSubmittedMonitoring(): bool
    {
        return $this->monitorings()
            ->where('agency_id', $this->agency_id)
            ->exists();
    }

    public function needsMonitoringAlert(): bool
    {
        return $this->hasActiveDeployment() && ! $this->hasSubmittedMonitoring();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('passport')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }
}
