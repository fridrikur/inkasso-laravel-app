<?php

namespace App\Policies;

use App\Models\User;
use App\Models\konsulent_notifikationer;
use Illuminate\Auth\Access\Response;

class KonsulentNotifikationerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, konsulent_notifikationer $konsulentNotifikationer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, konsulent_notifikationer $konsulentNotifikationer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, konsulent_notifikationer $konsulentNotifikationer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, konsulent_notifikationer $konsulentNotifikationer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, konsulent_notifikationer $konsulentNotifikationer): bool
    {
        return false;
    }
}
