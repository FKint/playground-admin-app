<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChildFamily extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['family_id', 'child_id'];

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
        return $this->hasMany(ActivityList::class);
    }
}
