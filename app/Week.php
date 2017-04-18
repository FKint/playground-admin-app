<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year', 'week_number'];

    /**
     * Get the corresponding year.
     */
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    /**
     * Get all days in this week.
     */
    public function playground_days()
    {
        return $this->hasMany(PlaygroundDay::class);
    }

    public function family_week_registrations(){
        return $this->hasMany(FamilyWeekRegistration::class);
    }
}
