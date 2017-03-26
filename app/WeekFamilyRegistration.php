<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WeekFamilyRegistration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['family_id', 'week_id', 'tariff_id'];

    /**
     * Get the corresponding family.
     */
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get the corresponding week.
     */
    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    /**
     * Get the corresponding tariff.
     */
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    /**
     * Get corresponding week registrations.
     */
    public function week_registrations()
    {
        return $this->hasMany(WeekRegistration::class);
    }
}
