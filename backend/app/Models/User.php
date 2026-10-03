<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'auth_provider',
        'auth_provider_id',
        'avatar_url',
        'role', // 'client', 'creator', 'editor', 'support', 'admin', 'super_admin'
        'phone',
        'company',
        'address',
        'avatar',
        'affiliate_code',
        'affiliate_balance',
        'otp_code',
        'otp_expires_at',
    ];

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'affiliate_id');
    }

    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function generateOtp(): void
    {
        $this->otp_code = (string) random_int(100000, 999999);
        $this->otp_expires_at = now()->addMinutes(15);
        $this->save();
    }

    public function sendOtpNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailOtpNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        // On remplace le lien de base par l'OTP
        $this->generateOtp();
        $this->sendOtpNotification();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isCreator(): bool
    {
        return in_array($this->role, ['creator', 'admin', 'super_admin']);
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['editor', 'admin', 'super_admin']);
    }

    public function isSupport(): bool
    {
        return in_array($this->role, ['support', 'admin', 'super_admin']);
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
