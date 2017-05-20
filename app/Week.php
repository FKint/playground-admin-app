<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function last_day()
    {
        $playground_day = $this->playground_days()
            ->join('week_days', 'playground_days.week_day_id', '=', 'week_days.id')
            ->orderBy('week_days.days_offset', -1)->first();
        $playground_day = PlaygroundDay::query()
            ->where([
                ['week_day_id', $playground_day->week_day_id],
                ['week_id', $playground_day->week_id]
            ])->firstOrFail();
        return $playground_day;
    }

    public function family_week_registrations()
    {
        return $this->hasMany(FamilyWeekRegistration::class);
    }
}
