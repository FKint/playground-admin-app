<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['full_name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function years()
    {
        return $this->hasMany(Year::class);
    }
}
