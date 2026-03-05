<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Season extends Model
{
    protected $table = 'seasons';

    protected $fillable = [
        'series_id',
        'season_number',
        'is_seen',
        'seen_at'
    ];

    // Relación: Una temporada pertenece a una serie
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class, 'series_id');
    }
}