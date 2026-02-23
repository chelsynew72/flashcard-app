<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'is_public'];

    protected $casts = ['is_public' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function studySessions()
    {
        return $this->hasMany(StudySession::class);
    }
}
