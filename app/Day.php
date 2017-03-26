<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['week_id', 'date'];

    /**
     * Get the corresponding week.
     */
    public function week()
    {
        return $this->belongsTo(Week::class);
    }
}
