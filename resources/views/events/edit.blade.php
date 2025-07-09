@extends('layouts.khairun')

@section('title', 'Edit Event - Our Memories')

@push('styles')
<style>
    .edit-event-container {
        max-width: 800px;
        margin: 3vh auto;
        padding: 0 2vw;
    }
    
    .edit-form-section {
        background: #343646;
        border-radius: 2.3vh;
        padding: 3vh 4vw;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .form-header {
        color: #8CE0FF;
        font-size: 1.8vw;
        font-family: 'DM Serif Text', serif;
        margin-bottom: 3vh;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1vw;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2vh 2vw;
        margin-top: 2vh;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-label {
        color: #D3D3D9;
        font-size: 1.04vw;
        font-weight: 500;
        margin-bottom: 1vh;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }
    
    .form-input, .form-select {
        background: transparent;
        border-radius: 1.25vh;
        border: 0.09vh solid #D3D3D9;
        padding: 1.5vh 1vw;
        color: #D3D3D9;
        font-size: 1.04vw;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #8CE0FF;
        background: rgba(140, 224, 255, 0.05);
    }
    
    .form-input::placeholder {
        color: #8F8C8C;
    }
    
    .form-select option {
        background: #181A26;
        color: #D3D3D9;
    }
    
    .sharing-section {
        background: rgba(140, 224, 255, 0.1);
        border: 1px solid #8CE0FF;
        border-radius: 1.5vh;
        padding: 2vh 2vw;
        margin-top: 2vh;
    }
    
    .sharing-title {
        color: #8CE0FF;
        font-size: 1.2vw;
        font-weight: 600;
        margin-bottom: 1.5vh;
        display: flex;
        align-items: center;
        gap: 0.5vw;
    }
    
    .user-checkbox {
        display: flex;
        align-items: center;
        gap: 1vw;
        padding: 1vh 0;
        transition: transform 0.3s ease;
    }
    
    .user-checkbox:hover {
        transform: translateX(5px);
    }
    
    .checkbox-input {
        width: 1.5vw;
        height: 1.5vw;
        min-width: 20px;
        min-height: 20px;
        accent-color: #8CE0FF;
        cursor: pointer;
    }
    
    .checkbox-label {
        color: #D3D3D9;
        font-size: 1vw;
        cursor: pointer;
        flex: 1;
    }
    
    .creator-badge {
        background: #8CE0FF;
        color: #181A26;
        padding: 0.3vh 0.8vw;
        border-radius: 1vh;
        font-size: 0.8vw;
        font-weight: 500;
    }
    
    .form-actions {
        margin-top: 3vh;
        display: flex;
        gap: 1vw;
        justify-content: flex-end;
        padding-top: 2vh;
        border-top: 1px solid rgba(211, 211, 217, 0.2);
    }
    
    .btn-cancel {
        background: #6D718D;
        color: white;
        padding: 1.2vh 2vw;
        border-radius: 5vh;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
    }
    
    .btn-cancel:hover {
        background: #5A5D75;
        transform: translateY(-2px);
    }
    
    .btn-update {
        background: #FFD93D;
        color: #181A26;
        padding: 1.2vh 2vw;
        border-radius: 5vh;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
    }
    
    .btn-update:hover {
        background: #FFC700;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .error-message {
        color: #FF6B6B;
        font-size: 0.9em;
        margin-top: 0.5vh;
        display: block;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-header {
            font-size: 24px;
        }
        
        .form-label {
            font-size: 14px;
        }
        
        .form-input, .form-select {
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="edit-event-container">
    <h1 class="page-title">Edit Event</h1>
    <p class="page-subtitle">Update your event details</p>
    
    <div class="edit-form-section">
        <h2 class="form-header">
            <span>✏️</span>
            <span>{{ $event->title }}</span>
        </h2>
        
        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Event Title -->
                <div class="form-group full-width">
                    <label class="form-label">
                        <span>📝</span>
                        <span>Event Name</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        value="{{ old('title', $event->title) }}"
                        class="form-input"
                        placeholder="Enter event name"
                        required
                    >
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Event Type -->
                <div class="form-group">
                    <label class="form-label">
                        <span>🏷️</span>
                        <span>Event Type</span>
                    </label>
                    <select name="type" class="form-select" required>
                        <option value="event" {{ old('type', $event->type) == 'event' ? 'selected' : '' }}>
                            🎉 General Event
                        </option>
                        <option value="cycle" {{ old('type', $event->type) == 'cycle' ? 'selected' : '' }}>
                            🔄 Cycle Tracking
                        </option>
                        <option value="birthday" {{ old('type', $event->type) == 'birthday' ? 'selected' : '' }}>
                            🎂 Birthday
                        </option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Spotify Track (Optional) -->
                <div class="form-group">
                    <label class="form-label">
                        <span>🎵</span>
                        <span>Background Music (Optional)</span>
                    </label>
                    <input 
                        type="text" 
                        name="spotify_track_id" 
                        value="{{ old('spotify_track_id', $event->spotify_track_id) }}"
                        class="form-input"
                        placeholder="Spotify track ID"
                    >
                    @error('spotify_track_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Start Date & Time -->
                <div class="form-group">
                    <label class="form-label">
                        <span>📅</span>
                        <span>Start Date & Time</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="start_date" 
                        value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}"
                        class="form-input"
                        required
                    >
                    @error('start_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- End Date & Time -->
                <div class="form-group">
                    <label class="form-label">
                        <span>📅</span>
                        <span>End Date & Time</span>
                    </label>
                    <input 
                        type="datetime-local" 
                        name="end_date" 
                        value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}"
                        class="form-input"
                        required
                    >
                    @error('end_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <!-- Sharing Section -->
            <div class="sharing-section">
                <h3 class="sharing-title">
                    <span>👥</span>
                    <span>Share with</span>
                </h3>
                
                @foreach($allUsers as $user)
                    @php
                        $isCurrentlyShared = in_array($user->id, $currentSharedUsers);
                        $isCreator = $user->id === $event->created_by;
                        $isCheckedFromOld = in_array($user->id, old('shared_with', []));
                        $shouldBeChecked = old('shared_with') ? $isCheckedFromOld : $isCurrentlyShared;
                    @endphp
                    
                    <label class="user-checkbox">
                        <input 
                            type="checkbox" 
                            name="shared_with[]" 
                            value="{{ $user->id }}"
                            class="checkbox-input"
                            {{ $isCreator ? 'checked disabled' : '' }}
                            {{ $shouldBeChecked && !$isCreator ? 'checked' : '' }}
                        >
                        <span class="checkbox-label">
                            {{ $user->name }}
                        </span>
                        @if($isCreator)
                            <span class="creator-badge">Creator</span>
                        @endif
                    </label>
                @endforeach
                
                @error('shared_with')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('calendar') }}" class="btn-cancel">
                    <span>←</span>
                    <span>Kembali ke Kalender</span>
                </a>
                <button type="submit" class="btn-update">
                    <span>✨</span>
                    <span>Update Event</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection