<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'worker_id',
        'foreign_agency_id',
        'source',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::created(function (Voucher $model) {
            $voucher = Voucher::find($model->id);
            $voucher->created_by = auth()->user()->name ?? 'admin';
            $voucher->save();
        });

        static::updated(function (Voucher $model) {
            $voucher = Voucher::find($model->id);
            $voucher->updated_by = auth()->user()->name ?? 'admin';
            $voucher->save();
        });
    }

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

    public function voucherType(): HasMany
    {
        return $this->hasMany(VoucherTypes::class);
    }
}
