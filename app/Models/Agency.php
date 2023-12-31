<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agency extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $fillable = ['name', 'email', 'uuid'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }

    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function worker(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }

    public function foreignAgencies()
    {
        return $this->hasMany(ForeignAgency::class);
    }

    public function voucherTypes()
    {
        return $this->hasMany(VoucherTypes::class);
    }

    public function handlers()
    {
        return $this->hasMany(Handler::class);
    }
}
