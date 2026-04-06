<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;

    protected $table = 'transaction';

    //status transaction : en_attente, success, failed, cancelled, expired

    protected $fillable = [
        'type',
        'source',
        'source_id',
        'status',
        'montant',
        'libelle',
        'date_transaction',
    ];

    protected $casts = [
        'montant'          => 'integer',
        'date_transaction' => 'datetime',
        'created_at'       => 'datetime',
    ];

    // ─── Relations dynamiques selon la source ─────────────────

    public function sourceable()
    {
        return match ($this->source) {
            'paiement'   => $this->belongsTo(Paiement::class, 'source_id'),
            'depense'    => $this->belongsTo(Depense::class, 'source_id'),
            default      => null,
        };
    }


    // ─── Scopes ──────────────────────────────────────────────

    public function scopeEntrees($query)
    {
        return $query->where('type', 'entree');
    }

    public function scopeSorties($query)
    {
        return $query->where('type', 'sortie');
    }

    public function scopePeriode($query, $debut, $fin)
    {
        return $query->whereBetween('date_transaction', [$debut, $fin]);
    }

    public function scopeParSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isEntree(): bool
    {
        return $this->type === 'entree';
    }

    public function isSortie(): bool
    {
        return $this->type === 'sortie';
    }

    /**
     * Factory : crée une transaction entrée depuis un paiement
     */
    public static function creerDepuisPaiement(Paiement $paiement, string $libelle): self
    {
        return self::create([
            'type'             => 'entree',
            'source'           => 'paiement',
            'source_id'        => $paiement->id,
            'montant'          => $paiement->montant,
            'libelle'          => $libelle,
            'date_transaction' => $paiement->date_paiement,
        ]);
    }

    /**
     * Factory : crée une transaction sortie depuis une dépense
     */
    public static function creerDepuisDepense(Depense $depense): self
    {
        return self::create([
            'type'             => 'sortie',
            'source'           => 'depense',
            'source_id'        => $depense->id,
            'montant'          => $depense->montant,
            'libelle'          => $depense->libelle,
            'date_transaction' => $depense->date_depense,
        ]);
    }
}
