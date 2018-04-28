<?php

namespace App\Policies;

use App\User;
use App\Child;
use App\Year;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChildPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the child.
     *
     * @param  \App\User $user
     * @param  \App\Child $child
     * @return mixed
     */
    public function view(User $user, Child $child)
    {
        return $user->can('view', $child->year);
    }

    /**
     * Determine whether the user can update the child.
     *
     * @param  \App\User $user
     * @param  \App\Child $child
     * @return mixed
     */
    public function update(User $user, Child $child)
    {
        return $user->can('update', $child->year);
    }

    /**
     * Determine whether the user can delete the child.
     *
     * @param  \App\User $user
     * @param  \App\Child $child
     * @return mixed
     */
    public function delete(User $user, Child $child)
    {
        return $user->can('update', $child->year);
    }
}