<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['description', 'title', 'invoice_header_text', 'invoice_header_image', 'invoice_bank_account'];
    protected $hidden = ['invoice_header_image'];

    /**
     * Get all weeks in this year.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }

    public function getActiveAdminSession()
    {
        if (0 == $this->admin_sessions()->count()) {
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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

    public function getPlaygroundDayForDate(\DateTimeImmutable $date)
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
     * Assumes that the week starts on a Monday.
     *
     * @param CarbonImmutable[] $exception_days
     *
     * @return Model
     */
    public function make_copy(int $organization_id, string $title, string $description, CarbonImmutable $first_day, CarbonImmutable $last_day, array $exception_days)
    {
        function getStartOfWeekDate(CarbonImmutable $date)
        {
            if (Carbon::MONDAY == $date->dayOfWeek) {
                return $date;
            }

            return $date->previous(Carbon::MONDAY);
        }

        $isExceptionDay = function (CarbonImmutable $date) use ($exception_days) {
            return count(array_filter($exception_days, fn ($d): bool => $d->isSameDay($date))) > 0;
        };

        $new_year = $this->replicate();
        $new_year->organization()->associate(Organization::find($organization_id));
        $new_year->title = $title;
        $new_year->description = $description;
        $new_year->save();
        foreach ($this->week_days as $week_day) {
            $week_day->make_copy($new_year)->save();
        }
        $current_day = $first_day;
        $day_interval = CarbonInterval::day();
        while ($current_day <= $last_day) {
            $monday_of_week = getStartOfWeekDate($current_day);
            $day_number = $current_day->format('N') - 1;
            $week_day = $new_year->week_days()->where('days_offset', '=', $day_number)->first();
            if (!$isExceptionDay($current_day) && $week_day) {
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
