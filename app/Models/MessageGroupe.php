<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageGroupe extends Model
{
    protected $table = 'message_groupes';

    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'canal',
        'tous_les_customers',
        'envoyer_le',
        'statut',
        'nb_destinataires',
        'nb_envoyes',
        'nb_echecs',
    ];

    protected $casts = [
        'tous_les_customers' => 'boolean',
        'envoyer_le'         => 'datetime',
        'nb_destinataires'   => 'integer',
        'nb_envoyes'         => 'integer',
        'nb_echecs'          => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'message_groupe_customer')
            ->withPivot(['statut', 'envoye_le', 'erreur'])
            ->withTimestamps();
    }

    public function destinataires(): HasMany
    {
        return $this->hasMany(MessageGroupeCustomer::class, 'message_groupe_id');
    }

    

    /* Scopes */
    public function scopePlanifie($query)  { return $query->where('statut', 'planifie'); }
    public function scopeEnvoye($query)    { return $query->where('statut', 'envoye'); }
    public function scopeEnCours($query)   { return $query->where('statut', 'en_cours'); }
}
