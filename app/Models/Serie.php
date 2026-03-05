<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Serie extends Model
{
    // 1. Le decimos el nombre exacto de la tabla en Supabase
    protected $table = 'series';

    // 2. Definimos qué campos se pueden rellenar (Mass Assignment)
    protected $fillable = [
        'user_id',
        'tvmaze_id',
        'title',
        'poster_url',
        'is_completed',
        'premiered'
    ];

    // 3. Relación: Una serie tiene muchas temporadas
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class, 'series_id');
    }
}