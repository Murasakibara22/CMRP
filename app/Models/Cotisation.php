<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotisation extends Model
{
    protected $table = 'cotisation';

    protected $fillable = [
        'customer_id',
        'type_cotisation_id',
        'mois',
        'annee',
        'montant_du',
        'montant_paye',
        'montant_restant',
        'statut',
        'mode_paiement',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'mois'          => 'integer',
        'annee'         => 'integer',
        'montant_du'    => 'integer',
        'montant_paye'  => 'integer',
        'montant_restant' => 'integer',
        'validated_at'  => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function typeCotisation(): BelongsTo
    {
        return $this->belongsTo(TypeCotisation::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueCotisation::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeAJour($query)
    {
        return $query->where('statut', 'a_jour');
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', 'en_retard');
    }

    public function scopePartiel($query)
    {
        return $query->where('statut', 'partiel');
    }

    public function scopeForMois($query, int $mois, int $annee)
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isAJour(): bool
    {
        return $this->statut === 'a_jour';
    }

    public function isEnRetard(): bool
    {
        return $this->statut === 'en_retard';
    }

    public function isPartiel(): bool
    {
        return $this->statut === 'partiel';
    }

    /**
     * Applique un paiement partiel ou total sur cette cotisation.
     * Retourne le montant restant après application (surplus).
     */
    public function appliquerPaiement(int $montant): int
    {
        $applicable = min($montant, $this->montant_restant);
        $surplus    = $montant - $applicable;

        $this->montant_paye    += $applicable;
        $this->montant_restant -= $applicable;
        $this->statut = $this->montant_restant === 0 ? 'a_jour' : 'partiel';

        $this->save();

        return $surplus;
    }

    /**
     * Crée le snapshot JSON pour l'historique
     */
    public function toSnapshot(): array
    {
        return $this->only([
            'id', 'customer_id', 'type_cotisation_id',
            'mois', 'annee',
            'montant_du', 'montant_paye', 'montant_restant',
            'statut', 'mode_paiement',
        ]);
    }

    /**
     * Libellé lisible : "Janvier 2025" ou null si pas mensuel
     */
    public function getPeriodeAttribute(): ?string
    {
        if (! $this->mois || ! $this->annee) {
            return null;
        }

        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return ($mois[$this->mois] ?? '') . ' ' . $this->annee;
    }
}