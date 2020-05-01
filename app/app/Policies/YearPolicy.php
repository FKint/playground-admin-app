<?php

namespace App\Policies;

use App\User;
use App\Year;
use Illuminate\Auth\Access\HandlesAuthorization;

class YearPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the year.
     *
     * @return mixed
     */
    public function view(User $user, Year $year)
    {
        return $user->organization->id === $year->organization->id;
    }

    /**
     * Determine whether the user can create years.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        dd('year create policy!');

        return false;
    }

    /**
     * Determine whether the user can update the year.
     *
     * @return mixed
     */
    public function update(User $user, Year $year)
    {
        return $user->organization->id === $year->organization->id;
    }

    /**
     * Determine whether the user can delete the year.
     *
     * @return mixed
     */
    public function delete(User $user, Year $year)
    {
        dd('year delete policy!');

        return false;
    }

    public function create_child(User $user, Year $year)
    {
        return $this->update($user, $year);
    }

    public function create_child_family(User $user, Year $year)
    {
        return $this->update($user, $year);
    }

    public function create_family(User $user, Year $year)
    {
        return $this->update($user, $year);
    }
}
