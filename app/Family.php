<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Family extends Model
{
    use SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guardian_first_name', 'guardian_last_name', 'tariff_id', 'remarks', 'contact'];

    protected $searchable = [
        'columns' => [
            'families.id' => 10,
            'families.guardian_first_name' => 8,
            'families.guardian_last_name' => 10,
            'children.first_name' => 5,
            'children.last_name' => 5
        ],
        'joins' => [
            'child_families' => ['families.id', 'child_families.family_id'],
            'children' => ['child_families.child_id', 'children.id']
        ]
    ];

    /**
     * Get the default tariff for this family.
     */
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function guardian_full_name()
    {
        return $this->guardian_first_name . " " . $this->guardian_last_name;
    }

    public function child_families()
    {
        return $this->hasMany(ChildFamily::class);
    }

    public function children()
    {
        return $this->belongsToMany(Child::class, 'child_families', 'family_id', 'child_id');
    }
}
