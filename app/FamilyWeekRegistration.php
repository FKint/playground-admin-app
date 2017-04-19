<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FamilyWeekRegistration extends Model
{

    protected $fillable = ['family_id', 'week_id', 'tariff_id'];

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function child_family_week_registrations()
    {
        return ChildFamilyWeekRegistration::where([
            ['family_id', '=', $this->family_id],
            ['week_id', '=', $this->week_id]
        ]);
    }
}
