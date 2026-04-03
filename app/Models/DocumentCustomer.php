<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentCustomer extends Model
{
    protected $table = 'document_customer';

    protected $fillable = [
        'customer_id',
        'libelle',
        'type_document',
        'chemin_fichier',
        'mime',
        'size'
    ];

    // ─── Relations ───────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->chemin_fichier, PATHINFO_EXTENSION);
    }
}