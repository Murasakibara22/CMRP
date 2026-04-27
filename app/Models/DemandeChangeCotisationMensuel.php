<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeChangeCotisationMensuel extends Model
{
    protected $table = 'demande_change_cotisations';

    protected $fillable = [
        'customer_id',
        'created_by',
        'type_demande',                  // changement | arret
        'ancien_type_cotisation_id',
        'ancien_montant_engagement',
        'nouveau_type_cotisation_id',    // null si arret
        'nouveau_montant_engagement',    // null si arret
        'supprimer_cotisations_retard',
        'motif',
        'statut',                        // en_attente | validee | rejetee
        'validated_by',
        'validated_at',
        'motif_rejet',
    ];

    protected $casts = [
        'ancien_montant_engagement'      => 'integer',
        'nouveau_montant_engagement'     => 'integer',
        'supprimer_cotisations_retard'   => 'boolean',
        'validated_at'                   => 'datetime',
    ];

    /* ── Relations ── */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ancienType(): BelongsTo
    {
        return $this->belongsTo(TypeCotisation::class, 'ancien_type_cotisation_id');
    }

    public function nouveauType(): BelongsTo
    {
        return $this->belongsTo(TypeCotisation::class, 'nouveau_type_cotisation_id');
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
    public function isChangement(): bool { return $this->type_demande === 'changement'; }
    public function isArret(): bool      { return $this->type_demande === 'arret'; }

    public function getLabelTypeDemandeAttribute(): string
    {
        return $this->type_demande === 'changement'
            ? 'Changement de type'
            : 'Arrêt de cotisation mensuelle';
    }
}