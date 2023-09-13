<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Concerns\HasUuid;

class JobPost extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'posted_by',
        'title',
        'uuid',
        'description',
        'country',
        'is_published',
    ];

    protected static function booted(): void
    {
        static::created(function (JobPost $model) {
            $voucher = JobPost::find($model->id);
            $voucher->posted_by = auth()->user()->name ?? 'admin';
            $voucher->save();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function application(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function agency(): BelongsTo
    {
        return $this->BelongsTo(Agency::class);
    }

    protected function accessLink(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return route('application.store.job', ['agency' => Filament::getTenant(), 'jobPost' => $attributes['uuid']]);
            }
        );
    }
}
