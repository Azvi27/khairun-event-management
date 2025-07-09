@extends('layouts.khairun')

@section('title', 'Dashboard - Our Memories')

@push('styles')
<link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Success Login Message -->
@if(session('success'))
    <div style="
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%); 
        color: #181A26; 
        padding: 15px 25px; 
        border-radius: 12px; 
        margin-bottom: 20px; 
        text-align: center; 
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(140, 224, 255, 0.3);
    ">
        🎉 {{ session('success') }}
    </div>
@endif

<!-- Hero Section -->
<section class="hero">
    <h1 class="hero-title">
        A journey told in memories — stitched together by time, and always open for the stories you choose to keep.
    </h1>
    <a href="{{ route('memories.create') }}" class="cta-button add-memory-btn">
        ➕ Add New Memory
    </a>
</section>

<!-- Memories Timeline -->
<section class="content-section">
    <h2 class="section-title">Our Gallery of Memories</h2>
    
    @if($memories->count() > 0)
        <div class="memory-timeline">
            @foreach($memories as $memory)
                <div class="memory-card" onclick="window.location.href='{{ route('memories.show', $memory) }}'" style="cursor: pointer;">
                    <div class="memory-date">
                        📅 {{ $memory->memory_date->format('d F Y') }}
                    </div>
                    
                    <!-- Memory Preview -->
                    <div class="memory-preview">
                        @if($memory->image_path)
                            <div class="memory-image-container">
                                <img src="{{ app('App\Services\StorageService')->getFileUrl($memory->image_path) }}" 
                                     alt="Current memory" 
                                     class="current-image">
                                <div class="memory-image-overlay">
                                    <span class="memory-overlay-text">📸</span>
                                </div>
                            </div>
                        @else
                            <div class="memory-text-preview">
                                <div class="memory-text-icon">💭</div>
                                <div class="memory-text-content">
                                    "{{ Str::limit($memory->description, 80) }}"
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="memory-description">
                        {{ Str::limit($memory->description, 60) }}
                    </div>
                    
                    <div class="memory-actions">
                        <span class="memory-action-hint">👁️ Click to view</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="memory-empty-state">
            <div class="empty-state-icon">📸</div>
            <div class="empty-state-text">
                <h3>No memories yet!</h3>
                <p>Start creating your beautiful journey together</p>
                <a href="{{ route('memories.create') }}" class="empty-state-btn">
                    ➕ Add First Memory
                </a>
            </div>
        </div>
    @endif
</section>

<!-- Countdown Section -->
<section class="countdown-section">
    @if($nextSurprise)
        <h2 class="countdown-title">
            Countdown to your next surprise! 🎁
        </h2>
        <div class="timer-container" onclick="window.location.href='{{ route('birthday-surprises.show', $nextSurprise->id) }}'" style="cursor: pointer;">
            <div class="timer-box" id="days">0</div>
            <div class="timer-box" id="hours">0</div>
            <div class="timer-box" id="minutes">0</div>
            <div class="timer-box" id="seconds">0</div>
        </div>
        <div class="timer-labels">
            <span>Days</span>
            <span>Hours</span>
            <span>Minutes</span>
            <span>Seconds</span>
        </div>
        <p class="surprise-info">
            �� Surprise from {{ $nextSurprise->sender->name ?? 'Someone special' }} 
            on {{ $nextSurprise->reveal_at->format('d F Y, H:i') }}
        </p>
    @else
        <h2 class="countdown-title">
            No upcoming surprises... Create one! 🎁
        </h2>
        <a href="{{ route('birthday-surprises.create') }}" class="cta-button" style="margin-top: 20px;">
            🎁 Create Surprise
        </a>
    @endif
</section>

<!-- Music Section -->
<section class="content-section music-section">
    <h2 class="section-title">For everything I couldn't say, I made a playlist instead.</h2>
    <p class="music-description">Click here — let the music take you where words once lived.</p>
    
    <a href="{{ route('music.index') }}" class="playlist-link" style="text-decoration: none;">
        <div class="playlist">
            <div class="song-item">
                <div class="song-cover">🎵</div>
                <div class="song-info">
                    <div class="song-title">Melukis Senja</div>
                    <div class="song-artist">Budi Doremi</div>
                </div>
            </div>
            <div class="song-item">
                <div class="song-cover">🎶</div>
                <div class="song-info">
                    <div class="song-title">Hati-Hati di Jalan</div>
                    <div class="song-artist">Tulus</div>
                </div>
            </div>
            <div class="song-item">
                <div class="song-cover">🎤</div>
                <div class="song-info">
                    <div class="song-title">Perfect</div>
                    <div class="song-artist">Ed Sheeran</div>
                </div>
            </div>
            <div class="song-item">
                <div class="song-cover">❤️</div>
                <div class="song-info">
                    <div class="song-title">Right Here Waiting</div>
                    <div class="song-artist">Richard Marx</div>
                </div>
            </div>
        </div>
    </a>
</section>
@endsection

@push('scripts')
<script>
// Countdown Timer JavaScript
    document.addEventListener('DOMContentLoaded', function() {
    @if($nextSurprise)
        const targetDate = new Date('{{ $nextSurprise->reveal_at }}').getTime();
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                // Countdown finished - redirect ke surprise
                window.location.href = '{{ route('birthday-surprises.show', $nextSurprise->id) }}';
            }
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    @endif
});
</script>
@endpush
