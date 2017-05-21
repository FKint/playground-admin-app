<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year'];

    /**
     * Get all weeks in this year.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }

    public function count_registrations_for_age_group($age_group)
    {
        return $this->weeks()
            ->join('child_family_day_registrations', 'weeks.id', '=', 'child_family_day_registrations.week_id')
            ->where('child_family_day_registrations.age_group_id', $age_group->id)
            ->count();
    }

    public function count_registrations()
    {
        return $this->weeks()
            ->join('child_family_day_registrations', 'weeks.id', '=', 'child_family_day_registrations.week_id')
            ->count();
    }

    public function playground_days()
    {
        return $this->hasManyThrough(PlaygroundDay::class, Week::class, 'year_id', 'week_id', 'id');
    }
}
