<?php

namespace App;

use DateInterval;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['description'];

    /**
     * Get all weeks in this year.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }

    public function getActiveAdminSession()
    {
        if ($this->admin_sessions()->count() == 0) {
            $admin_session = new AdminSession();
            $admin_session->year()->associate($this);
            $admin_session->save();
        }
        return $this->admin_sessions()->whereNull('session_end')->firstOrFail();
    }

    public function admin_sessions()
    {
        return $this->hasMany(AdminSession::class);
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

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function getAllAgeGroupsById()
    {
        return $this->age_groups->mapWithKeys(function ($group) {
            return [$group->id => $group->full_abbreviation_and_name()];
        })->toArray();
    }

    public function getAllTariffsById()
    {
        return $this->tariffs->mapWithKeys(function ($tariff) {
            return [$tariff->id => $tariff->full_abbreviation_and_name()];
        });
    }

    public function day_parts()
    {
        return $this->hasMany(DayPart::class);
    }

    public function age_groups()
    {
        return $this->hasMany(AgeGroup::class);
    }

    public function tariffs()
    {
        return $this->hasMany(Tariff::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }

    public function activity_lists()
    {
        return $this->hasMany(ActivityList::class);
    }

    public function getDashboardLists()
    {
        return $this->activity_lists()->where('show_on_dashboard', '=', true)->get();
    }

    public function child_families()
    {
        return $this->hasMany(ChildFamily::class);
    }

    public function week_days()
    {
        return $this->hasMany(WeekDay::class);
    }

    public function getPlaygroundDayForDate(DateTimeImmutable $date)
    {
        $week = $this->weeks()
            ->whereDate('first_day_of_week', '<=', $date->format('Y-m-d'))
            ->orderByDesc('first_day_of_week')
            ->first();
        if (!$week) {
            return null;
        }

        $interval = $date->diff($week->first_day_of_week);
        $week_day = $this->week_days()
            ->where('days_offset', '=', $interval->days)
            ->first();
        if (!$week_day) {
            return null;
        }

        return $week->playground_days()->where('week_day_id', '=', $week_day->id)->first();
    }

    public function supplements()
    {
        return $this->hasMany(Supplement::class);
    }

    public function child_family_day_registrations()
    {
        return $this->hasMany(ChildFamilyDayRegistration::class);
    }

    public function getDefaultDayPart()
    {
        return $this->day_parts()->where('default', true)->firstOrFail();
    }

    /**
     * Makes a clone of the current year.
     * Makes a copy of the settings for age groups, day parts, week days, supplements and tariffs.
     * Generates dates between $first_day and $last_day on the week days (and generates weeks accordingly) except for
     * dates in $exception_days.
     * @param string $description
     * @param DateTimeImmutable $first_day
     * @param DateTimeImmutable $last_day
     * @param array $exception_days
     * @return Model
     */
    public function make_copy(string $description, DateTimeImmutable $first_day, DateTimeImmutable $last_day, array $exception_days)
    {
        function getStartOfWeekDate(DateTimeImmutable $date)
        {
            // https://gist.github.com/stecman/0203410aa4da0ef01ea9
            $date = $date->setTime(0, 0, 0);

            if ($date->format('N') == 1) {
                // If the date is already a Monday, return it as-is
                return $date;
            } else {
                // Otherwise, return the date of the nearest Monday in the past
                // This includes Sunday in the previous week instead of it being the start of a new week
                return $date->modify('last monday');
            }
        }

        $new_year = $this->replicate();
        $new_year->description = $description;
        $new_year->save();
        foreach ($this->week_days as $week_day) {
            $week_day->make_copy($new_year)->save();
        }
        $current_day = $first_day;
        $day_interval = DateInterval::createFromDateString('1 day');
        while ($current_day <= $last_day) {
            $monday_of_week = getStartOfWeekDate($current_day);
            $day_number = $current_day->format('N') - 1;
            $week_day = $new_year->week_days()->where('days_offset', '=', $day_number)->first();
            if (!in_array($current_day, $exception_days) && $week_day) {
                $week = $new_year->weeks()->where('first_day_of_week', $monday_of_week)->first();
                if (!$week) {
                    $week = new Week(['week_number' => $new_year->weeks()->count(), 'first_day_of_week' => $monday_of_week]);
                    $week->year()->associate($new_year);
                    $week->save();
                }
                $playground_day = new PlaygroundDay();
                $playground_day->week_day()->associate($week_day);
                $playground_day->week()->associate($week);
                $playground_day->year()->associate($new_year);
                $playground_day->save();
            }
            $current_day = $current_day->add($day_interval);
        }

        foreach ($this->day_parts as $day_part) {
            $day_part->make_copy($new_year)->save();
        }
        foreach ($this->age_groups as $age_group) {
            $age_group->make_copy($new_year)->save();
        }
        foreach ($this->tariffs as $tariff) {
            $tariff->make_copy($new_year)->save();
        }
        foreach ($this->supplements as $supplement) {
            $supplement->make_copy($new_year)->save();
        }
        $admin_session = new AdminSession();
        $admin_session->year()->associate($new_year);
        $admin_session->save();
        return $new_year;
    }
}
