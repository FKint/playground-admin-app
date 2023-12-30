<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'price'];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function make_copy(Year $year)
    {
        $new_supplement = $this->replicate();
        $new_supplement->year()->associate($year);

        return $new_supplement;
    }
}
