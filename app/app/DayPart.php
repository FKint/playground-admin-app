<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DayPart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'order'];

    public static function getDefaultDayPart()
    {
        return DayPart::where('default', '=', true)->firstOrFail();
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
