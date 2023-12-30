<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildFamilyDayRegistration extends Model
{
    protected $fillable = ['child_id', 'family_id', 'week_id', 'week_day_id', 'day_part_id', 'attended', 'age_group_id', 'year_id'];

    public function supplements()
    {
        return $this->belongsToMany(
            Supplement::class,
            'child_family_day_registration_supplements',
            'child_family_day_registration_id',
            'supplement_id'
        );
    }

    public function playground_day()
    {
        return PlaygroundDay::query()->where([['week_id', 'week_id'], ['week_day_id', 'week_day_id']])->first();
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function day_part()
    {
        return $this->belongsTo(DayPart::class);
    }

    public function age_group()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function child_family_day_registration_supplements()
    {
        return $this->hasMany(ChildFamilyDayRegistrationSupplement::class);
    }
}
