<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['family_id', 'amount_paid', 'amount_expected', 'remarks', 'admin_session_id', 'year_id'];

    /**
     * Get the default tariff for this family.
     */
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function admin_session()
    {
        return $this->belongsTo(AdminSession::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
