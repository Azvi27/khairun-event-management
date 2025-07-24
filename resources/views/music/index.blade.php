@extends('layouts.khairun')

@section('title', 'Music - Our Memories')

@push('head')
<!-- Spotify Web Playback SDK -->
<script src="https://sdk.scdn.co/spotify-player.js"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/music.css') }}">
@endpush

@section('content')
<div class="music-container">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

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

    <!-- Spotify Connection Status -->
    @if(!$hasSpotifyConnection)
        <div class="spotify-connect-section">
            <div class="connect-card">
                <div class="connect-icon">🎵</div>
                <h3 class="connect-title">Connect to Spotify</h3>
                <p class="connect-description">
                    Connect your Spotify account to play music directly from your playlists and control playback.
                </p>
                <a href="{{ route('spotify.connect') }}" class="connect-btn">
                    <span>🎧</span>
                    <span>Connect Spotify Account</span>
                </a>
            </div>
        </div>
    @else
        <!-- Connected - Show User Info -->
        <div class="spotify-connected-section">
            <div class="connected-card">
                <div class="connected-info">
                    <img src="{{ auth()->user()->getSpotifyProfileImage() ?? 'https://via.placeholder.com/50x50?text=🎵' }}" 
                         alt="Spotify Profile" class="spotify-avatar">
                    <div class="connected-details">
                        <div class="connected-name">Connected to Spotify</div>
                        <div class="connected-user">{{ auth()->user()->getSpotifyDisplayName() }}</div>
                    </div>
                </div>
                <form action="{{ route('spotify.disconnect') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="disconnect-btn">Disconnect</button>
                </form>
            </div>
        </div>
    @endif

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

    <!-- User Playlists -->
    @if(count($userPlaylists) > 0)
        <h2 class="section-header">
            <span>🎵</span>
            <span>Your Spotify Playlists</span>
        </h2>
        <div class="playlists-grid">
            @foreach($userPlaylists as $playlist)
                <div class="playlist-card" onclick="loadPlaylist('{{ $playlist['id'] }}', '{{ $playlist['name'] }}')">
                    <img src="{{ $playlist['image'] ?? 'https://via.placeholder.com/300x300?text=Playlist' }}" 
                         alt="{{ $playlist['name'] }}" 
                         class="playlist-image">
                    <div class="playlist-info">
                        <div class="playlist-name">{{ $playlist['name'] }}</div>
                        <div class="playlist-description">{{ $playlist['description'] ?: 'No description' }}</div>
                        <div class="playlist-meta">
                            <span class="playlist-tracks">{{ $playlist['tracks_total'] }} tracks</span>
                            <span class="playlist-owner">by {{ $playlist['owner'] }}</span>
                        </div>
                        
                        <div class="playlist-actions">
                            <button class="playlist-btn btn-play" onclick="playPlaylist('{{ $playlist['id'] }}', '{{ $playlist['name'] }}', event)">
                                <span>▶️</span>
                                <span>Play</span>
                            </button>
                            @if($playlist['external_url'])
                                <a href="{{ $playlist['external_url'] }}" 
                                   target="_blank" 
                                   class="playlist-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Playlist Tracks Modal -->
        <div id="playlistModal" class="playlist-modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="playlistModalTitle">Playlist Tracks</h3>
                    <button class="modal-close" onclick="closePlaylistModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="tracks-grid" id="playlistTracks"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Memory Tracks -->
    @if($memoryTracks->count() > 0)
        <h2 class="section-header">
            <span>💭</span>
            <span>Soundtrack of Our Memories</span>
        </h2>
        <div class="tracks-grid">
            @foreach($memoryTracks as $track)
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}')">
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
                            @if($hasSpotifyConnection)
                                <button class="track-btn btn-play" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event)">
                                    <span>▶️</span>
                                    <span>Play Now</span>
                                </button>
                            @else
                                <button class="track-btn btn-disabled" onclick="showNotification('❌ Hubungkan Spotify untuk memutar musik', 'error');" disabled>
                                    <span>🔒</span>
                                    <span>Connect Spotify</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['memory']))
                                <a href="{{ route('memories.show', $track['memory']->id) }}" 
                                   class="track-btn btn-memory"
                                   onclick="event.stopPropagation()">
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
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}')">
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
                            @if($hasSpotifyConnection)
                                <button class="track-btn btn-play" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event)">
                                    <span>▶️</span>
                                    <span>Play Now</span>
                                </button>
                            @else
                                <button class="track-btn btn-disabled" onclick="showNotification('❌ Hubungkan Spotify untuk memutar musik', 'error');" disabled>
                                    <span>🔒</span>
                                    <span>Connect Spotify</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['event']))
                                <a href="{{ route('events.show', $track['event']->id) }}" 
                                   class="track-btn btn-memory"
                                   onclick="event.stopPropagation()">
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
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', null, '{{ $track['preview_url'] ?? '' }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        <div class="track-actions">
                            <button class="track-btn btn-play" onclick="if(isSpotifyConnected()) { playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event, '{{ $track['preview_url'] ?? '' }}'); } else { showNotification('❌ Hubungkan Spotify untuk memutar musik', 'error'); }" style="display: {{ $hasSpotifyConnection ? 'inline-flex' : 'none' }};">
                                <span>▶️</span>
                                <span>Play Now</span>
                            </button>
                            
                            <button class="track-btn btn-disabled" onclick="showNotification('❌ Hubungkan Spotify untuk memutar musik', 'error');" style="display: {{ $hasSpotifyConnection ? 'none' : 'inline-flex' }};" disabled>
                                <span>🔒</span>
                                <span>Connect Spotify</span>
                            </button>
                            
                            @if($track['preview_url'])
                                <button class="track-btn btn-preview" onclick="playPreviewAudio('{{ $track['preview_url'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}'); event.stopPropagation()">
                                    <span>🎧</span>
                                    <span>Preview</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
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
    // Jembatan PHP ke JavaScript - Data penting untuk SDK
    window.musicConfig = {
        spotifyToken: @json($spotifyToken ?? null),
        hasSpotifyConnection: @json($hasSpotifyConnection ?? false),
        csrfToken: @json(csrf_token()),
        routes: {
            play: @json(route('music.play')),
            transferPlayback: @json(route('music.transfer-playback')),
            search: @json(route('music.search')),
            playlist: '/music/playlist'
        },
        user: {
            name: @json(auth()->user()->name ?? 'Guest'),
            id: @json(auth()->user()->id ?? null)
        }
    };
    
    // Debug info untuk development
    console.log('Music Config loaded:', window.musicConfig);
    console.log('🎵 Loading music.js script...');
</script>
<script src="{{ asset('js/music.js') }}" onload="console.log('🎵 music.js loaded successfully!');" onerror="console.error('❌ Failed to load music.js!');"></script>
@endpush