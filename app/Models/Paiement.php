<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paiement extends Model
{
    protected $table = 'paiement';

    protected $fillable = [
        'customer_id',
        'type_cotisation_id',
        'cotisation_id',
        'montant',
        'mode_paiement',
        'reference',
        'statut',
        'metadata',
        'date_paiement',
    ];

    protected $casts = [
        'montant'       => 'integer',
        'metadata'      => 'array',
        'date_paiement' => 'datetime',
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

    public function cotisation(): BelongsTo
    {
        return $this->belongsTo(Cotisation::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'source_id')
            ->where('source', 'paiement');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeSuccess($query)
    {
        return $query->where('statut', 'success');
    }

    public function scopeEchec($query)
    {
        return $query->where('statut', 'echec');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isSuccess(): bool
    {
        return $this->statut === 'success';
    }

    public function isEchec(): bool
    {
        return $this->statut === 'echec';
    }

    public function isMobileMoney(): bool
    {
        return $this->mode_paiement === 'mobile_money';
    }

    /**
     * Opérateur mobile money (Orange, MTN…) depuis les metadata
     */
    public function getOperateurAttribute(): ?string
    {
        return $this->metadata['operateur'] ?? null;
    }
}