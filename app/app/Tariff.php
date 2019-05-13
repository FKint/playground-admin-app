<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'year_id', 'abbreviation', 'week_first_child', 'week_later_children', 'day_first_child', 'day_later_children'];

    public function full_abbreviation_and_name()
    {
        return $this->abbreviation.' - '.$this->name;
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function make_copy(Year $year)
    {
        $new_tariff = $this->replicate();
        $new_tariff->year()->associate($year);

        return $new_tariff;
    }
}
