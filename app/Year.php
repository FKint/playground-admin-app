<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['year'];

    /**
     * Get all weeks in this year.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }
}
