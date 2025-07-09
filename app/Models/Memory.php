<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Memory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'memory_date', 
        'description',
        'image_path',
        'spotify_track_id'
    ];

    protected $casts = [
        'memory_date' => 'date'
    ];

    // Relationship: Memory belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}