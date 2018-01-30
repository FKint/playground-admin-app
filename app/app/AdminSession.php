<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSession extends Model
{
    protected $fillable = ['session_end', 'responsible_name', 'counted_cash', 'remarks'];
    protected $appends = ['nb_transactions', 'session_start', 'expected_cash', 'error', 'finished'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFinishedAttribute()
    {
        return $this->session_end !== null;
    }

    public function getNbTransactionsAttribute()
    {
        return $this->transactions()->count();
    }

    public function getSessionStartAttribute()
    {
        $earliest_transaction_datetime = $this->transactions()->min('created_at');
        if (!$earliest_transaction_datetime) {
            return $this->session_end;
        }
        return $earliest_transaction_datetime;
    }

    public function getExpectedCashAttribute()
    {
        return $this->transactions()->sum('amount_paid');
    }

    public function getErrorAttribute()
    {
        return $this->counted_cash - $this->getExpectedCashAttribute();
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
