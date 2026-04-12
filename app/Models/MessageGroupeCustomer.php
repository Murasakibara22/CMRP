<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageGroupeCustomer extends Model
{
    protected $table = 'message_groupe_customer';

    protected $fillable = [
        'message_groupe_id',
        'customer_id',
        'statut',
        'envoye_le',
        'erreur',
    ];

    protected $casts = [
        'envoye_le' => 'datetime',
    ];

    public function messageGroupe(): BelongsTo
    {
        return $this->belongsTo(MessageGroupe::class, 'message_groupe_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}