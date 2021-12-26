<?php

namespace App;

use Carbon\Carbon;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityList extends Model {
    use FormAccessible;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'date', 'show_on_attendance_form', 'show_on_dashboard', 'price'];

    protected $dates = ['date'];

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

    public function formDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
