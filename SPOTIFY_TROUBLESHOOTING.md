# Spotify Play Functionality Troubleshooting Guide

## Masalah: Fungsi "Play" Tidak Bekerja di Hosting Environment

Berdasarkan analisis kode, berikut adalah kemungkinan penyebab dan solusi untuk masalah fungsi play yang tidak bekerja di hosting environment:

## 🔍 Kemungkinan Penyebab

### 1. **Tidak Ada Device Spotify yang Aktif**
- **Masalah**: Spotify Web Playback SDK memerlukan device yang aktif untuk memulai playback
- **Gejala**: API mengembalikan error "No active device found"
- **Solusi**: 
  - Pastikan Spotify app terbuka di device user
  - Atau implementasikan Spotify Web Playback SDK di website

### 2. **Akun Spotify Bukan Premium**
- **Masalah**: Spotify Web API playback hanya bekerja untuk akun Premium
- **Gejala**: API mengembalikan error "Premium required"
- **Solusi**: User harus menggunakan akun Spotify Premium

### 3. **Token Spotify Expired atau Invalid**
- **Masalah**: Access token sudah expired atau tidak valid
- **Gejala**: API mengembalikan 401 Unauthorized
- **Solusi**: Implementasi refresh token sudah ada, pastikan berjalan dengan benar

### 4. **Konfigurasi Environment Berbeda**
- **Masalah**: Konfigurasi Spotify di hosting berbeda dengan development
- **Gejala**: Credentials tidak valid atau redirect URI salah
- **Solusi**: Periksa file `.env` di hosting

### 5. **HTTPS Requirement**
- **Masalah**: Spotify Web API memerlukan HTTPS untuk beberapa endpoint
- **Gejala**: Request ditolak atau tidak berfungsi
- **Solusi**: Pastikan website menggunakan HTTPS

## 🛠️ Langkah Debugging

### 1. Gunakan Debug Controller
```bash
# Akses endpoint debug (hanya di development)
GET /debug/spotify/
GET /debug/spotify/test-playback
```

### 2. Periksa Log Laravel
```bash
# Di hosting, periksa log file
tail -f storage/logs/laravel.log | grep Spotify
```

### 3. Test Manual di Browser
```javascript
// Buka Developer Console dan test:
fetch('/music/play', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        track_uri: 'spotify:track:4iV5W9uYEdYUVa79Axb7Rh'
    })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error(error));
```

## 🔧 Solusi Implementasi

### 1. Implementasi Spotify Web Playback SDK

Tambahkan ke layout atau halaman music:

```html
<!-- Spotify Web Playback SDK -->
<script src="https://sdk.scdn.co/spotify-player.js"></script>
<script>
window.onSpotifyWebPlaybackSDKReady = () => {
    const token = '{{ auth()->user()->spotify_access_token ?? "" }}';
    
    if (!token) return;
    
    const player = new Spotify.Player({
        name: 'Khairun Web Player',
        getOAuthToken: cb => { cb(token); },
        volume: 0.5
    });

    // Ready
    player.addListener('ready', ({ device_id }) => {
        console.log('Ready with Device ID', device_id);
        window.spotifyDeviceId = device_id;
    });

    // Not Ready
    player.addListener('not_ready', ({ device_id }) => {
        console.log('Device ID has gone offline', device_id);
    });

    // Connect to the player!
    player.connect();
};
</script>
```

### 2. Update SpotifyService untuk Menggunakan Device ID

Tambahkan method di `SpotifyService.php`:

```php
/**
 * Start playback with specific device
 */
public function startPlaybackWithDevice($user, $deviceId, $options = [])
{
    if ($this->mockMode || !$user->hasSpotifyConnection()) {
        return ['success' => true, 'mock' => true];
    }

    try {
        $accessToken = $this->getUserAccessToken($user);
        
        $url = 'https://api.spotify.com/v1/me/player/play';
        if ($deviceId) {
            $url .= '?device_id=' . $deviceId;
        }
        
        $response = Http::withToken($accessToken)
                      ->put($url, $options);

        return [
            'success' => $response->successful(),
            'status_code' => $response->status(),
            'error' => $response->successful() ? null : $response->body()
        ];
    } catch (\Exception $e) {
        Log::error('Spotify start playback with device failed', [
            'error' => $e->getMessage(),
            'device_id' => $deviceId
        ]);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

### 3. Update JavaScript untuk Menggunakan Device ID

```javascript
window.playSpotifyTrack = function(trackId, trackName, artistName) {
    const spotifyUri = `spotify:track:${trackId}`;
    const deviceId = window.spotifyDeviceId; // Dari Web Playback SDK
    
    const requestBody = {
        track_uri: spotifyUri
    };
    
    if (deviceId) {
        requestBody.device_id = deviceId;
    }
    
    fetch('{{ route('music.play') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('🎵 Playing on Spotify!', 'success');
        } else {
            console.error('Playback failed:', data);
            showNotification('❌ ' + (data.error || 'Failed to play track'), 'error');
        }
    })
    .catch(error => {
        console.error('Playback failed:', error);
        showNotification('❌ Failed to start playback', 'error');
    });
};
```

## 📋 Checklist untuk Hosting

### Environment Configuration
- [ ] `SPOTIFY_CLIENT_ID` sudah diset dengan benar
- [ ] `SPOTIFY_CLIENT_SECRET` sudah diset dengan benar
- [ ] `SPOTIFY_REDIRECT_URI` sesuai dengan domain hosting
- [ ] Website menggunakan HTTPS
- [ ] Domain sudah didaftarkan di Spotify App Settings

### Database
- [ ] User memiliki `spotify_access_token` yang valid
- [ ] User memiliki `spotify_refresh_token`
- [ ] `spotify_token_expires_at` belum expired

### User Requirements
- [ ] User menggunakan akun Spotify Premium
- [ ] User memiliki Spotify app yang terbuka di device
- [ ] Atau website mengimplementasikan Spotify Web Playback SDK

## 🚨 Error Messages dan Solusi

| Error Message | Penyebab | Solusi |
|---------------|----------|--------|
| "No active device found" | Tidak ada Spotify device yang aktif | Buka Spotify app atau implementasi Web Playback SDK |
| "Premium required" | User bukan Premium | User harus upgrade ke Premium |
| "Invalid access token" | Token expired/invalid | Refresh token otomatis atau re-authenticate |
| "Forbidden" | Insufficient scope | Periksa scope saat OAuth |
| "Player command failed" | Device tidak mendukung | Gunakan device yang kompatibel |

## 📞 Testing Commands

```bash
# Test di development
php artisan serve
# Akses: http://localhost:8000/debug/spotify/

# Test di hosting
curl -H "Authorization: Bearer YOUR_TOKEN" https://yourdomain.com/debug/spotify/
```

## 💡 Rekomendasi

1. **Implementasikan Spotify Web Playback SDK** untuk pengalaman yang lebih baik
2. **Tambahkan error handling** yang lebih detail di frontend
3. **Buat fallback** ke preview URL jika playback gagal
4. **Tambahkan instruksi** untuk user tentang requirement Premium
5. **Monitor logs** secara berkala untuk error patterns

---

**Note**: File ini dibuat untuk membantu debugging masalah Spotify playback di hosting environment. Pastikan untuk menghapus debug routes di production.