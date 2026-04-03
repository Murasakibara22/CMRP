<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Depense extends Model
{
    protected $table = 'depense';

    protected $fillable = [
        'type_depense_id',
        'libelle',
        'montant',
        'date_depense',
        'note',
        'justificatif',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'montant'      => 'integer',
        'date_depense' => 'date',
        'validated_at' => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function typeDepense(): BelongsTo
    {
        return $this->belongsTo(TypeDepense::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'source_id')
            ->where('source', 'depense');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isValide(): bool
    {
        return $this->validated_by !== null;
    }

    public function getJustificatifUrlAttribute(): ?string
    {
        return $this->justificatif ? Storage::url($this->justificatif) : null;
    }
}