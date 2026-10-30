<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkerBlocklist;

class WorkerBlocklistPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view blocklist entries for checking purposes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * All authenticated users can view any blocklist entry for checking.
     */
    public function view(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can create blocklist entries for their own agency.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only the owning agency can update their blocklist entries.
     */
    public function update(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        return $this->isOwner($user, $workerBlocklist);
    }

    /**
     * Determine whether the user can delete the model.
     * Only the owning agency can deactivate (soft delete) their blocklist entries.
     */
    public function delete(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        return $this->isOwner($user, $workerBlocklist);
    }

    /**
     * Determine whether the user can restore the model.
     * Not allowed - blocklist entries cannot be restored once deactivated.
     */
    public function restore(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Not allowed - no hard deletes for audit trail.
     */
    public function forceDelete(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        return false;
    }

    /**
     * Check if the user's agency owns the blocklist entry.
     */
    private function isOwner(User $user, WorkerBlocklist $workerBlocklist): bool
    {
        $userAgencyId = $user->agency->first()?->id;

        return $userAgencyId && $userAgencyId === $workerBlocklist->agency_id;
    }
}
