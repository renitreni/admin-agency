<?php

namespace App\Services;

use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerBlocklist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkerBlocklistService
{
    /**
     * Create a blocklist entry for a worker.
     */
    public function blocklistWorker(array $data, User $blockedBy, ?Worker $worker = null): WorkerBlocklist
    {
        $agencyId = $blockedBy->agency->first()?->id;

        if (! $agencyId) {
            throw new \InvalidArgumentException('User must belong to an agency to create blocklist entries.');
        }

        $workerData = $this->extractWorkerData($data, $worker);

        return DB::transaction(function () use ($workerData, $blockedBy, $agencyId, $worker) {
            $blocklistEntry = WorkerBlocklist::create([
                'agency_id' => $agencyId,
                'blocked_by' => $blockedBy->id,
                'worker_id' => $worker?->id,
                'first_name' => $workerData['first_name'],
                'last_name' => $workerData['last_name'],
                'middle_name' => $workerData['middle_name'],
                'passport_number' => $workerData['passport_number'],
                'email' => $workerData['email'],
                'contact_number' => $workerData['contact_number'],
                'date_birth' => $workerData['date_birth'],
                'place_birth' => $workerData['place_birth'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'match_confidence' => $data['match_confidence'] ?? 'medium',
                'is_active' => true,
                'blocked_at' => now(),
            ]);

            return $blocklistEntry;
        });
    }

    /**
     * Check if worker data matches any active blocklist entries from other agencies.
     * Returns array with is_blocklisted boolean and matches array.
     */
    public function isBlocklisted(array $workerData, ?int $excludingAgencyId = null): array
    {
        $matches = $this->findPotentialMatches($workerData, $excludingAgencyId);

        $highConfidenceMatches = $matches->where('match_confidence', 'high');
        $isBlocklisted = $highConfidenceMatches->isNotEmpty();

        return [
            'is_blocklisted' => $isBlocklisted,
            'matches' => $matches->map(function ($match) use ($workerData) {
                return [
                    'entry' => $match,
                    'score' => WorkerBlocklist::calculateMatchScore($match->toArray(), $workerData),
                    'confidence' => $match->match_confidence,
                ];
            })->toArray(),
        ];
    }

    /**
     * Find potential blocklist matches from other agencies, ranked by score.
     */
    public function findPotentialMatches(array $workerData, ?int $excludingAgencyId = null): Collection
    {
        $query = WorkerBlocklist::query()
            ->active()
            ->with(['agency', 'blockedBy']);

        if ($excludingAgencyId) {
            $query->fromOtherAgencies($excludingAgencyId);
        }

        $entries = $query->get();

        // Score each entry and filter by threshold
        $scored = $entries->map(function ($entry) use ($workerData) {
            $score = WorkerBlocklist::calculateMatchScore($entry->toArray(), $workerData);
            $entry->match_confidence = WorkerBlocklist::getConfidenceFromScore($score);
            $entry->match_score = $score;

            return $entry;
        })->filter(fn ($entry) => $entry->match_score >= 40)
            ->sortByDesc('match_score')
            ->values();

        return $scored;
    }

    /**
     * Deactivate a blocklist entry (soft delete).
     * Only the owning agency can deactivate.
     */
    public function unblock(WorkerBlocklist $entry, User $user): void
    {
        $userAgencyId = $user->agency->first()?->id;

        if (! $userAgencyId || $userAgencyId !== $entry->agency_id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only the owning agency can deactivate this blocklist entry.');
        }

        $entry->update([
            'is_active' => false,
        ]);

        $entry->delete(); // Soft delete
    }

    /**
     * Update a blocklist entry.
     * Only the owning agency can update.
     */
    public function updateEntry(WorkerBlocklist $entry, array $data, User $user): WorkerBlocklist
    {
        $userAgencyId = $user->agency->first()?->id;

        if (! $userAgencyId || $userAgencyId !== $entry->agency_id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Only the owning agency can update this blocklist entry.');
        }

        $entry->update($data);

        return $entry;
    }

    /**
     * Extract worker data from request or worker model.
     */
    private function extractWorkerData(array $data, ?Worker $worker): array
    {
        $result = [
            'first_name' => $data['first_name'] ?? ($worker?->first_name ?? null),
            'last_name' => $data['last_name'] ?? ($worker?->last_name ?? null),
            'middle_name' => $data['middle_name'] ?? ($worker?->middle_name ?? null),
            'passport_number' => $data['passport_number'] ?? ($worker?->workerInformation?->passport_number ?? null),
            'email' => $data['email'] ?? ($worker?->workerInformation?->email ?? null),
            'contact_number' => $data['contact_number'] ?? ($worker?->workerInformation?->contact_number ?? null),
            'date_birth' => $data['date_birth'] ?? ($worker?->workerInformation?->date_birth ?? null),
            'place_birth' => $data['place_birth'] ?? ($worker?->workerInformation?->place_birth ?? null),
        ];

        return $result;
    }

    /**
     * Get blocklist entries for a specific agency (for display).
     */
    public function getEntriesForAgency(int $agencyId): Collection
    {
        return WorkerBlocklist::query()
            ->ownedByAgency($agencyId)
            ->with(['worker', 'blockedBy'])
            ->latest('blocked_at')
            ->get();
    }

    /**
     * Get blocklist entries from other agencies (for cross-agency checking).
     */
    public function getEntriesFromOtherAgencies(int $excludeAgencyId): Collection
    {
        return WorkerBlocklist::query()
            ->active()
            ->fromOtherAgencies($excludeAgencyId)
            ->with(['agency', 'blockedBy'])
            ->latest('blocked_at')
            ->get();
    }
}
