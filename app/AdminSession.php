<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSession extends Model
{
    protected $fillable = ['session_end', 'responsible_name', 'counted_cash', 'remarks'];
    protected $appends = ['nb_transactions', 'session_start', 'expected_cash', 'error'];

    public static function getActiveAdminSession()
    {
        return AdminSession::query()->whereNull('session_end')->first();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getNbTransactionsAttribute()
    {
        return $this->transactions()->count();
    }

    public function getSessionStartAttribute()
    {
        return $this->transactions()->min('created_at');
    }

    public function getExpectedCashAttribute()
    {
        return $this->transactions()->sum('amount_paid');
    }

    public function getErrorAttribute()
    {
        return $this->getExpectedCashAttribute() - $this->counted_cash;
    }
}
