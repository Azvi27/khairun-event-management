<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\SpotifyService;

class EventController extends Controller
{
    // Method CRUD standard (sama pattern dengan BirthdaySurpriseController)
    /** 
     * Tampilkan daftar events yang bisa dilihat user
     */

    //
    /*
    public function index()
    {
        $user = auth()->user();
        
        // Events yang user bisa lihat (created by user + shared to user)
        $events = Event::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('start_date', 'asc')->get();
        
        return view('events.index', compact('events'));
    }

    /**
    Tampilkan form untuk buat event baru
     
    public function create()
    {
        $currentUser = auth()->user();
        $allUsers = User::all(); // Azvi & Khairun untuk sharing options
        
        return view('events.create', compact('currentUser', 'allUsers'));
    }
    
    /**
     * Simpan event baru ke database dengan sharing logic
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'end_time' => 'nullable|date_format:H:i',
            'type' => 'required|in:event,cycle,birthday',
            'shared_with' => 'nullable|array',
            'shared_with.*' => 'exists:users,id',
            'spotify_track_id' => 'nullable|string',
        ]);

        // Combine date and time
        $startDateTime = $validated['start_date'];
        if (!empty($validated['start_time'])) {
            $startDateTime = $validated['start_date'] . ' ' . $validated['start_time'];
        }

        $endDateTime = $validated['end_date'] ?? $startDateTime;
        if (!empty($validated['end_time'])) {
            $endDateTime = ($validated['end_date'] ?? $validated['start_date']) . ' ' . $validated['end_time'];
        }

        // Create event
        $event = Event::create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'type' => $validated['type'],
            'spotify_track_id' => $validated['spotify_track_id'],
        ]);

        // Handle sharing
        $sharedWith = $validated['shared_with'] ?? [];
        if (!in_array(auth()->id(), $sharedWith)) {
            $sharedWith[] = auth()->id(); // Always include creator
        }
        
        $event->users()->sync($sharedWith);

        return redirect()->route('calendar')
            ->with('success', 'Event berhasil dibuat! 🎉');
    }

    /**
    * Tampilkan form edit event (dengan current sharing info)
    */
    public function edit(Event $event)
    {
        $user = auth()->user();
        
        // 1. SECURITY CHECK - User harus bisa lihat event ini
        if (!$event->canBeViewedBy($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. PREPARE DATA
        $allUsers = User::all();
        $currentSharedUsers = $event->users->pluck('id')->toArray(); // Current sharing
        
        // 3. RETURN EDIT FORM
        return view('events.edit', compact('event', 'allUsers', 'currentSharedUsers'));
    }

    /**
    * Update event dan sharing permissions
    */
    public function update(Request $request, Event $event)
    {
        $user = auth()->user();
        
        // 1. SECURITY CHECK - User harus bisa lihat event ini
        if (!$event->canBeViewedBy($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. VALIDATION
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:event,cycle,birthday',
            'shared_with' => 'array',
            'shared_with.*' => 'exists:users,id',
            'spotify_track_id' => 'nullable|string|max:255'
        ]);

        // 3. UPDATE EVENT DATA
        $event->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'type' => $validated['type'],
            'spotify_track_id' => $validated['spotify_track_id'] ?? null
        ]);

        // 4. UPDATE SHARING (SYNC PIVOT TABLE)
        $userIds = $validated['shared_with'] ?? [];
        
        // Ensure creator always has access
        if (!in_array($event->created_by, $userIds)) {
            $userIds[] = $event->created_by;
        }
        
        // Sync users (removes old, adds new)
        $event->users()->sync($userIds);

        // 5. SUCCESS RESPONSE
        return redirect()->route('calendar')
                        ->with('success', 'Event berhasil diperbarui! ✨');
    }

    /**
    * Tampilkan detail event (hanya jika user punya akses)
    */
    public function show(Event $event)
    {
        $user = auth()->user();
        
        // 1. SECURITY CHECK - User harus bisa lihat event ini
        if (!$event->canBeViewedBy($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. LOAD RELATIONSHIPS (untuk tampilan detail)
        $event->load(['creator', 'users']); // Eager loading
        
        // 3. RETURN DETAIL VIEW
        return view('events.show', compact('event'));
    }
    
    /**
    * Hapus event (hanya creator yang boleh)
    */
    public function destroy(Event $event)
    {
        $user = auth()->user();
        
        // 1. SECURITY CHECK - Hanya creator yang boleh delete
        if (!$event->isCreatedBy($user)) {
            abort(403, 'Hanya pembuat event yang bisa menghapus event ini.');
        }
        
        // 2. DELETE EVENT (pivot records akan auto-delete karena cascade)
        $event->delete();
        
        // 3. SUCCESS RESPONSE - ✅ REDIRECT KE CALENDAR
        return redirect()->route('calendar')
                        ->with('success', 'Event berhasil dihapus! 🗑️');
    }
    
    // Method khusus untuk calendar
    /**
    * Tampilkan calendar view dengan events
    */
    public function calendar(Request $request)
    {
        $user = auth()->user();
        
        // 1. GET MONTH & YEAR (default: current month)
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // 2. GET EVENTS FOR THIS MONTH
        $events = Event::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereYear('start_date', $year)
          ->whereMonth('start_date', $month)
          ->get();
        
        // 3. GROUP EVENTS BY DATE (untuk calendar display)
        $eventsByDate = $events->groupBy(function($event) {
            return $event->start_date->format('Y-m-d');
        });
        
        // 4. GET UPCOMING EVENTS (next 30 days)
        $upcomingEvents = Event::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('start_date', '>=', now())
          ->orderBy('start_date', 'asc')
          ->take(10)
          ->get();
        
        // ✅ TAMBAHKAN INI - All users untuk sharing options
        $allUsers = User::all();
        
        // 5. CALENDAR DATA
        $calendarData = [
            'year' => $year,
            'month' => $month,
            'monthName' => Carbon::create($year, $month)->format('F Y'),
            'firstDay' => Carbon::create($year, $month, 1),
            'lastDay' => Carbon::create($year, $month, 1)->endOfMonth(),
        ];
        
        // 6. RETURN CALENDAR VIEW - ✅ TAMBAHKAN allUsers
        return view('events.calendar', compact('eventsByDate', 'calendarData', 'upcomingEvents', 'allUsers'));
    }
}