<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Agency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'uuid'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function uniqueIds()
    {
        return ['uuid'];
    }
    
    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(
    //         fn ($model) => $model->uuid = Str::uuid()
    //     );
    // }

    public function worker(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
