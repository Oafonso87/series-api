<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. IMPORTANTE: Añadir esta línea para la API
use Illuminate\Database\Eloquent\Relations\HasMany; // Para la relación con series

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // 2. Añadimos HasApiTokens aquí dentro para que el usuario pueda generar "llaves" de acceso
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // Como tu tabla no tiene email_verified_at, Laravel simplemente ignorará esto
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 3. AÑADIMOS LA RELACIÓN: Un usuario tiene muchas series
    public function series(): HasMany
    {
        return $this->hasMany(Serie::class);
    }
}