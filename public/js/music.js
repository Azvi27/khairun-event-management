// =================================================================
// MUSIC PLAYER - Spotify Web Playback SDK Integration
// =================================================================

console.log('🎵 music.js script starting...');

// ✅ SPOTIFY SDK CALLBACK - Required for SDK to work
window.onSpotifyWebPlaybackSDKReady = () => {
    console.log('🎵 Spotify Web Playback SDK is ready!');
    // SDK is now available, initialize if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
};

// ✅ FALLBACK: Check if SDK is already loaded after a delay
setTimeout(() => {
    if (!window.Spotify || !window.Spotify.Player) {
        console.warn('⚠️ Spotify SDK not loaded after 3 seconds. This may be due to network issues or ad blockers.');
        console.log('🎵 Continuing with limited functionality...');
        // Initialize anyway for basic functionality
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize);
        } else {
            initialize();
        }
    }
}, 3000);

// ✅ DEBUG: Check if DOM is ready
if (document.readyState === 'loading') {
    console.log('🎵 DOM still loading, waiting...');
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎵 DOM loaded, checking if SDK is ready...');
        // Only initialize if SDK is already loaded
        if (window.Spotify && window.Spotify.Player) {
            initialize();
        } else {
            console.log('🎵 Waiting for Spotify SDK to load...');
        }
    });
} else {
    console.log('🎵 DOM already loaded, checking SDK...');
    // Only initialize if SDK is already loaded
    if (window.Spotify && window.Spotify.Player) {
        initialize();
    } else {
        console.log('🎵 Waiting for Spotify SDK to load...');
    }
}

// ✅ DEBUG: Check if window.musicConfig exists
if (window.musicConfig) {
    console.log('🎵 musicConfig found:', window.musicConfig);
} else {
    console.error('❌ musicConfig not found!');
}

// =================================================================
// GLOBAL VARIABLES
// =================================================================
let spotifyPlayer = null;
let spotifyDeviceId = null;
let isPlayerReady = false;
let currentPlayerState = null;
let playerInitializationPromise = null;

// Configuration from backend
const config = window.musicConfig || {};
const spotifyToken = config.spotifyToken || null;
let hasConnection = config.hasSpotifyConnection || false;
const routes = config.routes || {};
const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// ✅ FALLBACK: Jika config tidak ada, coba ambil dari DOM
if (!hasConnection && document.querySelector('.connected-card')) {
    console.log('Fallback: Detected connected-card in DOM, assuming Spotify is connected');
    hasConnection = true;
}

// ✅ DEBUG: Log configuration yang diterima
console.log('Music Config loaded:', {
    hasConnection,
    hasToken: !!spotifyToken,
    routes: Object.keys(routes),
    fullConfig: config
});

// ✅ DEBUG: Log status koneksi saat halaman load
console.log('Initial Spotify Connection Status:', {
    hasConnection,
    configHasConnection: config.hasSpotifyConnection,
    hasToken: !!spotifyToken,
    connectedCard: !!document.querySelector('.connected-card'),
    connectedName: document.querySelector('.connected-name')?.textContent
});

// =================================================================
// DOM ELEMENTS
// =================================================================
const searchInput = document.getElementById('musicSearch');
const searchResults = document.getElementById('searchResults');
const searchGrid = document.getElementById('searchGrid');
const playBtn = document.getElementById('playBtn');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const progressBar = document.getElementById('progressBar');
const progressFill = document.getElementById('progressFill');
const volumeSlider = document.getElementById('volumeSlider');

// ✅ DEBUG: Log DOM elements status
console.log('🔍 DOM Elements Status:', {
    searchInput: !!searchInput,
    searchResults: !!searchResults,
    searchGrid: !!searchGrid,
    playBtn: !!playBtn,
    prevBtn: !!prevBtn,
    nextBtn: !!nextBtn
});

let searchTimeout;

