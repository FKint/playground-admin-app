<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'abbreviation', 'start_date', 'end_date'];

    /**
     * Get all children in this age group.
     */
    public function children()
    {
        return $this->hasMany(Child::class);
    }


    public static function getAllAgeGroupsById()
    {
        $all_age_groups = [];
        foreach (AgeGroup::all() as $age_group) {
            $all_age_groups[$age_group->id] = $age_group->abbreviation . " - " . $age_group->name;
        }
        return $all_age_groups;
    }
}
