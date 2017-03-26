<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guardian_first_name', 'guardian_last_name', 'tariff', 'remarks', 'contacts'];

    /**
     * Get the default tariff for this family.
     */
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
}
