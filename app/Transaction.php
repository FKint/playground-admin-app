<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['child_family_id', 'amount_paid', 'amount_expected', 'remarks'];

    /**
     * Get the default tariff for this family.
     */
    public function child_family()
    {
        return $this->belongsTo(ChildFamily::class);
    }
}
