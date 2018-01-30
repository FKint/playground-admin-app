<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'date', 'show_on_attendance_form', 'show_on_dashboard', 'price'];

    /**
     * Get all child families on this list.
     */
    public function child_families()
    {
        return $this->belongsToMany(ChildFamily::class, 'activity_list_child_families');
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
