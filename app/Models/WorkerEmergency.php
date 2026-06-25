<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerEmergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'worker_id',
        'passport_number',
        'secret_code',
        'latitude',
        'longitude',
        'notes',
        'resolved_at',
        'resolved_by',
        'resolution_reason',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'resolved_at' => 'datetime',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public function getGoogleMapsUrl(): ?string
    {
        if (! $this->hasLocation()) {
            return null;
        }

        return 'https://www.google.com/maps?q=' . urlencode((string) $this->latitude) . ',' . urlencode((string) $this->longitude);
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    public function markAsResolved(int $userId, string $reason): void
    {
        $this->update([
            'resolved_at' => now(),
            'resolved_by' => $userId,
            'resolution_reason' => $reason,
        ]);
    }

    public function scopeTenant($query)
    {
        return $query->where('agency_id', Filament::getTenant()->id);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Check if worker has an unresolved emergency
     */
    public static function hasUnresolvedEmergency(int $workerId): bool
    {
        return self::where('worker_id', $workerId)
            ->unresolved()
            ->exists();
    }
}
