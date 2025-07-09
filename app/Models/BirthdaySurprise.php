<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Library untuk handle tanggal
use Illuminate\Support\Facades\Cache;

class BirthdaySurprise extends Model
{
    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
        'content_type',
        'content_payload',
        'reveal_at',
        'is_revealed',
        'content'
    ];

    // TAMBAH INI:
    protected $casts = [
        'photos' => 'array',
        'is_revealed' => 'boolean',
        'reveal_at' => 'datetime'
    ];
    
    // RELATIONSHIPS
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    // HELPER METHODS - Logika Bisnis

    // Method 1: Cek apakah surprise sudah boleh dibuka
    public function isRevealed()
    {
        return $this->is_revealed;
    }

    // Method 2: Cek apakah sudah waktunya reveal (untuk automation)
    public function shouldBeRevealed()
    {
        return Carbon::now()->greaterThanOrEqualTo($this->reveal_at) && !$this->is_revealed;
    }

    // Method 3: Hitung countdown sampai reveal time
    public function getCountdownAttribute()
    {
        // Cache countdown untuk 1 menit
        return Cache::remember("countdown_{$this->id}", 60, function() {
            if ($this->isRevealed()) {
                return 'Sudah terkirim!';
            }
            
            $now = Carbon::now();
            $revealTime = $this->reveal_at;
            
            if ($now->gte($revealTime)) {
                return 'Siap dibuka!';
            }
            
            $diff = $now->diff($revealTime);
            
            if ($diff->days > 0) {
                return $diff->days . ' hari lagi';
            } elseif ($diff->h > 0) {
                return $diff->h . ' jam lagi';
            } elseif ($diff->i > 0) {
                return $diff->i . ' menit lagi';
            } else {
                return 'Hampir terbuka!';
            }
        });
    }

    // Method 4: Get icon sesuai jenis konten
    public function getContentIcon()
    {
        return match($this->content_type) {
            'message' => '💌',
            'image' => '🖼️', 
            'video_link' => '🎥',
            default => '��'
        };
    }

    public function canBeRevealed()
    {
        return !$this->is_revealed && now()->gte($this->reveal_at);
    }
}