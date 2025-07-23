<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\BirthdaySurprise;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // ✅ EXISTING CODE - TIDAK DIUBAH SAMA SEKALI
        // Get recent memories for gallery
        $memories = Memory::where('user_id', auth()->id())
                         ->orderBy('memory_date', 'desc')
                         ->take(6)
                         ->get();
        
        // Get active birthday surprise for countdown - PERBAIKI FIELD NAME
        $nextSurprise = BirthdaySurprise::where('receiver_user_id', auth()->id())
                                          ->where('is_revealed', false)
                                          ->where('reveal_at', '>', now())
                                          ->orderBy('reveal_at', 'asc')
                                          ->first();
        
        // Get upcoming events for calendar section
        try {
            $upcomingEvents = Event::whereHas('users', function($query) {
                $query->where('user_id', auth()->id());
            })->where('start_date', '>=', now())
              ->orderBy('start_date', 'asc')
              ->take(5)
              ->get();
        } catch (\Exception $e) {
            // Fallback jika ada masalah dengan query
            $upcomingEvents = collect();
        }
        
        // Get events for current month (for mini calendar)
        $currentMonth = now();
        try {
            $monthlyEvents = Event::whereHas('users', function($query) {
                $query->where('user_id', auth()->id());
            })->whereYear('start_date', $currentMonth->year)
              ->whereMonth('start_date', $currentMonth->month)
              ->get()
              ->groupBy(function($event) {
                  return $event->start_date->format('Y-m-d');
              });
        } catch (\Exception $e) {
            // Fallback jika ada masalah dengan query
            $monthlyEvents = collect();
        }
        
        // Calendar data for mini calendar
        $calendarData = [
            'year' => $currentMonth->year,
            'month' => $currentMonth->month,
            'monthName' => $currentMonth->format('F Y'),
            'firstDay' => $currentMonth->copy()->startOfMonth(),
            'lastDay' => $currentMonth->copy()->endOfMonth(),
            'daysInMonth' => $currentMonth->daysInMonth,
            'startDayOfWeek' => $currentMonth->copy()->startOfMonth()->dayOfWeek,
        ];
        
        // ➕ NEW: Statistics calculation with safe fallback
        $stats = [];
        try {
            $stats = [
                'total_memories' => Memory::where('user_id', $user->id)->count(),
                'memories_this_month' => Memory::where('user_id', $user->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'upcoming_events' => Event::whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('start_date', '>', now())->count(),
                'pending_surprises' => BirthdaySurprise::where('receiver_user_id', $user->id)
                    ->where('is_revealed', false)
                    ->where('reveal_at', '>', now())
                    ->count(),
                'days_together' => $user->created_at->diffInDays(now()),
                'memories_with_music' => Memory::where('user_id', $user->id)
                    ->whereNotNull('spotify_track_id')
                    ->count(),
            ];
        } catch (\Exception $e) {
            // ✅ SAFE FALLBACK - Dashboard tetap berfungsi tanpa stats
            \Log::warning('Dashboard stats calculation failed', ['error' => $e->getMessage()]);
            $stats = null;
        }
        
        return view('dashboard', compact('memories', 'nextSurprise', 'upcomingEvents', 'monthlyEvents', 'calendarData', 'stats'));
    }
} 