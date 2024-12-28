<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $table = 'votes';

    protected $fillable = [
        'type',
        'user_id',
        'response_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function response()
    {
        return $this->belongsTo(Response::class, 'response_id');
    }
}
