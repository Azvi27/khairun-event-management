<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',  // ← CHANGE: dari 'user_id' ke 'created_by'
        'title',
        'start_date',
        'end_date', 
        'type',
        'spotify_track_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // RELATIONSHIPS
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // NEW: Many-to-Many relationship (PIVOT)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Helper methods
    public function canBeViewedBy($user)
    {
        return $this->users->contains($user->id);
    }

    public function isCreatedBy($user)
    {
        return $this->created_by === $user->id;
    }

    // QUERY SCOPES - For filtering events
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_date', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month);
    }

    // ACCESSORS - For formatted display
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('d M Y, H:i');
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d M Y, H:i') : null;
    }

    public function getDateRangeAttribute()
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            // Same day event
            return $this->start_date->format('d M Y') . 
                   ' (' . $this->start_date->format('H:i') . 
                   ' - ' . $this->end_date->format('H:i') . ')';
        } else {
            // Multi-day event
            return $this->start_date->format('d M Y, H:i') . 
                   ' s/d ' . $this->end_date->format('d M Y, H:i');
        }
    }

    // HELPER METHODS - Business logic
    /**
     * Get icon based on event type
     */
    public function getTypeIcon()
    {
        return match($this->type) {
            'event' => '🎉',
            'cycle' => '🔄',
            'birthday' => '🎂',
            default => '📅'
        };
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            'event' => 'Event Umum',
            'cycle' => 'Cycle Tracking', 
            'birthday' => 'Birthday',
            default => 'Unknown'
        };
    }

    public function isUpcoming()
    {
        return $this->start_date->isFuture();
    }

    public function isPast()
    {
        return $this->start_date->isPast();
    }

    public function isToday()
    {
        return $this->start_date->isToday();
    }

    public function isRecurring()
    {
        return $this->type === 'cycle' || $this->type === 'birthday';
    }
}