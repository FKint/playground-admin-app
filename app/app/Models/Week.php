<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year_id', 'week_number', 'first_day_of_week'];

    protected $casts = ['first_day_of_week' => 'date'];

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

    public function first_day()
    {
        $playground_day = $this->playground_days()
            ->join('week_days', 'playground_days.week_day_id', '=', 'week_days.id')
            ->orderBy('week_days.days_offset', 'asc')
            ->first();

        return PlaygroundDay::query()
            ->where([
                ['week_day_id', $playground_day->week_day_id],
                ['week_id', $playground_day->week_id],
            ])->firstOrFail();
    }

    public function last_day()
    {
        $playground_day = $this->playground_days()
            ->join('week_days', 'playground_days.week_day_id', '=', 'week_days.id')
            ->orderBy('week_days.days_offset', 'desc')->first();

        return PlaygroundDay::query()
            ->where([
                ['week_day_id', $playground_day->week_day_id],
                ['week_id', $playground_day->week_id],
            ])->firstOrFail();
    }

    public function family_week_registrations()
    {
        return $this->hasMany(FamilyWeekRegistration::class);
    }
}
