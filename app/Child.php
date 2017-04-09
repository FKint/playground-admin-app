<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'birth_year', 'age_group', 'remarks'];

    /**
     * Get the age group this child belongs to.
     */
    public function age_group()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    /**
     * Ge the child families this child belongs to.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child_families()
    {
        return $this->hasMany(ChildFamily::class);
    }
}
