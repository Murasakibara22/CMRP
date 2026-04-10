<?php

namespace App\Models;

use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\HistoriqueReclammation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Reclammation extends Model
{
    protected $fillable = [
        'customer_id',
        'sujet',
        'description',
        'status',
        'user_charged_id',
        'cotisation_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class, 'cotisation_id');
    }

    public function userCharged()
    {
        return $this->belongsTo(User::class, 'user_charged_id');
    }

    public function historiqueReclammation(){
        return $this->hasMany(HistoriqueReclammation::class, 'reclammation_id');
    }
}
