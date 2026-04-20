<?php

namespace App\Models;

use App\Models\MessageGroupe;
use App\Models\Reclammation;
use App\Models\TypeCotisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends  Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'dial_code',
        'phone',
        'adresse',
        'montant_engagement',
        'date_adhesion',
        'status',

        'type_cotisation_mensuel_id',
        'matricule',
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

    public function reclammation(): HasMany
    {
        return $this->hasMany(Reclammation::class);
    }

     public function messageGroupes(): BelongsToMany
    {
        return $this->belongsToMany(MessageGroupe::class, 'message_groupe_customer')
                ->withPivot(['statut', 'envoye_le', 'erreur'])
            ->withTimestamps();
    }

    public function typeCotisationMensuel()
    {
        return $this->belongsTo(TypeCotisation::class, 'type_cotisation_mensuel_id');
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

    //le matricule se genere a la creation d'un customer
    public function generateMatricule(): void
    {
        $this->matricule = 'F'.str_pad($this->id, 5, '0', STR_PAD_LEFT);
        $this->save();
    }

    static function boot()
    {
        parent::boot();
        static::created(function ($customer) {
            $customer->generateMatricule();
        });
    }


      /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
