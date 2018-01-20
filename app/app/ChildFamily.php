<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class ChildFamily extends Model
{
    use SearchableTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['family_id', 'child_name_id'];
    protected $appends = ['nb_registrations'];

    protected $searchable = [
        'columns' => [
            'families.id' => 5,
            'families.guardian_first_name' => 5,
            'families.guardian_last_name' => 5,
            'children.first_name' => 10,
            'children.last_name' => 10
        ],
        'joins' => [
            'families' => ['child_families.family_id', 'families.id'],
            'children' => ['child_families.child_id', 'children.id']
        ]
    ];

    /**
     * Get corresponding child.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the corresponding family.
     */
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get all activity lists.
     */
    public function activity_lists()
    {
        return $this->belongsToMany(
            ActivityList::class,
            'activity_list_child_families',
            'child_family_id',
            'activity_list_id'
        );
    }

    public function child_family_day_registrations()
    {
        return ChildFamilyDayRegistration::query()
            ->where([['child_id', '=', $this->child_id], ['family_id', '=', $this->family_id]]);
    }

    public function getNbRegistrationsAttribute(){
        return $this->child_family_day_registrations()->count();
    }
}
