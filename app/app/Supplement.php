<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'price'];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function make_copy(Year $year)
    {
        $new_supplement = $this->replicate();
        $year->supplements()->attach($new_supplement);
        return $new_supplement;
    }
}
