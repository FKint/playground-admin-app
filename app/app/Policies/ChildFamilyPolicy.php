<?php

namespace App\Policies;

use App\Models\ChildFamily;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChildFamilyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the childFamily.
     *
     * @return mixed
     */
    public function view(User $user, ChildFamily $childFamily)
    {
        return $user->can('view', $childFamily->year);
    }

    /**
     * Determine whether the user can update the childFamily.
     *
     * @return mixed
     */
    public function update(User $user, ChildFamily $childFamily)
    {
        return $user->can('update', $childFamily->year);
    }

    /**
     * Determine whether the user can delete the childFamily.
     *
     * @return mixed
     */
    public function delete(User $user, ChildFamily $childFamily)
    {
        return $user->can('update', $childFamily->year);
    }
}
