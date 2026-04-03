<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueCotisation extends Model
{
    public $timestamps = false;

    protected $table = 'historique_cotisation';

    protected $fillable = [
        'cotisation_id',
        'type_operation',
        'montant',
        'note',
        'snapshot_cotisation',
    ];

    protected $casts = [
        'snapshot_cotisation' => 'array',
        'montant'             => 'integer',
        'created_at'          => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function cotisation(): BelongsTo
    {
        return $this->belongsTo(Cotisation::class);
    }

    // ─── Factory helper ──────────────────────────────────────

    /**
     * Crée un log d'historique à partir d'une cotisation
     */
    public static function log(
        Cotisation $cotisation,
        string $typeOperation,
        int $montant,
        ?string $note = null
    ): self {
        return self::create([
            'cotisation_id'       => $cotisation->id,
            'type_operation'      => $typeOperation,
            'montant'             => $montant,
            'note'                => $note,
            'snapshot_cotisation' => $cotisation->toSnapshot(),
        ]);
    }
}