// =================================================================
// SPOTIFY PLAYER INITIALIZATION - Promise-based for guaranteed timing
// =================================================================
function initializeSpotifyPlayer() {
    if (playerInitializationPromise) {
        return playerInitializationPromise;
    }
    
    if (!hasConnection || !spotifyToken) {
        console.log('Spotify not available:', { hasConnection, hasToken: !!spotifyToken });
        console.log('ℹ️ Spotify connection required for full player features');
        return Promise.resolve(false);
    }
    
    if (!window.Spotify || !window.Spotify.Player) {
        console.error('Spotify SDK not loaded - waiting for onSpotifyWebPlaybackSDKReady callback');
        console.log('Make sure Spotify SDK script is loaded: https://sdk.scdn.co/spotify-player.js');
        return Promise.resolve(false);
    }
    
    console.log('Creating Spotify Player...');
    
    playerInitializationPromise = new Promise((resolve, reject) => {
        try {
            spotifyPlayer = new Spotify.Player({
                name: 'Music Player Web',
                getOAuthToken: cb => { cb(spotifyToken); },
                volume: 0.5
            });
            
            setupPlayerEventListeners(resolve, reject);
            connectPlayer();
            
        } catch (error) {
            console.error('Error creating Spotify Player:', error);
            showNotification('❌ Gagal membuat Spotify Player', 'error');
            reject(error);
        }
    });
    
    return playerInitializationPromise;
}

function setupPlayerEventListeners(resolve, reject) {
    if (!spotifyPlayer) return;
    
    // Error handling
    spotifyPlayer.addListener('initialization_error', ({ message }) => {
        console.error('SDK Init Error:', message);
        showNotification('❌ Gagal memuat Spotify Player', 'error');
        if (reject) reject(new Error(message));
    });
    
    spotifyPlayer.addListener('authentication_error', ({ message }) => {
        console.error('SDK Auth Error:', message);
        showNotification('❌ Sesi Spotify berakhir, silakan hubungkan ulang', 'error');
        if (reject) reject(new Error(message));
    });
    
    spotifyPlayer.addListener('account_error', ({ message }) => {
        console.error('SDK Account Error:', message);
        showNotification('❌ Akun Spotify Anda harus Premium untuk fitur ini', 'error');
        if (reject) reject(new Error(message));
    });
    
    // Player ready - Promise resolution
    spotifyPlayer.addListener('ready', ({ device_id }) => {
        console.log('Spotify Player ready with Device ID:', device_id);
        spotifyDeviceId = device_id;
        isPlayerReady = true;
        showNotification('🎧 Spotify Player siap!', 'success');
        transferPlaybackToThisDevice(device_id);
        if (resolve) resolve(true);
    });
    
    // Player disconnected
    spotifyPlayer.addListener('not_ready', ({ device_id }) => {
        console.log('Device disconnected:', device_id);
        spotifyDeviceId = null;
        isPlayerReady = false;
    });
    
    // State changes - Single Source of Truth
    spotifyPlayer.addListener('player_state_changed', (state) => {
        console.log('Player state changed:', state);
        currentPlayerState = state;
        updateUIFromPlayerState(state);
    });
}

function connectPlayer() {
    if (!spotifyPlayer) return;
    
    spotifyPlayer.connect().then(success => {
        if (success) {
            console.log('Successfully connected to Spotify!');
        } else {
            console.error('Failed to connect to Spotify');
            showNotification('❌ Gagal terhubung ke Spotify', 'error');
        }
    });
}

