<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'client', 'creator', 'editor', 'support', 'admin', 'super_admin'
        'phone',
        'company',
        'address',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
