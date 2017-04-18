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
    protected $fillable = ['name', 'abbreviation', 'week_first_child', 'week_later_children', 'day_first_child', 'day_later_children'];

    public static function getAllTariffsById()
    {
        $all_tariffs = [];
        foreach (Tariff::all() as $tariff) {
            $all_tariffs[$tariff->id] = $tariff->abbreviation . " - " . $tariff->name;
        }
        return $all_tariffs;
    }
}