function updateUIFromPlayerState(state) {
    if (state) {
        const currentTrack = state.track_window.current_track;
        const isPlaying = !state.paused;
        const position = state.position;
        const duration = state.duration;
        
        // Update track info
        updateCurrentTrackInfo(currentTrack.name, currentTrack.artists[0].name);
        
        // Update play button
        updatePlayButton(isPlaying);
        
        // Update progress
        updateProgressBar(position, duration);
        
        // Update album art
        const albumArt = document.getElementById('currentAlbumArt');
        if (albumArt && currentTrack.album.images[0]) {
            albumArt.src = currentTrack.album.images[0].url;
        }
        
        // Update document title
        document.title = isPlaying 
            ? `🎵 ${currentTrack.name} - ${currentTrack.artists[0].name} | Music Player`
            : 'Music Player';
    } else {
        // Player stopped
        updatePlayButton(false);
        document.title = 'Music Player';
    }
}

function transferPlaybackToThisDevice(deviceId) {
    const transferUrl = routes.transferPlayback || '/music/transfer-playback';
    
    fetch(transferUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ device_id: deviceId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log('Playback transferred to browser');
        } else {
            console.error('Failed to transfer playback:', data.error);
        }
    })
    .catch(error => {
        console.error('Error transferring playback:', error);
    });
}

