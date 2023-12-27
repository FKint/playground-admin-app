<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaygroundDay extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['week_day_id', 'year_id', 'week_id'];
    protected $appends = ['date'];

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function week_day()
    {
        return $this->belongsTo(WeekDay::class);
    }

    public function weekDay()
    {
        return $this->week_day();
    }

    public function date()
    {
        return $this->week->first_day_of_week->addDays($this->week_day->days_offset);
    }

    public function getDateAttribute()
    {
        return $this->date()->toImmutable();
    }

    public function child_family_day_registrations()
    {
        return ChildFamilyDayRegistration::query()
            ->where([['week_id', $this->week_id], ['week_day_id', $this->week_day_id]]);
    }

    public function count_supplements_for_age_group($supplement, $age_group)
    {
        return $this->child_family_day_registrations()
            ->where('age_group_id', $age_group->id)
            ->join('child_family_day_registration_supplements', 'child_family_day_registrations.id', '=', 'child_family_day_registration_supplements.child_family_day_registration_id')
            ->where('child_family_day_registration_supplements.supplement_id', $supplement->id)
            ->count();
    }

    public function count_supplements($supplement)
    {
        return $this->child_family_day_registrations()
            ->join('child_family_day_registration_supplements', 'child_family_day_registrations.id', '=', 'child_family_day_registration_supplements.child_family_day_registration_id')
            ->where('child_family_day_registration_supplements.supplement_id', $supplement->id)
            ->count();
    }

    public function count_registrations_for_age_group($age_group)
    {
        return $this->child_family_day_registrations()
            ->where('age_group_id', $age_group->id)
            ->count();
    }

    public function count_registrations()
    {
        return $this->child_family_day_registrations()->count();
    }
}
