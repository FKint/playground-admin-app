<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Child extends Model
{
    use SearchableTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'birth_year', 'age_group_id', 'remarks', 'year_id'];
    protected $appends = ['full_name'];

    protected $searchable = [
        'columns' => [
            'children.id' => 10,
            'children.first_name' => 10,
            'children.last_name' => 10,
            'families.guardian_first_name' => 5,
            'families.guardian_last_name' => 5,
        ],
        'joins' => [
            'child_families' => ['children.id', 'child_families.child_id'],
            'families' => ['child_families.family_id', 'families.id'],
        ],
    ];

    /**
     * Get the age group this child belongs to.
     */
    public function age_group()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function ageGroup()
    {
        return $this->age_group();
    }

    /**
     * Get the child families this child belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child_families()
    {
        return $this->hasMany(ChildFamily::class, $foreignKey = 'child_id');
    }

    public function families()
    {
        return $this->belongsToMany(Family::class, 'child_families', 'child_id', 'family_id');
    }

    public function full_name()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getFullNameAttribute()
    {
        return $this->full_name();
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
