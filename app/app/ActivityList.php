<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'date', 'show_on_attendance_form', 'show_on_dashboard', 'price'];

    protected $casts = ['date' => 'immutable_datetime:Y-m-d'];

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

    // Return this as a string for easier population in eg. spatie/laravel-html.
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }
}
