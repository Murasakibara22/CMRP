<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeRemboursement extends Model
{
    protected $table = 'demande_remboursements';

    protected $fillable = [
        'paiement_id',
        'customer_id',
        'montant',
        'motif',
        'statut',        // en_attente | validee | rejetee
        'created_by',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'montant'      => 'integer',
        'validated_at' => 'datetime',
    ];

    /* ── Relations ── */
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /* ── Scopes ── */
    public function scopeEnAttente($query)  { return $query->where('statut', 'en_attente'); }
    public function scopeValidee($query)    { return $query->where('statut', 'validee'); }
    public function scopeRejetee($query)    { return $query->where('statut', 'rejetee'); }

    /* ── Helpers ── */
    public function isEnAttente(): bool { return $this->statut === 'en_attente'; }
    public function isValidee(): bool   { return $this->statut === 'validee'; }
}