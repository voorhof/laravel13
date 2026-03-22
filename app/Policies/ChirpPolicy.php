<?php /** @noinspection PhpUnusedParameterInspection */

namespace App\Policies;

use App\Models\Chirp;
use App\Models\User;

class ChirpPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Allow all users to view chirps, also guests through using ?User
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Chirp $chirp): bool
    {
        // Allow all users to view a chirp, also guests through using ?User
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow authenticated users to create chirps
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Chirp $chirp): bool
    {
        // Allow the user to update their own chirps
        return $chirp->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Chirp $chirp): bool
    {
        // Allow the user to delete their own chirps
        return $chirp->user()->is($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Chirp $chirp): bool
    {
        // Not used in this demo, so return false
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Chirp $chirp): bool
    {
        // Not used in this demo, so return false
        return false;
    }
}
