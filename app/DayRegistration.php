<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DayRegistration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['day_id', 'week_registration_id', 'day_part_id', 'checked_in'];

    /**
     * Get the corresponding day.
     */
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Get the corresponding week registration.
     */
    public function week_registration()
    {
        return $this->belongsTo(WeekRegistration::class);
    }

    /**
     * Get the corresponding day part.
     */
    public function day_part()
    {
        return $this->belongsTo(DayPart::class);
    }

    /**
     * Get all supplements.
     */
    public function supplements()
    {
        return $this->hasMany(Supplement::class);
    }
}
