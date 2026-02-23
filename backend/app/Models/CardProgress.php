<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'easiness_factor',
        'repetitions',
        'interval',
        'next_review_at',
        'last_reviewed_at',
        'last_rating',
    ];

    protected $casts = [
        'next_review_at'    => 'datetime',
        'last_reviewed_at'  => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
