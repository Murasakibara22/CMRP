<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'nom',
        'prenom',
        'email',
        'dial_code',
        'telephone',
        'password',
        'status',
        'otp',
        'otp_verified_at',
        'otp_expired_at',
        'photo_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'otp_verified_at' => 'datetime',
        'otp_expired_at'  => 'datetime',
    ];

    // ─── Relations ───────────────────────────────────────────

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withTimestamps();
    }

    public function cotisationsValidees(): HasMany
    {
        return $this->hasMany(Cotisation::class, 'validated_by');
    }

    public function depensesValidees(): HasMany
    {
        return $this->hasMany(Depense::class, 'validated_by');
    }

    // ─── Helpers permissions ──────────────────────────────────

    /**
     * Retourne toutes les permissions effectives :
     * celles du rôle + celles accordées directement à l'user
     */
    public function allPermissions(): \Illuminate\Support\Collection
    {
        $rolePermissions = $this->role?->permissions ?? collect();
        $userPermissions = $this->permissions;

        return $rolePermissions->merge($userPermissions)->unique('id');
    }

    public function hasPermission(string $code): bool
    {
        return $this->allPermissions()->contains('code', $code);
    }

    public function hasRole(string $code): bool
    {
        return $this->role?->code === $code;
    }

    // ─── Helpers OTP ─────────────────────────────────────────

    public function isOtpValid(): bool
    {
        return $this->otp !== null
            && $this->otp_expired_at !== null
            && $this->otp_expired_at->isFuture()
            && $this->otp_verified_at === null;
    }

    public function clearOtp(): void
    {
        $this->update([
            'otp'             => null,
            'otp_verified_at' => null,
            'otp_expired_at'  => null,
        ]);
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function isActif(): bool
    {
        return $this->status === 'actif';
    }
}
