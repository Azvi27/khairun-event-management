@extends('layouts.khairun')

@section('title', 'Calendar - Our Memories')

@push('styles')
<style>
    /* Calendar Specific Styles */
    .calendar-layout {
        display: flex;
        gap: 2vw;
        margin-bottom: 3vh;
    }

    .calendar-section {
        flex: 2;
    }

    .event-section {
        flex: 1;
    }

    .content-section {
        background: #343646;
        border-radius: 2.3vh;
        padding: 2.2vh 3.1vw;
        position: relative;
    }

    .section-title {
        color: #D3D3D9;
        font-size: 1.4vw;
        font-family: 'DM Serif Text', serif;
        margin-bottom: 2vh;
        text-align: center;
    }

    /* Calendar Navigation */
    .calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2vh;
    }

    .nav-btn {
        background: #8CE0FF;
        border: none;
        border-radius: 50%;
        width: 2.5vw;
        height: 2.5vw;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2vw;
        color: #181A26;
        font-weight: bold;
        min-width: 35px;
        min-height: 35px;
    }

    .nav-btn:hover {
        background: #6bd4ff;
        transform: scale(1.1);
    }

    .month-year-selector {
        display: flex;
        align-items: center;
        gap: 1vw;
    }

    .month-select, .year-select {
        background: #181A26;
        color: #D3D3D9;
        border: 1px solid #8CE0FF;
        border-radius: 1vh;
        padding: 0.8vh 1.2vw;
        font-size: clamp(14px, 0.9vw, 18px);
        font-family: 'DM Serif Text', serif;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .month-select:hover, .year-select:hover {
        background: #343646;
        border-color: #6bd4ff;
    }

    .current-month-year {
        color: #8CE0FF;
        font-size: clamp(16px, 1.1vw, 22px);
        font-family: 'DM Serif Text', serif;
        font-weight: 600;
        min-width: 12vw;
        text-align: center;
    }

    /* Calendar Grid */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5vw;
        margin-top: 2vh;
    }

    .day-header {
        color: #D4D4D4;
        font-size: clamp(12px, 0.85vw, 16px);
        font-weight: 600;
        text-align: center;
        padding: 1vh;
    }

    .calendar-date {
        color: #D4D4D4;
        font-size: clamp(12px, 0.8vw, 16px);
        text-align: center;
        padding: 1.2vh;
        height: 3.5vw;
        min-height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5vh;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .calendar-date:hover {
        background: rgba(140, 224, 255, 0.1);
        transform: scale(1.05);
    }

    .calendar-date.today {
        background: rgba(140, 224, 255, 0.3);
        border: 2px solid #8CE0FF;
        font-weight: 600;
    }

    .calendar-date.other-month {
        color: #6D718D;
        opacity: 0.5;
    }

    .calendar-date.has-event::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        background: #8CE0FF;
        border-radius: 50%;
    }

    /* Quick Navigation */
    .quick-nav {
        display: flex;
        gap: 1vw;
        margin-top: 1vh;
        justify-content: center;
        flex-wrap: wrap;
    }

    .quick-nav-btn {
        background: transparent;
        border: 1px solid #8CE0FF;
        color: #8CE0FF;
        padding: 0.6vh 1.2vw;
        border-radius: 1vh;
        cursor: pointer;
        font-size: clamp(12px, 0.75vw, 14px);
        transition: all 0.3s ease;
    }

    .quick-nav-btn:hover {
        background: #8CE0FF;
        color: #181A26;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5vh;
    }

    .form-label {
        color: #D3D3D9;
        font-size: clamp(14px, 0.9vw, 18px);
        font-weight: 500;
        margin-bottom: 0.5vh;
        display: block;
    }

    .form-input {
        width: 100%;
        padding: 1vh;
        border-radius: 1vh;
        border: 1px solid #D3D3D9;
        background: transparent;
        color: #D3D3D9;
        font-size: clamp(13px, 0.8vw, 16px);
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #8CE0FF;
    }

    .form-row {
        display: flex;
        gap: 1vw;
    }

    .form-textarea {
        resize: vertical;
        min-height: 10vh;
    }

    .add-btn {
        background: #8CE0FF;
        border-radius: 2vh;
        padding: 1vh 2vw;
        color: #181A26;
        font-size: clamp(14px, 0.85vw, 16px);
        border: none;
        float: right;
        margin-top: 1vh;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .add-btn:hover {
        background: #6bd4ff;
        transform: translateY(-2px);
    }

    /* Events List */
    .events-list {
        background: #343646;
        border-radius: 2.3vh;
        padding: 2.2vh 3.1vw;
        margin-top: 3vh;
    }

    .events-title {
        color: #D3D3D9;
        font-size: clamp(18px, 1.4vw, 24px);
        font-family: 'DM Serif Text', serif;
        margin-bottom: 2vh;
        text-align: center;
    }

    .event-item {
        border-bottom: 1px solid #4A4D63;
        padding: 1.5vh 0;
        display: flex;
        align-items: center;
        gap: 1vw;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .event-item:hover {
        transform: translateX(5px);
    }

    .event-item:last-child {
        border-bottom: none;
    }

    .event-date-display {
        color: #8CE0FF;
        font-size: clamp(14px, 0.95vw, 18px);
        font-family: 'DM Serif Text', serif;
        min-width: 8vw;
        font-weight: 600;
    }

    .event-time {
        color: #D4D4D4;
        font-size: clamp(12px, 0.8vw, 15px);
        min-width: 4vw;
    }

    .event-separator {
        width: 3px;
        height: 2vh;
        background: #8CE0FF;
        border-radius: 2px;
    }

    .event-name-display {
        color: #D3D3D9;
        font-size: clamp(13px, 0.85vw, 16px);
        flex: 1;
    }

    .event-actions {
        display: flex;
        gap: 0.5vw;
    }

    .event-action-btn {
        color: #8CE0FF;
        font-size: 0.8em;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .event-action-btn:hover {
        color: #6bd4ff;
    }

    .form-row {
        display: flex;
        gap: 10px;
    }

    .form-row .form-input {
        flex: 1;
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .calendar-layout {
            flex-direction: column;
        }

        .calendar-section, .event-section {
            flex: 1;
        }
    }

    /* Sharing Options */
    .sharing-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 10px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(211, 211, 217, 0.1);
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .checkbox-item:hover {
        background: rgba(140, 224, 255, 0.1);
    }

    .checkbox-input {
        width: 18px;
        height: 18px;
        accent-color: #8CE0FF;
        cursor: pointer;
    }

    .checkbox-label {
        color: #D3D3D9;
        font-size: 14px;
        cursor: pointer;
        flex: 1;
    }

    .creator-badge {
        background: #8CE0FF;
        color: #181A26;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 8px;
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <h1 class="page-title">Calendar</h1>

    <!-- Calendar and Event Layout -->
    <div class="calendar-layout">
        <!-- Calendar Section -->
        <section class="content-section calendar-section">
            <div class="calendar-nav">
                <button class="nav-btn" id="prevMonth">‹</button>
                
                <div class="month-year-selector">
                    <select class="month-select" id="monthSelect">
                        <option value="0">Januari</option>
                        <option value="1">Februari</option>
                        <option value="2">Maret</option>
                        <option value="3">April</option>
                        <option value="4">Mei</option>
                        <option value="5">Juni</option>
                        <option value="6">Juli</option>
                        <option value="7">Agustus</option>
                        <option value="8">September</option>
                        <option value="9">Oktober</option>
                        <option value="10">November</option>
                        <option value="11">Desember</option>
                    </select>
                    
                    <select class="year-select" id="yearSelect">
                        @for($year = date('Y') - 5; $year <= date('Y') + 10; $year++)
                            <option value="{{ $year }}" {{ $year == $calendarData['year'] ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <button class="nav-btn" id="nextMonth">›</button>
            </div>

            <div class="current-month-year" id="currentMonthYear">
                {{ $calendarData['monthName'] }} {{ $calendarData['year'] }}
            </div>

            <div class="calendar-grid" id="calendarGrid">
                <!-- Days Header -->
                <div class="day-header">Min</div>
                <div class="day-header">Sen</div>
                <div class="day-header">Sel</div>
                <div class="day-header">Rab</div>
                <div class="day-header">Kam</div>
                <div class="day-header">Jum</div>
                <div class="day-header">Sab</div>
                
                <!-- Calendar Days -->
                        @php
                            $startOfMonth = $calendarData['firstDay'];
                            $endOfMonth = $calendarData['lastDay'];
                            $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                            $endOfCalendar = $endOfMonth->copy()->endOfWeek();
                            $currentDate = $startOfCalendar->copy();
                        @endphp

                        @while($currentDate <= $endOfCalendar)
                            @php
                                $dateString = $currentDate->format('Y-m-d');
                                $dayEvents = $eventsByDate[$dateString] ?? collect();
                                $isCurrentMonth = $currentDate->month === $calendarData['month'];
                                $isToday = $currentDate->isToday();
                            @endphp
                            
                    <div class="calendar-date 
                        {{ !$isCurrentMonth ? 'other-month' : '' }}
                        {{ $isToday ? 'today' : '' }}
                        {{ $dayEvents->count() > 0 ? 'has-event' : '' }}"
                        data-date="{{ $dateString }}"
                        title="{{ $dayEvents->count() > 0 ? $dayEvents->pluck('title')->join(', ') : '' }}">
                                    {{ $currentDate->day }}
                            </div>
                            
                            @php
                                $currentDate->addDay();
                            @endphp
                        @endwhile
                    </div>

            <div class="quick-nav">
                <button class="quick-nav-btn" onclick="goToToday()">Hari Ini</button>
                <button class="quick-nav-btn" onclick="goToMonth(-1)">Bulan Lalu</button>
                <button class="quick-nav-btn" onclick="goToMonth(1)">Bulan Depan</button>
                        </div>
        </section>

        <!-- Add Event Section -->
        <section class="content-section event-section">
            <h2 class="section-title">Add Event</h2>
            
            @if(session('success'))
                <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('events.store') }}" method="POST" id="eventForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Event Name</label>
                    <input type="text" name="title" class="form-input" 
                        placeholder="Enter event name" 
                        value="{{ old('title') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Start Date & Time</label>
                    <div class="form-row">
                        <input type="date" name="start_date" class="form-input" 
                            id="eventDate" value="{{ old('start_date') }}" required>
                        <input type="time" name="start_time" class="form-input" 
                            value="{{ old('start_time', '09:00') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">End Date & Time (Optional)</label>
                    <div class="form-row">
                        <input type="date" name="end_date" class="form-input" 
                            value="{{ old('end_date') }}">
                        <input type="time" name="end_time" class="form-input" 
                            value="{{ old('end_time') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">
                        <span class="icon">🏷️</span>
                        Event Type
                    </label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="">Select event type...</option>
                        <option value="event">🎉 General Event</option>
                        <option value="cycle">🔄 Cycle Tracking</option>
                        <option value="birthday">🎂 Birthday</option>
                    </select>
                    @error('type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ✅ TAMBAHKAN INI - Spotify Track Field -->
                <div class="form-group">
                    <label for="spotify_track_id" class="form-label">
                        <span class="icon">🎵</span>
                        Background Music (Optional)
                    </label>
                    <input 
                        type="text" 
                        id="spotify_track_id" 
                        name="spotify_track_id" 
                        value="{{ old('spotify_track_id') }}"
                        class="form-input"
                        placeholder="Spotify track ID (optional)"
                    >
                    @error('spotify_track_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Share With -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="icon">👥</span>
                        Share With
                    </label>
                    <div class="sharing-options">
                        @foreach($allUsers as $user)
                            <label class="checkbox-item">
                                <input 
                                    type="checkbox" 
                                    name="shared_with[]" 
                                    value="{{ $user->id }}"
                                    {{ auth()->id() === $user->id ? 'checked disabled' : '' }}
                                    {{ in_array($user->id, old('shared_with', [])) ? 'checked' : '' }}
                                    class="checkbox-input"
                                >
                                <span class="checkbox-label">
                                    {{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="creator-badge">(You)</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('shared_with')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input form-textarea" 
                            placeholder="Write your description here">{{ old('description') }}</textarea>
                </div>
                
                <button type="submit" class="add-btn">Add Event</button>
            </form>
        </section>
    </div>

    <!-- Events List Section -->
    <section class="events-list">
        <h2 class="events-title">Upcoming Events</h2>
        
        @if($upcomingEvents->count() > 0)
            @foreach($upcomingEvents as $event)
                <div class="event-item" onclick="window.location='{{ route('events.show', $event) }}'">
                    <div class="event-date-display">
                        {{ $event->start_date->format('d F') }}
                                    </div>
                    <div class="event-time">
                        {{ $event->start_date->format('H:i') }}
                            </div>
                    <div class="event-separator"></div>
                    <div class="event-name-display">
                        {{ $event->getTypeIcon() }} {{ $event->title }}
                        </div>
                    <div class="event-actions">
                        <a href="{{ route('events.edit', $event) }}" class="event-action-btn" 
                           onclick="event.stopPropagation()">Edit</a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" 
                              style="display: inline;" onsubmit="return confirm('Delete this event?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="event-action-btn" 
                                    onclick="event.stopPropagation()">Delete</button>
                        </form>
                        </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; color: #8F8C8C; padding: 2vh;">
                No upcoming events
            </div>
        @endif
    </section>
        </div>

@push('scripts')
<script>
// Calendar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const currentMonthYear = document.getElementById('currentMonthYear');
    
    // Set current month/year
    monthSelect.value = {{ $calendarData['month'] - 1 }};
    yearSelect.value = {{ $calendarData['year'] }};
    
    // Navigation handlers
    document.getElementById('prevMonth').addEventListener('click', () => {
        let month = parseInt(monthSelect.value);
        let year = parseInt(yearSelect.value);
        
        if (month === 0) {
            month = 11;
            year--;
        } else {
            month--;
        }
        
        navigateToMonth(year, month + 1);
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        let month = parseInt(monthSelect.value);
        let year = parseInt(yearSelect.value);
        
        if (month === 11) {
            month = 0;
            year++;
        } else {
            month++;
        }
        
        navigateToMonth(year, month + 1);
    });
    
    monthSelect.addEventListener('change', () => {
        navigateToMonth(yearSelect.value, parseInt(monthSelect.value) + 1);
    });
    
    yearSelect.addEventListener('change', () => {
        navigateToMonth(yearSelect.value, parseInt(monthSelect.value) + 1);
    });
    
    // Calendar date click handler
    document.querySelectorAll('.calendar-date:not(.other-month)').forEach(date => {
        date.addEventListener('click', function() {
            const dateValue = this.getAttribute('data-date');
            document.getElementById('eventDate').value = dateValue;
            
            // Highlight selected date
            document.querySelectorAll('.calendar-date').forEach(d => {
                d.classList.remove('highlight');
            });
            this.classList.add('highlight');
        });
    });
});

function navigateToMonth(year, month) {
    window.location.href = `{{ route('calendar') }}?year=${year}&month=${month}`;
}

function goToToday() {
    const today = new Date();
    navigateToMonth(today.getFullYear(), today.getMonth() + 1);
}

function goToMonth(offset) {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    
    let month = parseInt(monthSelect.value) + offset;
    let year = parseInt(yearSelect.value);
    
    if (month < 0) {
        month = 11;
        year--;
    } else if (month > 11) {
        month = 0;
        year++;
    }
    
    navigateToMonth(year, month + 1);
}
</script>
@endpush
@endsection