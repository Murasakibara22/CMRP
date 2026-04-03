<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoutEngagement extends Model
{
    protected $table = 'cout_engagement';

    protected $fillable = ['montant', 'libelle', 'status'];

    // ─── Relations ───────────────────────────────────────────

    /**
     * Les fidèles ayant souscrit à ce palier
     * (lien indirect via montant_engagement sur Customer)
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'montant_engagement', 'montant');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('status', 'actif');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getMontantFormateAttribute(): string
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    public function isActif(): bool
    {
        return $this->status === 'actif';
    }
}