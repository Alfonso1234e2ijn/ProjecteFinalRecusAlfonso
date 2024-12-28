<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Urating extends Model
{
    protected $table = 'uratings';

    protected $fillable = [
        'user_id',
        'rater_id',
        'rating',
    ];

    /**
     * Relación con el usuario que es calificado.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el usuario que da la valoración.
     *
     * @return BelongsTo
     */
    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
}