// =================================================================
// PLAYER CONTROLS - Promise-based with guaranteed readiness
// =================================================================
async function playItem(spotifyUri, itemName = '', artistName = '') {
    console.log('playItem called:', { spotifyUri, itemName, artistName });
    
    if (!hasConnection) {
        showNotification('❌ Hubungkan akun Spotify Anda terlebih dahulu', 'error');
        return;
    }
    
    // Wait for player to be ready
    try {
        await ensurePlayerReady();
    } catch (error) {
        console.error('Player not ready:', error);
        
        if (error.message.includes('SDK not loaded')) {
            showNotification('❌ Spotify SDK tidak dimuat. Periksa koneksi internet atau refresh halaman.', 'error');
        } else if (error.message.includes('connection required')) {
            showNotification('❌ Hubungkan akun Spotify Anda terlebih dahulu', 'error');
        } else {
            showNotification('❌ Pemutar Spotify tidak tersedia', 'error');
        }
        return;
    }
    
    const playUrl = routes.play || '/music/play';
    const isTrack = spotifyUri.includes('track:');
    
    const requestBody = {
        device_id: spotifyDeviceId,
        ...(isTrack ? { uris: [spotifyUri] } : { context_uri: spotifyUri })
    };
    
    fetch(playUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(requestBody)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const message = itemName ? `🎵 Memutar: ${itemName}` : '🎵 Memutar musik...';
            showNotification(message, 'success');
        } else {
            const errorMessage = data.error || 'Gagal memutar musik';
            showNotification(`❌ ${errorMessage}`, 'error');
            
            if (errorMessage.includes('premium') || errorMessage.includes('Premium')) {
                showNotification('❌ Fitur ini memerlukan akun Spotify Premium', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error playing:', error);
        showNotification('❌ Terjadi kesalahan jaringan', 'error');
    });
}

// Helper function to ensure player readiness
async function ensurePlayerReady() {
    // Check if SDK is available
    if (!window.Spotify || !window.Spotify.Player) {
        throw new Error('Spotify SDK not loaded');
    }
    
    // Check if we have connection and token
    if (!hasConnection || !spotifyToken) {
        throw new Error('Spotify connection required');
    }
    
    if (isPlayerReady && spotifyDeviceId) {
        return true;
    }
    
    if (playerInitializationPromise) {
        return await playerInitializationPromise;
    }
    
    // Try to initialize if not already done
    return await initializeSpotifyPlayer();
}

// Unified control function - eliminates manual state management
async function togglePlayback() {
    try {
        await ensurePlayerReady();
        
        if (!currentPlayerState) {
            showNotification('🎵 Pilih lagu dari daftar untuk mulai memutar musik', 'info');
            return;
        }
        
        if (currentPlayerState.paused) {
            await spotifyPlayer.resume();
            showNotification('▶️ Musik dilanjutkan', 'info');
        } else {
            await spotifyPlayer.pause();
            showNotification('⏸️ Musik dijeda', 'info');
        }
    } catch (error) {
        console.error('Error toggling playback:', error);
        showNotification('❌ Gagal mengontrol pemutar', 'error');
    }
}

async function nextTrack() {
    try {
        await ensurePlayerReady();
        await spotifyPlayer.nextTrack();
        showNotification('⏭️ Lagu berikutnya', 'info');
    } catch (error) {
        console.error('Error next track:', error);
        showNotification('❌ Gagal ke lagu berikutnya', 'error');
    }
}

async function previousTrack() {
    try {
        await ensurePlayerReady();
        await spotifyPlayer.previousTrack();
        showNotification('⏮️ Lagu sebelumnya', 'info');
    } catch (error) {
        console.error('Error previous track:', error);
        showNotification('❌ Gagal ke lagu sebelumnya', 'error');
    }
}

// =================================================================
// UI UPDATES
// =================================================================
function updateCurrentTrackInfo(trackName, artistName) {
    const currentTrackElement = document.getElementById('currentTrack');
    const currentArtistElement = document.getElementById('currentArtist');
    
    if (currentTrackElement) currentTrackElement.textContent = trackName;
    if (currentArtistElement) currentArtistElement.textContent = artistName;
}

function updatePlayButton(isPlaying) {
    // Update main play button
    if (playBtn) {
        playBtn.textContent = isPlaying ? '⏸️' : '▶️';
        playBtn.title = isPlaying ? 'Pause' : 'Play';
    }
    
    // Update track play buttons - more robust selection
    const playButtons = document.querySelectorAll('.btn-play, .play-btn');
    playButtons.forEach(btn => {
        const icon = btn.querySelector('span:first-child');
        const text = btn.querySelector('span:last-child');
        if (icon && text) {
            icon.textContent = isPlaying ? '⏸️' : '▶️';
            text.textContent = isPlaying ? 'Pause' : 'Play';
        }
    });
}

function updateProgressBar(position, duration) {
    const progressBar = document.getElementById('progressBar');
    const currentTime = document.getElementById('currentTime');
    const totalTime = document.getElementById('totalTime');
    
    if (progressBar && duration > 0) {
        const progress = (position / duration) * 100;
        progressBar.style.width = progress + '%';
    }
    
    if (currentTime) currentTime.textContent = formatTime(position);
    if (totalTime) totalTime.textContent = formatTime(duration);
}

function formatTime(ms) {
    const minutes = Math.floor(ms / 60000);
    const seconds = Math.floor((ms % 60000) / 1000);
    return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
}

function showNotification(message, type = 'info') {
    const container = document.querySelector('.music-container');
    if (!container) return;
    
    const oldNotification = container.querySelector('.alert.dynamic-notification');
    if (oldNotification) oldNotification.remove();
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} dynamic-notification`;
    notification.textContent = message;
    
    container.prepend(notification);
    setTimeout(() => notification.remove(), 5000);
}

// =================================================================
// EVENT LISTENERS - Unified Setup
// =================================================================
function setupEventListeners() {
    // Main play button - simplified logic
    if (playBtn) {
        playBtn.addEventListener('click', function() {
            console.log('Main play button clicked');
            
            if (!hasConnection) {
                showNotification('❌ Hubungkan akun Spotify Anda terlebih dahulu', 'error');
                return;
            }
            
            // Use unified toggle function
            togglePlayback();
        });
    }
    
    // Previous/Next buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', previousTrack);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', nextTrack);
    }
    
    // Search functionality
    if (searchInput) {
        console.log('🔍 Setting up search input listener');
        console.log('🔍 searchInput element:', searchInput);
        console.log('🔍 searchInput type:', searchInput.type);
        console.log('🔍 searchInput id:', searchInput.id);
        
        // ✅ CHECK EXISTING LISTENERS (removed getEventListeners as it's not available in all browsers)
        console.log('🔍 Setting up search input listener...');
        
        // ✅ REMOVE OLD LISTENER FIRST
        if (searchInput._inputHandler) {
            console.log('🔍 Removing old input handler');
            searchInput.removeEventListener('input', searchInput._inputHandler);
        }
        
        // ✅ CREATE NEW HANDLER
        searchInput._inputHandler = function(event) {
            console.log('🔍 Search input event triggered!');
            console.log('🔍 Event type:', event.type);
            console.log('🔍 Input value:', this.value);
            console.log('🔍 Event target:', event.target);
            console.log('🔍 Event currentTarget:', event.currentTarget);
            
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                console.log('🔍 Query too short, hiding results');
                if (searchResults) searchResults.style.display = 'none';
                return;
            }
            
            console.log('🔍 Setting timeout for search:', query);
            searchTimeout = setTimeout(() => {
                console.log('🔍 Timeout triggered, calling searchMusic');
                searchMusic(query);
            }, 500);
        };
        
        // ✅ ADD NEW LISTENER
        searchInput.addEventListener('input', searchInput._inputHandler);
        console.log('🔍 Search input listener attached successfully');
        
        // ✅ TEST: Trigger a test event
        setTimeout(() => {
            console.log('🔍 Testing search input listener...');
            const testEvent = new Event('input', { bubbles: true });
            searchInput.dispatchEvent(testEvent);
        }, 1000);
        
    } else {
        console.error('❌ searchInput not found!');
        console.error('❌ Available elements with "search" in id:');
        document.querySelectorAll('[id*="search"]').forEach(el => {
            console.error('  -', el.id, ':', el);
        });
    }
    
    // Close search results when clicking outside
    document.addEventListener('click', function(event) {
        const searchSection = document.querySelector('.search-section');
        if (searchSection && !searchSection.contains(event.target) && searchResults) {
            searchResults.style.display = 'none';
        }
    });
    
    // Prevent search results from closing when clicking inside
    if (searchResults) {
        searchResults.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
}

// =================================================================
// SEARCH FUNCTIONALITY
// =================================================================
function searchMusic(query) {
    if (!searchGrid || !searchResults) return;
    
    console.log('🔍 searchMusic called with query:', query);
    
    searchGrid.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--color-text-secondary);">🔍 Mencari musik...</div>';
    searchResults.style.display = 'block';
    
    const searchUrl = routes.search || '/music/search';
    console.log('🔍 Fetching from URL:', searchUrl);
    
    fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('🔍 Search response:', data);
        if (data.success && data.tracks && data.tracks.length > 0) {
            console.log('🔍 Calling displaySearchResults with', data.tracks.length, 'tracks');
            displaySearchResults(data.tracks);
        } else {
            console.log('🔍 No tracks found, showing empty message');
            searchGrid.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--color-text-secondary);">🎵 Tidak ada hasil ditemukan</div>';
        }
    })
    .catch(error => {
        console.error('❌ Error searching music:', error);
        searchGrid.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--color-text-secondary);">❌ Terjadi kesalahan saat mencari</div>';
    });
}

function displaySearchResults(tracks) {
    console.log('🎵 displaySearchResults called with', tracks.length, 'tracks');
    console.log('🎵 First track:', tracks[0]);
    
    if (!searchGrid) {
        console.error('❌ searchGrid not found!');
        return;
    }
    
    // ✅ IMPROVED: Deteksi koneksi Spotify yang lebih robust
    const hasSpotifyToken = !!(spotifyToken || (window.musicConfig && window.musicConfig.spotifyToken));
    const hasConnectionStatus = config.hasSpotifyConnection || hasConnection;
    const hasConnectedCard = !!document.querySelector('.connected-card');
    const hasConnectedName = !!document.querySelector('.connected-name');
    const hasDisconnectButton = !!document.querySelector('form[action*="spotify.disconnect"]');
    
    console.log('🎵 displaySearchResults - Debug Info:', {
        hasSpotifyToken,
        hasConnectionStatus,
        hasConnectedCard,
        hasConnectedName,
        hasDisconnectButton,
        spotifyToken: spotifyToken ? 'EXISTS' : 'NULL',
        configSpotifyToken: window.musicConfig && window.musicConfig.spotifyToken ? 'EXISTS' : 'NULL',
        configHasConnection: config.hasSpotifyConnection,
        hasConnection: hasConnection
    });
    
    // ✅ IMPROVED: Logika yang lebih komprehensif
    const shouldShowPlayButton = hasSpotifyToken || 
                               hasConnectionStatus || 
                               hasConnectedCard || 
                               hasConnectedName || 
                               hasDisconnectButton;
    
    console.log('🎵 displaySearchResults - shouldShowPlayButton:', shouldShowPlayButton);
    
    searchGrid.innerHTML = tracks.map(track => {
        const escapedName = track.name.replace(/'/g, "\\'").replace(/"/g, '\\"');
        const escapedArtist = track.artist.replace(/'/g, "\\'").replace(/"/g, '\\"');
        
        const playButton = shouldShowPlayButton ? `
            <button class="track-btn btn-play" onclick="event.stopPropagation(); playSpotifyTrack('${track.id}', '${escapedName}', '${escapedArtist}', event);">
                <span>▶️</span>
                <span>Play Now</span>
            </button>
        ` : `
            <button class="track-btn btn-disabled" onclick="event.stopPropagation(); showNotification('❌ Hubungkan Spotify untuk memutar musik', 'error');" disabled>
                <span>🔒</span>
                <span>Connect Spotify</span>
            </button>
        `;
        
        return `
            <div class="track-card">
                <img src="${track.image || 'https://via.placeholder.com/300x300?text=No+Image'}" 
                     alt="${escapedName}" 
                     class="track-image">
                <div class="track-info">
                    <div class="track-name">${track.name}</div>
                    <div class="track-artist">${track.artist}</div>
                    <div class="track-album">${track.album || ''}</div>
                    
                    <div class="track-actions">
                        ${playButton}
                        
                        ${track.external_url ? `
                            <a href="${track.external_url}" 
                               target="_blank" 
                               class="track-btn btn-spotify"
                               onclick="event.stopPropagation()">
                                <span>🎵</span>
                                <span>Open in Spotify</span>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    console.log('🎵 displaySearchResults completed, HTML generated');
}

// =================================================================
// GLOBAL FUNCTIONS - For onclick handlers
// =================================================================
// ✅ HELPER FUNCTION: Deteksi status koneksi Spotify dari DOM
window.isSpotifyConnected = function() {
    // Cek dari config terlebih dahulu
    if (config.hasSpotifyConnection) {
        console.log('isSpotifyConnected: true (from config)');
        return true;
    }
    
    // Cek dari window.musicConfig
    if (window.musicConfig && window.musicConfig.hasSpotifyConnection) {
        console.log('isSpotifyConnected: true (from window.musicConfig)');
        return true;
    }
    
    // Cek dari variabel hasConnection
    if (hasConnection) {
        console.log('isSpotifyConnected: true (from hasConnection)');
        return true;
    }
    
    // Cek dari token - jika ada token, berarti connect
    if (spotifyToken) {
        console.log('isSpotifyConnected: true (from token)');
        return true;
    }
    
    // Cek dari window.musicConfig token
    if (window.musicConfig && window.musicConfig.spotifyToken) {
        console.log('isSpotifyConnected: true (from window.musicConfig token)');
        return true;
    }
    
    // Cek dari DOM - jika ada elemen connected-card, berarti sudah connect
    if (document.querySelector('.connected-card')) {
        console.log('isSpotifyConnected: true (from DOM connected-card)');
        return true;
    }
    
    // Cek dari DOM - jika ada elemen connected-name, berarti sudah connect
    if (document.querySelector('.connected-name')) {
        console.log('isSpotifyConnected: true (from DOM connected-name)');
        return true;
    }
    
    // Cek dari DOM - jika ada form disconnect, berarti sudah connect
    if (document.querySelector('form[action*="spotify.disconnect"]')) {
        console.log('isSpotifyConnected: true (from DOM disconnect form)');
        return true;
    }
    
    // Cek dari DOM - jika ada text "Connected to Spotify"
    if (document.querySelector('.connected-name') && document.querySelector('.connected-name').textContent.includes('Connected to Spotify')) {
        console.log('isSpotifyConnected: true (from DOM connected-name text)');
        return true;
    }
    
    console.log('isSpotifyConnected: false (no connection detected)');
    return false;
};

window.showNotification = function(message, type = 'info') {
    const container = document.querySelector('.music-container');
    if (!container) return;
    
    const oldNotification = container.querySelector('.alert.dynamic-notification');
    if (oldNotification) oldNotification.remove();
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} dynamic-notification`;
    notification.textContent = message;
    
    container.prepend(notification);
    setTimeout(() => notification.remove(), 5000);
};

window.playSpotifyTrack = function(trackId, trackName, artistName, event) {
    console.log('🎵 playSpotifyTrack called:', { trackId, trackName, artistName, event });
    
    // ✅ SAFE: Handle event properly
    if (event && typeof event.stopPropagation === 'function') {
        event.stopPropagation();
    }
    
    if (!trackId) {
        showNotification('❌ Track ID tidak valid', 'error');
        return;
    }
    
    console.log('🎵 Playing track:', trackName, 'by', artistName);
    const trackUri = `spotify:track:${trackId}`;
    playItem(trackUri, trackName, artistName);
};

window.playPlaylist = function(playlistId, playlistName, event) {
    console.log('🎵 playPlaylist called:', { playlistId, playlistName, event });
    
    // ✅ SAFE: Handle event properly
    if (event && typeof event.stopPropagation === 'function') {
        event.stopPropagation();
    }
    
    if (!playlistId) {
        showNotification('❌ Playlist ID tidak valid', 'error');
        return;
    }
    
    console.log('🎵 Playing playlist:', playlistName);
    const playlistUri = `spotify:playlist:${playlistId}`;
    playItem(playlistUri, playlistName);
};

window.playPreviewAudio = function(previewUrl, trackName, artistName) {
    if (!previewUrl) {
        showNotification('Preview tidak tersedia untuk lagu ini', 'error');
        return;
    }
    
    // Stop existing preview
    const existingAudio = document.getElementById('previewAudio');
    if (existingAudio) {
        existingAudio.pause();
        existingAudio.remove();
    }
    
    // Create new audio element
    const audio = document.createElement('audio');
    audio.id = 'previewAudio';
    audio.src = previewUrl;
    audio.volume = 0.5;
    audio.play();
    
    updateCurrentTrackInfo(trackName + ' (Preview)', artistName);
    showNotification('🎧 Memutar preview...', 'info');
    
    // Auto stop after 30 seconds
    setTimeout(() => {
        if (audio && !audio.paused) {
            audio.pause();
            audio.remove();
        }
    }, 30000);
};

window.loadPlaylist = function(playlistId, playlistName) {
    const modal = document.getElementById('playlistModal');
    const modalTitle = modal?.querySelector('.modal-title');
    const modalBody = modal?.querySelector('.modal-body');
    
    if (modalTitle) modalTitle.textContent = playlistName;
    if (modal) modal.style.display = 'block';
    
    const playlistUrl = routes.playlist || `/music/playlist/${playlistId}`;
    
    fetch(playlistUrl)
        .then(res => res.json())
        .then(data => {
            if (data.success && modalBody) {
                modalBody.innerHTML = data.tracks.map(track => `
                    <div class="playlist-track" onclick="playSpotifyTrack('${track.id}', '${track.name}', '${track.artists[0]?.name || ''}')">
                        <img src="${track.album?.images[2]?.url || ''}" alt="${track.name}" class="track-thumb">
                        <div class="track-details">
                            <div class="track-name">${track.name}</div>
                            <div class="track-artist">${track.artists.map(a => a.name).join(', ')}</div>
                        </div>
                        <div class="track-duration">${formatDuration(track.duration_ms)}</div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Error loading playlist:', err);
            if (modalBody) modalBody.innerHTML = '<p>Gagal memuat playlist.</p>';
        });
};

window.closePlaylistModal = function() {
    const modal = document.getElementById('playlistModal');
    if (modal) modal.style.display = 'none';
};

function formatDuration(ms) {
    const minutes = Math.floor(ms / 60000);
    const seconds = ((ms % 60000) / 1000).toFixed(0);
    return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
}

// Backward compatibility - simplified
window.playItem = playItem;
window.playTrack = function(trackUri) { playItem(trackUri); };
window.togglePlayback = togglePlayback;
window.nextTrack = nextTrack;
window.previousTrack = previousTrack;

// =================================================================
// INITIALIZATION SEQUENCE
// =================================================================
function initialize() {
    console.log('🎵 initialize() called');
    
    // ✅ DEBUG: Check DOM elements
    console.log('🎵 Checking DOM elements...');
    console.log('🎵 searchInput:', searchInput);
    console.log('🎵 searchResults:', searchResults);
    console.log('🎵 searchGrid:', searchGrid);
    
    // Initialize Spotify Web Playback SDK
    initializeSpotifyPlayer();
    
    // Setup event listeners
    setupEventListeners();
    
    // ✅ DEBUG: Check if event listeners were set up
    console.log('🎵 Event listeners setup completed');
    
    // ✅ AUTO-FIX: Otomatis tampilkan tombol play jika user connect
    setTimeout(() => {
        if (window.isSpotifyConnected()) {
            console.log('Auto-showing play buttons for connected user');
            showPlayButtons();
        }
    }, 1000); // Initial load
    
    // ✅ AUTO-FIX: Jalankan lagi setelah search results muncul
    const originalDisplaySearchResults = displaySearchResults;
    displaySearchResults = function(tracks) {
        originalDisplaySearchResults(tracks);
        setTimeout(() => {
            if (window.isSpotifyConnected()) {
                console.log('Auto-fixing play buttons in search results');
                showPlayButtons();
            }
        }, 100);
    };
}

// ✅ HELPER: Tampilkan tombol play untuk semua track
function showPlayButtons() {
    const playButtons = document.querySelectorAll('.btn-play');
    const disabledButtons = document.querySelectorAll('.btn-disabled');
    
    playButtons.forEach(btn => {
        btn.style.display = 'inline-flex';
    });
    
    disabledButtons.forEach(btn => {
        btn.style.display = 'none';
    });
}

// Start the initialization
// initialize(); // This line is now handled by the DOMContentLoaded listener

// ✅ AUTO-FIX: Otomatis tampilkan tombol play jika user connect
// Jalankan setelah DOM fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Tunggu sebentar untuk memastikan semua elemen sudah ter-render
    setTimeout(() => {
        if (window.isSpotifyConnected()) {
            console.log('Auto-fixing play buttons for connected user');
            showPlayButtons();
        }
    }, 2000);
});

// ✅ AUTO-FIX: Jalankan lagi setelah search results muncul
// Ini untuk memastikan tombol play muncul di hasil pencarian
const originalDisplaySearchResults = displaySearchResults;
displaySearchResults = function(tracks) {
    originalDisplaySearchResults(tracks);
    
    // Setelah menampilkan hasil, cek lagi status koneksi
    setTimeout(() => {
        if (window.isSpotifyConnected()) {
            console.log('Auto-fixing play buttons in search results');
            showPlayButtons();
        }
    }, 100);
};