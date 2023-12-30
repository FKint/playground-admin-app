<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'abbreviation', 'start_date', 'end_date'];

    /**
     * Get all children in this age group.
     */
    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function full_abbreviation_and_name()
    {
        return $this->abbreviation.' - '.$this->name;
    }

    public function make_copy($year)
    {
        $new_age_group = $this->replicate();
        $new_age_group->year()->associate($year);

        return $new_age_group;
    }
}
