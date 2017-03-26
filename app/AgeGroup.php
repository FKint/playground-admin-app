<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
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
}
