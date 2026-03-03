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
        'google_id',
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
   public function sendPasswordResetNotification($token)
{
    $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
    $url = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($this->email);

    $this->notify(new \App\Notifications\ResetPasswordNotification($url));
}
}
