<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildFamilyWeekRegistration extends Model
{
    protected $fillable = ['child_id', 'family_id', 'week_id', 'whole_week_price', 'year_id'];

    public function child_family_day_registrations()
    {
        return ChildFamilyDayRegistration::where([
            ['family_id', '=', $this->family_id],
            ['week_id', '=', $this->week_id],
            ['child_id', '=', $this->child_id],
        ]);
    }

    public function is_empty()
    {
        return !$this->whole_week_price && 0 == $this->child_family_day_registrations()->count();
    }
}
