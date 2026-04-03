<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeDepense extends Model
{
    protected $table = 'type_depense';

    protected $fillable = ['libelle', 'description', 'status'];

    // ─── Relations ───────────────────────────────────────────

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('status', 'actif');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getTotalDepensesAttribute(): int
    {
        return $this->depenses()->sum('montant');
    }
}