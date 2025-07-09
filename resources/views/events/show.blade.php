@extends('layouts.khairun')

@section('title', 'Event Detail - Our Memories')

@push('styles')
<style>
    .event-detail-container {
        max-width: 900px;
        margin: 3vh auto;
        padding: 0 2vw;
    }
    
    .event-detail-card {
        background: linear-gradient(135deg, #343646 0%, #3a3d52 100%);
        border-radius: 2.3vh;
        padding: 4vh 4vw;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(140, 224, 255, 0.2);
    }
    
    .event-detail-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #8CE0FF, #FFD93D, #FF6B6B);
    }
    
    .event-header {
        display: flex;
        align-items: center;
        gap: 1.5vw;
        margin-bottom: 3vh;
        padding-bottom: 2vh;
        border-bottom: 1px solid rgba(211, 211, 217, 0.2);
    }
    
    .event-icon-large {
        font-size: 3.5em;
        filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
    }
    
    .event-title-section {
        flex: 1;
    }
    
    .event-title {
        color: #FFFFFF;
        font-size: 2.2vw;
        font-family: 'DM Serif Text', serif;
        margin-bottom: 1vh;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .event-type-badge {
        display: inline-block;
        padding: 0.8vh 2vw;
        border-radius: 2vh;
        font-size: 0.9vw;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .type-event {
        background: rgba(140, 224, 255, 0.2);
        color: #8CE0FF;
        border: 2px solid #8CE0FF;
    }
    
    .type-cycle {
        background: rgba(255, 107, 107, 0.2);
        color: #FF6B6B;
        border: 2px solid #FF6B6B;
    }
    
    .type-birthday {
        background: rgba(255, 217, 61, 0.2);
        color: #FFD93D;
        border: 2px solid #FFD93D;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2vw;
        margin-bottom: 3vh;
    }
    
    .info-card {
        background: rgba(211, 211, 217, 0.1);
        border-radius: 1.5vh;
        padding: 2.5vh 2vw;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(140, 224, 255, 0.1);
    }
    
    .info-card-title {
        color: #8CE0FF;
        font-size: 1.2vw;
        font-family: 'DM Serif Text', serif;
        margin-bottom: 1.5vh;
        display: flex;
        align-items: center;
        gap: 0.5vw;
        font-weight: 600;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8vh 0;
        border-bottom: 1px solid rgba(211, 211, 217, 0.1);
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #D3D3D9;
        font-size: 0.9vw;
        font-weight: 500;
    }
    
    .info-value {
        color: #FFFFFF;
        font-size: 0.9vw;
        font-weight: 400;
    }
    
    .shared-users {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5vw;
        margin-top: 1vh;
    }
    
    .user-badge {
        background: rgba(140, 224, 255, 0.2);
        color: #8CE0FF;
        border: 1px solid rgba(140, 224, 255, 0.3);
        padding: 0.5vh 1vw;
        border-radius: 1.5vh;
        font-size: 0.8vw;
        font-weight: 500;
    }
    
    .creator-badge {
        background: rgba(255, 217, 61, 0.2);
        color: #FFD93D;
        border: 1px solid rgba(255, 217, 61, 0.3);
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2vh;
        border-top: 1px solid rgba(211, 211, 217, 0.2);
        margin-top: 2vh;
    }
    
    .btn-back {
        background: rgba(109, 113, 141, 0.8);
        color: #FFFFFF;
        padding: 1.2vh 2vw;
        border-radius: 5vh;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        border: 1px solid rgba(109, 113, 141, 0.3);
    }
    
    .btn-back:hover {
        background: rgba(109, 113, 141, 1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .btn-actions {
        display: flex;
        gap: 1vw;
    }
    
    .btn-edit {
        background: rgba(140, 224, 255, 0.2);
        color: #8CE0FF;
        padding: 1.2vh 2vw;
        border-radius: 5vh;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
        border: 2px solid #8CE0FF;
    }
    
    .btn-edit:hover {
        background: rgba(140, 224, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.3);
    }
    
    .btn-delete {
        background: rgba(255, 107, 107, 0.2);
        color: #FF6B6B;
        padding: 1.2vh 2vw;
        border-radius: 5vh;
        border: 2px solid #FF6B6B;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5vw;
    }
    
    .btn-delete:hover {
        background: rgba(255, 107, 107, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
    }
    
    .single-column {
        grid-column: 1 / -1;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .event-title {
            font-size: 24px;
        }
        
        .info-card-title {
            font-size: 18px;
        }
        
        .event-header {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="event-detail-container">
    <div class="event-detail-card">
        <!-- Event Header -->
        <div class="event-header">
            <span class="event-icon-large">{{ $event->getTypeIcon() }}</span>
            <div class="event-title-section">
                <h1 class="event-title">{{ $event->title }}</h1>
                <span class="event-type-badge {{ $event->type === 'cycle' ? 'type-cycle' : ($event->type === 'birthday' ? 'type-birthday' : 'type-event') }}">
                    {{ $event->getTypeLabel() }}
                </span>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="info-grid">
            <!-- Time Information -->
            <div class="info-card">
                <h4 class="info-card-title">
                    <span>📅</span>
                    <span>Informasi Waktu</span>
                </h4>
                <div class="info-item">
                    <span class="info-label">Mulai:</span>
                    <span class="info-value">{{ $event->start_date->format('d F Y, H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Selesai:</span>
                    <span class="info-value">{{ $event->end_date->format('d F Y, H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Durasi:</span>
                    <span class="info-value">{{ $event->date_range }}</span>
                </div>
            </div>

            <!-- Sharing Information -->
            <div class="info-card">
                <h4 class="info-card-title">
                    <span>👥</span>
                    <span>Informasi Sharing</span>
                </h4>
                <div class="info-item">
                    <span class="info-label">Dibuat oleh:</span>
                    <span class="info-value">{{ $event->creator->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dibagikan dengan:</span>
                </div>
                <div class="shared-users">
                    @foreach($event->users as $user)
                        <span class="user-badge {{ $user->id === $event->created_by ? 'creator-badge' : '' }}">
                            {{ $user->name }}
                            @if($user->id === $event->created_by)
                                (Creator)
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="info-grid">
            <div class="info-card single-column">
                <h4 class="info-card-title">
                    <span>📋</span>
                    <span>Metadata</span>
                </h4>
                <div class="info-item">
                    <span class="info-label">Dibuat pada:</span>
                    <span class="info-value">{{ $event->created_at->format('d F Y, H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Terakhir diupdate:</span>
                    <span class="info-value">{{ $event->updated_at->format('d F Y, H:i') }}</span>
                </div>
                @if($event->spotify_track_id)
                    <div class="info-item">
                        <span class="info-label">Background Music:</span>
                        <span class="info-value">🎵 {{ $event->spotify_track_id }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('calendar') }}" class="btn-back">
                <span>←</span>
                <span>Kembali ke Kalender</span>
            </a>
            
            <div class="btn-actions">
                @if($event->canBeViewedBy(auth()->user()))
                    <a href="{{ route('events.edit', $event->id) }}" class="btn-edit">
                        <span>✏️</span>
                        <span>Edit Event</span>
                    </a>
                @endif
                
                @if($event->isCreatedBy(auth()->user()))
                    <form method="POST" action="{{ route('events.destroy', $event->id) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.')">
                            <span>🗑️</span>
                            <span>Hapus Event</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection