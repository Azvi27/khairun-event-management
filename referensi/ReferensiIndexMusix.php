@extends('layouts.khairun')

@section('title', 'Music - Our Memories')

@push('head')
<!-- Spotify Web Playback SDK -->
<script src="https://sdk.scdn.co/spotify-player.js"></script>
@endpush

@push('styles')
{{-- Semua CSS Anda tetap sama persis, tidak perlu diubah --}}
<style>
    /* ... (semua CSS Anda yang sudah ada di sini) ... */
</style>
@endpush

@section('content')
{{-- Semua HTML Anda tetap sama persis, tidak perlu diubah --}}
<div class="music-container">
    <!-- ... (semua HTML Anda dari header, player, connection status, search, dll) ... -->

    <!-- Contoh bagian Tracks Grid yang dimodifikasi -->
    <h2 class="section-header">
        <span>🎶</span>
        <span>Recommended for You</span>
    </h2>
    <div class="tracks-grid">
        @foreach($recommendedTracks as $track)
            <div class="track-card">
                <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" alt="{{ $track['name'] }}" class="track-image">
                <div class="track-info">
                    <div class="track-name">{{ $track['name'] }}</div>
                    <div class="track-artist">{{ $track['artist'] }}</div>
                    <div class="track-actions">
                        @if($hasSpotifyConnection)
                            <button class="track-btn btn-play" onclick="playTrack('spotify:track:{{ $track['id'] }}')">
                                <span>▶️</span>
                                <span>Play</span>
                            </button>
                        @endif
                        <a href="{{ $track['external_url'] }}" target="_blank" class="track-btn btn-spotify">
                            <span>🎵</span>
                            <span>Open in Spotify</span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // =================================================================
    // LOGIKA BARU UNTUK SPOTIFY WEB PLAYBACK SDK
    // =================================================================
    let spotifyPlayer = null;
    let spotifyDeviceId = null;
    const spotifyToken = '{{ $spotifyToken ?? null }}';
    const hasConnection = {{ $hasSpotifyConnection ? 'true' : 'false' }};

    if (hasConnection && spotifyToken) {
        window.onSpotifyWebPlaybackSDKReady = () => {
            spotifyPlayer = new Spotify.Player({
                name: 'Khairun Memory Web Player',
                getOAuthToken: cb => { cb(spotifyToken); },
                volume: 0.5
            });

            // Error handling
            spotifyPlayer.addListener('initialization_error', ({ message }) => { 
                console.error('SDK Init Error:', message); 
                showNotification('Gagal memuat Spotify Player.', 'error');
            });
            spotifyPlayer.addListener('authentication_error', ({ message }) => { 
                console.error('SDK Auth Error:', message); 
                showNotification('Sesi Spotify berakhir, silakan hubungkan ulang.', 'error');
            });
            spotifyPlayer.addListener('account_error', ({ message }) => { 
                console.error('SDK Account Error:', message); 
                showNotification('Akun Spotify Anda harus Premium untuk fitur ini.', 'error');
            });

            // Player siap digunakan
            spotifyPlayer.addListener('ready', ({ device_id }) => {
                console.log('Spotify Player siap dengan Device ID:', device_id);
                spotifyDeviceId = device_id;
                showNotification('🎧 Spotify Player siap!', 'success');
                // Otomatis transfer playback ke browser
                transferPlaybackToThisDevice(device_id);
            });

            // Player terputus
            spotifyPlayer.addListener('not_ready', ({ device_id }) => {
                console.log('Device ID terputus:', device_id);
                spotifyDeviceId = null;
            });

            // Hubungkan player
            spotifyPlayer.connect();
        };
    }

    function transferPlaybackToThisDevice(deviceId) {
        fetch('{{ route("music.transfer-playback") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ device_id: deviceId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('Playback berhasil ditransfer ke browser.');
            } else {
                console.error('Gagal transfer playback:', data.error);
            }
        });
    }

    // =================================================================
    // PERBAIKAN: Jadikan fungsi ini global agar bisa diakses dari onclick
    // =================================================================
    window.playTrack = function(trackUri) {
        if (!hasConnection) {
            showNotification('Hubungkan akun Spotify Anda terlebih dahulu.', 'error');
            return;
        }
        if (!spotifyDeviceId) {
            showNotification('Spotify Player belum siap. Coba refresh halaman atau pastikan tidak ada AdBlocker.', 'error');
            return;
        }

        fetch('{{ route("music.play") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                uris: [trackUri], // Spotify API mengharapkan array 'uris'
                device_id: spotifyDeviceId 
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification('🎵 Memutar lagu...', 'success');
            } else {
                const errorMessage = data.error || 'Gagal memutar lagu.';
                showNotification(`❌ ${errorMessage}`, 'error');
                console.error('Gagal memutar:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan jaringan.', 'error');
        });
    };

    // Fungsi notifikasi sederhana
    function showNotification(message, type = 'info') {
        const container = document.querySelector('.music-container');
        if (!container) return;
        
        const oldNotification = container.querySelector('.alert.dynamic-notification');
        if(oldNotification) oldNotification.remove();

        const notification = document.createElement('div');
        notification.className = `alert alert-${type} dynamic-notification`;
        notification.textContent = message;
        
        container.prepend(notification);
        setTimeout(() => notification.remove(), 5000);
    }

    // Sisa JavaScript Anda bisa tetap di sini.
});
</script>
@endpush
