<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'dial_code',
        'phone',
        'adresse',
        'montant_engagement',
        'date_adhesion',
        'status',
    ];

    protected $casts = [
        'date_adhesion'     => 'date',
        'montant_engagement' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function cotisations(): HasMany
    {
        return $this->hasMany(Cotisation::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentCustomer::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('status', 'actif');
    }

    public function scopeAvecEngagement($query)
    {
        return $query->whereNotNull('montant_engagement');
    }

    public function scopeSansEngagement($query)
    {
        return $query->whereNull('montant_engagement');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function hasMensuel(): bool
    {
        return $this->montant_engagement !== null;
    }

    public function isActif(): bool
    {
        return $this->status === 'actif';
    }

    /**
     * Dernière cotisation mensuelle du fidèle
     */
    public function derniereCotisationMensuelle(): ?Cotisation
    {
        return $this->cotisations()
            ->whereHas('typeCotisation', fn($q) => $q->where('type', 'mensuel'))
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->first();
    }

    /**
     * Statut global du fidèle pour le dashboard
     * Retourne : a_jour | partiel | en_retard | sans_engagement
     */
    public function statutGlobal(): string
    {
        if (! $this->hasMensuel()) {
            return 'sans_engagement';
        }

        $derniere = $this->derniereCotisationMensuelle();

        return $derniere?->statut ?? 'en_retard';
    }
}
