<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Family extends Model
{
    use SearchableTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['guardian_first_name', 'guardian_last_name', 'tariff_id', 'remarks', 'contact', 'social_contact', 'needs_invoice', 'email'];
    protected $appends = ['guardian_full_name', 'saldo', 'total_costs'];
    protected $searchable = [
        'columns' => [
            'families.id' => 10,
            'families.guardian_first_name' => 10,
            'families.guardian_last_name' => 10,
            'families.email' => 10,
            'children.first_name' => 5,
            'children.last_name' => 5,
        ],
        'joins' => [
            'child_families' => ['families.id', 'child_families.family_id'],
            'children' => ['child_families.child_id', 'children.id'],
        ],
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
        return $this->guardian_first_name.' '.$this->guardian_last_name;
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

    public function getTotalCosts($cached = false)
    {
        if ($cached) {
            return $this->transactions()->sum('amount_expected');
        }
        $total_week_registrations_cost = 0;
        foreach ($this->family_week_registrations as $week_registration) {
            $total_week_registrations_cost += $week_registration->getTotalWeekPrice();
        }
        foreach ($this->child_families as $child_family) {
            foreach ($child_family->activity_lists as $activity_list) {
                $total_week_registrations_cost += $activity_list->price;
            }
        }

        return $total_week_registrations_cost;
    }

    public function getTotalPayments()
    {
        return $this->transactions()->sum('amount_paid');
    }

    public function getCurrentSaldo($cached = false)
    {
        // The saldo of the organisation's relationship with the family.
        // Positive if the family has debts.
        return $this->getTotalCosts($cached) - $this->getTotalPayments();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getGuardianFullNameAttribute()
    {
        return $this->guardian_full_name();
    }

    public function getSaldoAttribute()
    {
        return $this->getCurrentSaldo(true);
    }

    public function getTotalCostsAttribute()
    {
        return $this->getTotalCosts(true);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
