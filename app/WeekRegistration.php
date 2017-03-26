<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeekRegistration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['child_family_id', 'week_family_registration_id'];

    /**
     * Get the corresponding child family.
     */
    public function child_family()
    {
        return $this->belongsTo(ChildFamily::class);
    }

    /**
     * Get the corresponding family regisration.
     */
    public function week_family_registration()
    {
        return $this->belongsTo(WeekFamilyRegistration::class);
    }

    /**
     * Get all corresponding day registrations.
     */
    public function week_registrations(){
        return $this->hasMany(DayRegistration::class);
    }
}
