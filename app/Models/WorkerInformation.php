<?php

namespace App\Models;

use App\Notifications\WorkerRegisteredNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class WorkerInformation extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'worker_id',
        'contact_number',
        'date_hired',
        'email',
        'address',
        'date_birth',
        'place_birth',
        'passport_number',
        'passport_place_issue',
        'passport_date_issue',
        'passport_date_expired',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'spouse_name',
        'spouse_occupation',
        'gender',
        'religion',
        'civil_status',
        'height',
        'weight',
        'cover_letter',
    ];

    protected static function booted(): void
    {
        static::created(function (Model $model) {
            if ($model->email) {
                $model->notify(new WorkerRegisteredNotification($model));
            }
        });
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }
}
