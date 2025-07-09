@extends('layouts.khairun')

@section('title', 'Music - Our Memories')

@push('styles')
<style>
    .music-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
        background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
        min-height: 100vh;
    }
    
    .music-header {
        text-align: center;
        margin-bottom: 40px;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: 25px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }
    
    .music-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.1) 0%, rgba(255, 217, 61, 0.1) 100%);
        z-index: 1;
    }
    
    .music-header > * {
        position: relative;
        z-index: 2;
    }
    
    .music-title {
        color: #FFFFFF;
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-family: 'DM Serif Text', serif;
        margin-bottom: 15px;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        background: linear-gradient(45deg, #8CE0FF, #FFD93D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .music-subtitle {
        color: #E8E8E8;
        font-size: clamp(1rem, 2vw, 1.3rem);
        opacity: 0.9;
        font-style: italic;
    }

    /* Enhanced Music Player */
    .music-player {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 40px;
        border: 2px solid rgba(140, 224, 255, 0.2);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }

    .music-player::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(140, 224, 255, 0.1), transparent);
        transition: left 2s ease-in-out;
    }

    .music-player:hover::before {
        left: 100%;
    }

    .now-playing {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 25px;
        position: relative;
        z-index: 2;
    }

    .album-art {
        width: 80px;
        height: 80px;
        border-radius: 15px;
        background: linear-gradient(45deg, #8CE0FF, #FFD93D);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        animation: pulse 2s ease-in-out infinite alternate;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        100% { transform: scale(1.05); }
    }

    .track-details {
        flex: 1;
        color: #FFFFFF;
    }

    .current-track {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 5px;
        background: linear-gradient(45deg, #8CE0FF, #FFFFFF);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .current-artist {
        font-size: 1.1rem;
        color: #8CE0FF;
        opacity: 0.8;
    }

    /* Enhanced Progress Bar */
    .progress-container {
        position: relative;
        margin: 25px 0;
        z-index: 2;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: #8CE0FF;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, #1a1a2e 0%, #2a2a4e 100%);
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.5);
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #8CE0FF 0%, #FFD93D 50%, #8CE0FF 100%);
        border-radius: 20px;
        position: relative;
        transition: width 0.3s ease;
        background-size: 200% 100%;
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .progress-handle {
        position: absolute;
        right: -8px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        background: #FFFFFF;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.5);
        transition: all 0.3s ease;
    }

    .progress-bar:hover .progress-handle {
        transform: translateY(-50%) scale(1.2);
        box-shadow: 0 6px 20px rgba(140, 224, 255, 0.8);
    }

    /* Music Controls */
    .music-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .control-btn {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #1a1a2e;
        font-size: 1.2rem;
        box-shadow: 0 8px 25px rgba(140, 224, 255, 0.3);
    }

    .control-btn:hover {
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 12px 35px rgba(140, 224, 255, 0.5);
    }

    .control-btn.play {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #FFD93D 0%, #ffed4e 100%);
        font-size: 1.5rem;
    }

    .control-btn.play:hover {
        box-shadow: 0 12px 35px rgba(255, 217, 61, 0.5);
    }

    /* Volume Control */
    .volume-control {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 30px;
    }

    .volume-icon {
        color: #8CE0FF;
        font-size: 1.2rem;
    }

    .volume-slider {
        width: 100px;
        height: 6px;
        background: #2a2a4e;
        border-radius: 10px;
        appearance: none;
        cursor: pointer;
    }

    .volume-slider::-webkit-slider-thumb {
        appearance: none;
        width: 14px;
        height: 14px;
        background: #8CE0FF;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.5);
    }

    /* Track Cards - Enhanced */
    .tracks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .track-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 20px;
        padding: 25px;
        border: 2px solid rgba(140, 224, 255, 0.1);
        transition: all 0.4s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .track-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.05) 0%, rgba(255, 217, 61, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .track-card:hover::before {
        opacity: 1;
    }
    
    .track-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: rgba(140, 224, 255, 0.4);
        box-shadow: 0 20px 50px rgba(140, 224, 255, 0.2);
    }
    
    .track-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .track-card:hover .track-image {
        transform: scale(1.05);
    }
    
    .track-info {
        color: #FFFFFF;
        position: relative;
        z-index: 2;
    }
    
    .track-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 8px;
        font-family: 'DM Serif Text', serif;
        background: linear-gradient(45deg, #8CE0FF, #FFFFFF);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .track-artist {
        font-size: 1rem;
        color: #8CE0FF;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .track-album {
        font-size: 0.9rem;
        color: #D3D3D9;
        opacity: 0.8;
        margin-bottom: 15px;
    }

    /* Search Section - Enhanced */
    .search-section {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 40px;
        border: 2px solid rgba(140, 224, 255, 0.2);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }
    
    .search-input {
        width: 100%;
        background: rgba(140, 224, 255, 0.05);
        border: 2px solid rgba(140, 224, 255, 0.3);
        border-radius: 15px;
        padding: 18px 25px;
        color: #FFFFFF;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .search-input:focus {
        outline: none;
        border-color: #8CE0FF;
        background: rgba(140, 224, 255, 0.1);
        box-shadow: 0 0 30px rgba(140, 224, 255, 0.3);
    }

    .search-input::placeholder {
        color: rgba(140, 224, 255, 0.6);
    }

    /* Section Headers - Enhanced */
    .section-header {
        color: #8CE0FF;
        font-size: clamp(1.5rem, 3vw, 2.2rem);
        font-family: 'DM Serif Text', serif;
        margin: 50px 0 30px 0;
        display: flex;
        align-items: center;
        gap: 15px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .section-header span:first-child {
        font-size: 1.5em;
        filter: drop-shadow(0 0 10px rgba(140, 224, 255, 0.5));
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .now-playing {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .music-controls {
            flex-wrap: wrap;
            gap: 15px;
        }

        .volume-control {
            margin-left: 0;
            margin-top: 15px;
        }

        .tracks-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
    }

    /* Loading Animation */
    @keyframes loading {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading {
        animation: loading 1s linear infinite;
    }
</style>
@endpush

@section('content')
<div class="music-container">
    <!-- Header -->
    <div class="music-header">
        <h1 class="music-title">🎵 Our Playlist</h1>
        <p class="music-subtitle">For everything I couldn't say, I made a playlist instead.</p>
    </div>

    <!-- Enhanced Music Player -->
    <div class="music-player">
        <div class="now-playing">
            <div class="album-art">
                🎵
            </div>
            <div class="track-details">
                <div class="current-track" id="currentTrack">Perfect</div>
                <div class="current-artist" id="currentArtist">Ed Sheeran</div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-info">
                <span id="currentTime">2:15</span>
                <span id="totalTime">4:22</span>
            </div>
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill" style="width: 35%;">
                    <div class="progress-handle"></div>
                </div>
            </div>
        </div>

        <div class="music-controls">
            <button class="control-btn" id="prevBtn">⏮️</button>
            <button class="control-btn play" id="playBtn">▶️</button>
            <button class="control-btn" id="nextBtn">⏭️</button>
            <button class="control-btn" id="shuffleBtn">🔀</button>
            <button class="control-btn" id="repeatBtn">🔁</button>
            
            <div class="volume-control">
                <span class="volume-icon">🔊</span>
                <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="75">
            </div>
        </div>
    </div>

    @if($isMockMode)
    <!-- Mock Notice -->
    <div class="mock-notice">
        <p class="mock-notice-text">
            🎧 Demo Mode: Menampilkan data contoh. API Spotify akan aktif saat hosting.
        </p>
    </div>
    @endif

    <!-- Search Section -->
    <div class="search-section">
        <input type="text" 
               class="search-input" 
               placeholder="🔍 Search for songs, artists, or albums..."
               id="musicSearch">
        <div class="search-results" id="searchResults" style="display: none;">
            <div class="tracks-grid" id="searchGrid"></div>
        </div>
    </div>

    <!-- Memory Tracks -->
    @if($memoryTracks->count() > 0)
        <h2 class="section-header">
            <span>💭</span>
            <span>Soundtrack of Our Memories</span>
        </h2>
        <div class="tracks-grid">
            @foreach($memoryTracks as $track)
                <div class="track-card" onclick="playTrack('{{ $track['name'] }}', '{{ $track['artist'] }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        @if(isset($track['memory']))
                            <div class="memory-info">
                                💭 From memory: {{ $track['memory']->memory_date->format('d M Y') }}
                            </div>
                        @endif
                        
                        <div class="track-actions">
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify">
                                    <span>🎵</span>
                                    <span>Play on Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['memory']))
                                <a href="{{ route('memories.show', $track['memory']->id) }}" 
                                   class="track-btn btn-memory">
                                    <span>👁️</span>
                                    <span>View Memory</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Event Tracks -->
    @if($eventTracks->count() > 0)
        <h2 class="section-header">
            <span>📅</span>
            <span>Event Soundtracks</span>
        </h2>
        <div class="tracks-grid">
            @foreach($eventTracks as $track)
                <div class="track-card" onclick="playTrack('{{ $track['name'] }}', '{{ $track['artist'] }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        @if(isset($track['event']))
                            <div class="event-info">
                                📅 From event: {{ $track['event']->title }}
                            </div>
                        @endif
                        
                        <div class="track-actions">
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify">
                                    <span>🎵</span>
                                    <span>Play on Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['event']))
                                <a href="{{ route('events.show', $track['event']->id) }}" 
                                   class="track-btn btn-memory">
                                    <span>👁️</span>
                                    <span>View Event</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Recommended Tracks -->
    <h2 class="section-header">
        <span>🎶</span>
        <span>Recommended for You</span>
    </h2>
    @if(count($recommendedTracks) > 0)
        <div class="tracks-grid">
            @foreach($recommendedTracks as $track)
                <div class="track-card" onclick="playTrack('{{ $track['name'] }}', '{{ $track['artist'] }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        <div class="track-actions">
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify">
                                    <span>🎵</span>
                                    <span>Play on Spotify</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🎵</div>
            <p>No tracks available at the moment</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('musicSearch');
    const searchResults = document.getElementById('searchResults');
    const searchGrid = document.getElementById('searchGrid');
    let searchTimeout;

    // Player controls
    const playBtn = document.getElementById('playBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    const volumeSlider = document.getElementById('volumeSlider');
    
    let isPlaying = false;
    let currentProgress = 35;

    // Search functionality
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchMusic(query);
        }, 500);
    });

    // Player controls
    playBtn.addEventListener('click', function() {
        isPlaying = !isPlaying;
        this.innerHTML = isPlaying ? '⏸️' : '▶️';
        
        if (isPlaying) {
            startProgressAnimation();
        } else {
            stopProgressAnimation();
        }
    });

    // Progress bar interaction
    progressBar.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const percentage = (clickX / rect.width) * 100;
        
        progressFill.style.width = percentage + '%';
        currentProgress = percentage;
        
        // Update time display
        updateTimeDisplay(percentage);
    });

    // Volume control
    volumeSlider.addEventListener('input', function() {
        const volume = this.value;
        // Here you would control actual volume
        console.log('Volume set to:', volume);
    });

    function startProgressAnimation() {
        // Simulate progress animation
        const interval = setInterval(() => {
            if (!isPlaying) {
                clearInterval(interval);
                return;
            }
            
            currentProgress += 0.1;
            if (currentProgress >= 100) {
                currentProgress = 0;
                isPlaying = false;
                playBtn.innerHTML = '▶️';
                clearInterval(interval);
            }
            
            progressFill.style.width = currentProgress + '%';
            updateTimeDisplay(currentProgress);
        }, 100);
    }

    function stopProgressAnimation() {
        // Animation stopped by changing isPlaying flag
    }

    function updateTimeDisplay(percentage) {
        const totalSeconds = 262; // 4:22 in seconds
        const currentSeconds = Math.floor((percentage / 100) * totalSeconds);
        
        const minutes = Math.floor(currentSeconds / 60);
        const seconds = currentSeconds % 60;
        
        document.getElementById('currentTime').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    // Global function for track selection
    window.playTrack = function(trackName, artistName) {
        document.getElementById('currentTrack').textContent = trackName;
        document.getElementById('currentArtist').textContent = artistName;
        
        // Reset progress
        currentProgress = 0;
        progressFill.style.width = '0%';
        updateTimeDisplay(0);
        
        // Start playing
        isPlaying = true;
        playBtn.innerHTML = '⏸️';
        startProgressAnimation();
    };

    async function searchMusic(query) {
        try {
            const response = await fetch(`{{ route('music.search') }}?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            displaySearchResults(data.tracks || []);
        } catch (error) {
            console.error('Search failed:', error);
        }
    }

    function displaySearchResults(tracks) {
        if (tracks.length === 0) {
            searchResults.style.display = 'none';
            return;
        }

        searchGrid.innerHTML = tracks.map(track => `
            <div class="track-card" onclick="playTrack('${track.name}', '${track.artist}')">
                <img src="${track.image || 'https://via.placeholder.com/300x300?text=No+Image'}" 
                     alt="${track.name}" 
                     class="track-image">
                <div class="track-info">
                    <div class="track-name">${track.name}</div>
                    <div class="track-artist">${track.artist}</div>
                    <div class="track-album">${track.album}</div>
                    
                    <div class="track-actions">
                        ${track.external_url ? `
                            <a href="${track.external_url}" 
                               target="_blank" 
                               class="track-btn btn-spotify"
                               onclick="event.stopPropagation()">
                                <span>🎵</span>
                                <span>Play on Spotify</span>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        searchResults.style.display = 'block';
    }
});
</script>
@endpush