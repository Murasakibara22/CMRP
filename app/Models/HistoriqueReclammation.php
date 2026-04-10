<?php

namespace App\Models;

use App\Models\Reclammation;
use Illuminate\Database\Eloquent\Model;

class HistoriqueReclammation extends Model
{
    protected $fillable = [
        'reclammation_id',
        'description',
        'status',
        'snapshot_reclammation',
        'user_charged_id',
        'cotisation_id',
    ];

    public function reclammation()
    {
        return $this->belongsTo(Reclammation::class, 'reclammation_id');
    }
}
