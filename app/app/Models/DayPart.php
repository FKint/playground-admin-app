<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayPart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'order', 'default', 'year_id'];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function make_copy($year)
    {
        $new_day_part = $this->replicate();
        $new_day_part->year()->associate($year);

        return $new_day_part;
    }
}
