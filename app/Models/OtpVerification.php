<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'verified_at',
        'attempts',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    // Constants
    const OTP_LENGTH = 6;
    const OTP_EXPIRY_MINUTES = 10;
    const MAX_ATTEMPTS = 5;

    /**
     * Generate a random OTP code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for a phone number
     */
    public static function createForPhone(string $phone, ?string $ipAddress = null): self
    {
        // Invalider les anciens OTP non vérifiés
        self::where('phone', $phone)
            ->whereNull('verified_at')
            ->delete();

        return self::create([
            'phone' => $phone,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verify(string $phone, string $code): bool
    {
        $otp = self::where('phone', $phone)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            // Incrémenter les tentatives
            self::where('phone', $phone)
                ->whereNull('verified_at')
                ->increment('attempts');

            return false;
        }

        // Vérifier le nombre de tentatives
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        // Marquer comme vérifié
        $otp->update([
            'verified_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Check if OTP is valid
     */
    public function isValid(): bool
    {
        return is_null($this->verified_at)
            && $this->expires_at->isFuture()
            && $this->attempts < self::MAX_ATTEMPTS;
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is verified
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Scope for valid OTPs
     */
    public function scopeValid($query)
    {
        return $query->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->where('attempts', '<', self::MAX_ATTEMPTS);
    }

    /**
     * Scope for expired OTPs
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }

    /**
     * Clean up expired and old verified OTPs
     */
    public static function cleanup(): int
    {
        return self::where(function ($query) {
            $query->where('expires_at', '<', Carbon::now()->subDay())
                ->orWhere(function ($q) {
                    $q->whereNotNull('verified_at')
                        ->where('verified_at', '<', Carbon::now()->subDays(7));
                });
        })->delete();
    }
}
