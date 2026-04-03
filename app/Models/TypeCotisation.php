<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TypeCotisation extends Model
{
    protected $table = 'type_cotisation';

    protected $fillable = [
        'libelle',
        'description',
        'is_required',
        'type',
        'jour_recurrence',
        'montant_objectif',
        'status',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_required'      => 'boolean',
        'montant_objectif' => 'integer',
        'start_at'         => 'date',
        'end_at'           => 'date',
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

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActif($query)
    {
        return $query->where('status', 'actif');
    }

    public function scopeMensuel($query)
    {
        return $query->where('type', 'mensuel');
    }

    public function scopeEnCours($query)
    {
        return $query->where('status', 'actif')
            ->where(function ($q) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', Carbon::today());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', Carbon::today());
            });
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isMensuel(): bool
    {
        return $this->type === 'mensuel';
    }

    public function isOrdinaire(): bool
    {
        return $this->type === 'ordinaire';
    }

    public function isJourPrecis(): bool
    {
        return $this->type === 'jour_precis';
    }

    public function isActif(): bool
    {
        return $this->status === 'actif';
    }

    /**
     * Vérifie si la période est actuellement active (pour Ramadan)
     */
    public function isEnCours(): bool
    {
        $today = Carbon::today();

        $apresDebut = is_null($this->start_at) || $this->start_at->lte($today);
        $avantFin   = is_null($this->end_at) || $this->end_at->gte($today);

        return $this->isActif() && $apresDebut && $avantFin;
    }

    /**
     * Progression pour le Ramadan (total collecté vs objectif)
     */
    public function progressionRamadan(): array
    {
        $totalCollecte = $this->cotisations()
            ->where('statut', 'a_jour')
            ->sum('montant_paye');

        $objectif = $this->montant_objectif ?? 0;

        return [
            'collecte'    => $totalCollecte,
            'objectif'    => $objectif,
            'pourcentage' => $objectif > 0 ? round(($totalCollecte / $objectif) * 100, 1) : 0,
        ];
    }
}
