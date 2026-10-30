<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerBlocklist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agency_id',
        'blocked_by',
        'worker_id',
        'first_name',
        'last_name',
        'middle_name',
        'passport_number',
        'email',
        'contact_number',
        'date_birth',
        'place_birth',
        'reason',
        'notes',
        'match_confidence',
        'is_active',
        'blocked_at',
    ];

    protected $casts = [
        'date_birth' => 'date',
        'blocked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'match_confidence' => 'medium',
        'is_active' => true,
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Scope to only active (not soft-deleted, is_active = true) entries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to entries owned by a specific agency.
     */
    public function scopeOwnedByAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope to entries from other agencies (for cross-agency checks).
     */
    public function scopeFromOtherAgencies(Builder $query, int $excludeAgencyId): Builder
    {
        return $query->where('agency_id', '!=', $excludeAgencyId);
    }

    /**
     * Normalize a name for matching: lowercase, trim, collapse whitespace, remove punctuation.
     */
    public static function normalizeName(?string $name): ?string
    {
        if (! $name) {
            return null;
        }

        return strtolower(preg_replace('/\s+/', ' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', trim($name))));
    }

    /**
     * Normalize a phone number: digits only, strip country code +63, leading 0.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        // Strip Philippines country code +63 or leading 0
        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Calculate match confidence score between blocklist entry and worker data.
     * Returns score 0-100.
     */
    public static function calculateMatchScore(array $blocklistEntry, array $workerData): int
    {
        $score = 0;

        // Passport number exact match (strongest identifier for Philippines) - 40 points
        if ($blocklistEntry['passport_number'] && $workerData['passport_number']) {
            if (strtolower(trim($blocklistEntry['passport_number'])) === strtolower(trim($workerData['passport_number']))) {
                $score += 40;
            }
        }

        // Email exact match - 30 points
        if ($blocklistEntry['email'] && $workerData['email']) {
            if (strtolower(trim($blocklistEntry['email'])) === strtolower(trim($workerData['email']))) {
                $score += 30;
            }
        }

        // Contact number exact match (normalized) - 15 points
        if ($blocklistEntry['contact_number'] && $workerData['contact_number']) {
            $normalizedBlocklistPhone = self::normalizePhone($blocklistEntry['contact_number']);
            $normalizedWorkerPhone = self::normalizePhone($workerData['contact_number']);
            if ($normalizedBlocklistPhone && $normalizedWorkerPhone && $normalizedBlocklistPhone === $normalizedWorkerPhone) {
                $score += 15;
            }
        }

        // First + last name normalized exact match - 20 points
        $blocklistFirstName = self::normalizeName($blocklistEntry['first_name'] ?? null);
        $blocklistLastName = self::normalizeName($blocklistEntry['last_name'] ?? null);
        $workerFirstName = self::normalizeName($workerData['first_name'] ?? null);
        $workerLastName = self::normalizeName($workerData['last_name'] ?? null);

        if ($blocklistFirstName && $blocklistLastName && $workerFirstName && $workerLastName) {
            if ($blocklistFirstName === $workerFirstName && $blocklistLastName === $workerLastName) {
                $score += 20;

                // Bonus: middle name match - 5 points
                $blocklistMiddle = self::normalizeName($blocklistEntry['middle_name'] ?? null);
                $workerMiddle = self::normalizeName($workerData['middle_name'] ?? null);
                if ($blocklistMiddle && $workerMiddle && $blocklistMiddle === $workerMiddle) {
                    $score += 5;
                }
            }
        }

        // Date of birth exact match - 10 points
        if ($blocklistEntry['date_birth'] && $workerData['date_birth']) {
            if ($blocklistEntry['date_birth'] === $workerData['date_birth']) {
                $score += 10;
            }
        }

        // Place of birth match - 5 points
        if ($blocklistEntry['place_birth'] && $workerData['place_birth']) {
            if (strtolower(trim($blocklistEntry['place_birth'])) === strtolower(trim($workerData['place_birth']))) {
                $score += 5;
            }
        }

        return min($score, 100);
    }

    /**
     * Get confidence label from score.
     */
    public static function getConfidenceFromScore(int $score): string
    {
        if ($score >= 70) {
            return 'high';
        }
        if ($score >= 40) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Check if worker data matches this blocklist entry with configurable threshold.
     */
    public function matchesWorker(array $workerData, int $threshold = 40): bool
    {
        $score = self::calculateMatchScore($this->toArray(), $workerData);

        return $score >= $threshold;
    }
}
