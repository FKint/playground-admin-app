<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Nicolaslopezj\Searchable\SearchableTrait;

class Family extends Model
{
    use SearchableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guardian_first_name', 'guardian_last_name', 'tariff_id', 'remarks', 'contact'];
    protected $appends = ['guardian_full_name'];
    protected $searchable = [
        'columns' => [
            'families.id' => 10,
            'families.guardian_first_name' => 10,
            'families.guardian_last_name' => 10,
            'children.first_name' => 5,
            'children.last_name' => 5
        ],
        'joins' => [
            'child_families' => ['families.id', 'child_families.family_id'],
            'children' => ['child_families.child_id', 'children.id']
        ]
    ];

    /**
     * Get the default tariff for this family.
     */
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function guardian_full_name()
    {
        return $this->guardian_first_name . " " . $this->guardian_last_name;
    }

    public function child_families()
    {
        return $this->hasMany(ChildFamily::class);
    }

    public function children()
    {
        return $this->belongsToMany(Child::class, 'child_families', 'family_id', 'child_id');
    }

    public function family_week_registrations()
    {
        return $this->hasMany(FamilyWeekRegistration::class);
    }


    public function getTotalCosts()
    {
        return $this->transactions()->sum('amount_expected');
    }

    public function getTotalPayments()
    {
        return $this->transactions()->sum('amount_paid');
    }

    public function getCurrentSaldo()
    {
        // The saldo of the organisation's relationship with the family.
        // Positive if the family has debts.
        return $this->getTotalCosts() - $this->getTotalPayments();
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getGuardianFullNameAttribute()
    {
        return $this->guardian_full_name();
    }
}
