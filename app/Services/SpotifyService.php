<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SpotifyService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://api.spotify.com/v1';
    protected $accountsUrl = 'https://accounts.spotify.com/api';
    protected $mockMode = false; // ✅ TAMBAHKAN INI

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        
        // ✅ TAMBAHKAN: Enable mock mode jika credentials tidak ada
        $this->mockMode = empty($this->clientId) || empty($this->clientSecret) || app()->environment('local');
    }

    /**
     * ✅ TAMBAHKAN: Mock data untuk development
     */
    protected function getMockTracks($query = '', $limit = 10)
    {
        $mockData = [
            [
                'id' => 'mock_001',
                'name' => 'Perfect',
                'artist' => 'Ed Sheeran',
                'album' => '÷ (Divide)',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273ba5db46f4b838ef6027e6f96',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/0tgVpDi06FyKpA1z0VMD4v',
                'duration_ms' => 263400,
            ],
            [
                'id' => 'mock_002',
                'name' => 'Hati-Hati di Jalan',
                'artist' => 'Tulus',
                'album' => 'Monokrom',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273d5ac8cdb4f7c5f8c5f7b8c1e',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/1a2B3c4D5e6F7g8H9i0J',
                'duration_ms' => 245000,
            ],
            [
                'id' => 'mock_003',
                'name' => 'Right Here Waiting',
                'artist' => 'Richard Marx',
                'album' => 'Repeat Offender',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273f2d2e1c1b8e9f2a2c3d4e5f6',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/2B3c4D5e6F7g8H9i0J1K',
                'duration_ms' => 285000,
            ],
            [
                'id' => 'mock_004',
                'name' => 'Melukis Senja',
                'artist' => 'Budi Doremi',
                'album' => 'Celengan Rindu',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273a1b2c3d4e5f6g7h8i9j0k1l2',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/3C4d5E6f7G8h9I0j1K2L',
                'duration_ms' => 267000,
            ],
            [
                'id' => 'mock_005',
                'name' => 'Thinking Out Loud',
                'artist' => 'Ed Sheeran',
                'album' => 'x (Multiply)',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273b1c2d3e4f5g6h7i8j9k0l1m2',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/4D5e6F7g8H9i0J1k2L3M',
                'duration_ms' => 281000,
            ],
            [
                'id' => 'mock_006',
                'name' => 'All of Me',
                'artist' => 'John Legend',
                'album' => 'Love in the Future',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273c2d3e4f5g6h7i8j9k0l1m2n3',
                'preview_url' => null,
                'external_url' => 'https://open.spotify.com/track/5E6f7G8h9I0j1K2l3M4N',
                'duration_ms' => 269000,
            ]
        ];

        // Filter berdasarkan query jika ada
        if (!empty($query)) {
            $mockData = array_filter($mockData, function($track) use ($query) {
                return stripos($track['name'], $query) !== false || 
                       stripos($track['artist'], $query) !== false;
            });
        }

        return array_slice($mockData, 0, $limit);
    }

    /**
     * Search tracks for Khairun memories
     */
    public function searchTracks($query, $limit = 10)
    {
        // ✅ TAMBAHKAN: Return mock data untuk development
        if ($this->mockMode) {
            return $this->getMockTracks($query, $limit);
        }

        try {
            $response = $this->makeRequest('get', '/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $tracks = $response->json()['tracks']['items'];
                
                // Format for Khairun usage
                return collect($tracks)->map(function ($track) {
                    return [
                        'id' => $track['id'],
                        'name' => $track['name'],
                        'artist' => $track['artists'][0]['name'] ?? 'Unknown Artist',
                        'album' => $track['album']['name'] ?? 'Unknown Album',
                        'image' => $track['album']['images'][0]['url'] ?? null,
                        'preview_url' => $track['preview_url'],
                        'external_url' => $track['external_urls']['spotify'] ?? null,
                        'duration_ms' => $track['duration_ms'],
                    ];
                })->toArray();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Spotify search failed', ['error' => $e->getMessage(), 'query' => $query]);
            // Fallback ke mock data jika API gagal
            return $this->getMockTracks($query, $limit);
        }
    }

    /**
     * Get track details by ID
     */
    public function getTrack($trackId)
    {
        // ✅ TAMBAHKAN: Return mock data untuk development
        if ($this->mockMode) {
            $mockTracks = $this->getMockTracks();
            return collect($mockTracks)->firstWhere('id', $trackId);
        }

        try {
            $response = $this->makeRequest('get', "/tracks/{$trackId}");

            if ($response->successful()) {
                $track = $response->json();
                
                return [
                    'id' => $track['id'],
                    'name' => $track['name'],
                    'artist' => $track['artists'][0]['name'] ?? 'Unknown Artist',
                    'album' => $track['album']['name'] ?? 'Unknown Album',
                    'image' => $track['album']['images'][0]['url'] ?? null,
                    'preview_url' => $track['preview_url'],
                    'external_url' => $track['external_urls']['spotify'] ?? null,
                    'duration_ms' => $track['duration_ms'],
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Spotify track fetch failed', ['error' => $e->getMessage(), 'track_id' => $trackId]);
            // Fallback ke mock data
            $mockTracks = $this->getMockTracks();
            return collect($mockTracks)->firstWhere('id', $trackId);
        }
    }

    // ✅ TAMBAHKAN: Method untuk get playlist/recommended tracks
    public function getRecommendedTracks($limit = 20)
    {
        if ($this->mockMode) {
            return $this->getMockTracks('', $limit);
        }

        // Untuk production nanti bisa implement recommendation logic
        return $this->searchTracks('love romantic', $limit);
    }

    // ✅ TAMBAHKAN: Method untuk check if dalam mock mode
    public function isMockMode()
    {
        return $this->mockMode;
    }

    /**
     * Get user's playlists
     */
    public function getUserPlaylists($user, $limit = 20)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return $this->getMockPlaylists();
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->get('https://api.spotify.com/v1/me/playlists', [
                              'limit' => $limit
                          ]);

            if ($response->successful()) {
                $playlists = $response->json()['items'];
                
                return collect($playlists)->map(function ($playlist) {
                    return [
                        'id' => $playlist['id'],
                        'name' => $playlist['name'],
                        'description' => $playlist['description'],
                        'image' => $playlist['images'][0]['url'] ?? null,
                        'tracks_total' => $playlist['tracks']['total'],
                        'public' => $playlist['public'],
                        'collaborative' => $playlist['collaborative'],
                        'external_url' => $playlist['external_urls']['spotify'] ?? null,
                        'owner' => $playlist['owner']['display_name'] ?? 'Unknown',
                    ];
                })->toArray();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Spotify user playlists failed', ['error' => $e->getMessage()]);
            return $this->getMockPlaylists();
        }
    }

    /**
     * Get playlist tracks
     */
    public function getPlaylistTracks($user, $playlistId, $limit = 50)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return $this->getMockTracks('', $limit);
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
                              'limit' => $limit,
                              'fields' => 'items(track(id,name,artists,album,duration_ms,external_urls,preview_url))'
                          ]);

            if ($response->successful()) {
                $items = $response->json()['items'];
                
                return collect($items)->map(function ($item) {
                    $track = $item['track'];
                    return [
                        'id' => $track['id'],
                        'name' => $track['name'],
                        'artist' => $track['artists'][0]['name'] ?? 'Unknown Artist',
                        'album' => $track['album']['name'] ?? 'Unknown Album',
                        'image' => $track['album']['images'][0]['url'] ?? null,
                        'preview_url' => $track['preview_url'],
                        'external_url' => $track['external_urls']['spotify'] ?? null,
                        'duration_ms' => $track['duration_ms'],
                    ];
                })->toArray();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Spotify playlist tracks failed', ['error' => $e->getMessage()]);
            return $this->getMockTracks('', $limit);
        }
    }

    /**
     * Start/Resume playback on user's active device
     */
    public function startPlayback($user, $options = [])
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return ['success' => true, 'mock' => true];
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->put('https://api.spotify.com/v1/me/player/play', $options);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'error' => $response->successful() ? null : $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Spotify start playback failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Pause playback on user's active device
     */
    public function pausePlayback($user)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return ['success' => true, 'mock' => true];
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->put('https://api.spotify.com/v1/me/player/pause');

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'error' => $response->successful() ? null : $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Spotify pause playback failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Skip to next track
     */
    public function nextTrack($user)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return ['success' => true, 'mock' => true];
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->post('https://api.spotify.com/v1/me/player/next');

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'error' => $response->successful() ? null : $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Spotify next track failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Skip to previous track
     */
    public function previousTrack($user)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return ['success' => true, 'mock' => true];
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->post('https://api.spotify.com/v1/me/player/previous');

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'error' => $response->successful() ? null : $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Spotify previous track failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get current playback state
     */
    public function getCurrentPlayback($user)
    {
        if ($this->mockMode || !$user->hasSpotifyConnection()) {
            return $this->getMockCurrentPlayback();
        }

        try {
            $accessToken = $this->getUserAccessToken($user);
            
            $response = Http::withToken($accessToken)
                          ->get('https://api.spotify.com/v1/me/player');

            if ($response->successful() && $response->body() !== '') {
                $data = $response->json();
                $track = $data['item'] ?? null;
                
                return [
                    'is_playing' => $data['is_playing'] ?? false,
                    'progress_ms' => $data['progress_ms'] ?? 0,
                    'device' => $data['device']['name'] ?? null,
                    'shuffle_state' => $data['shuffle_state'] ?? false,
                    'repeat_state' => $data['repeat_state'] ?? 'off',
                    'track' => $track ? [
                        'id' => $track['id'],
                        'name' => $track['name'],
                        'artist' => $track['artists'][0]['name'] ?? 'Unknown Artist',
                        'album' => $track['album']['name'] ?? 'Unknown Album',
                        'image' => $track['album']['images'][0]['url'] ?? null,
                        'duration_ms' => $track['duration_ms'],
                        'external_url' => $track['external_urls']['spotify'] ?? null,
                    ] : null
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Spotify get current playback failed', ['error' => $e->getMessage()]);
            return $this->getMockCurrentPlayback();
        }
    }

    /**
     * Mock playlists for development
     */
    protected function getMockPlaylists()
    {
        return [
            [
                'id' => 'mock_playlist_001',
                'name' => 'Our Love Songs',
                'description' => 'Perfect soundtrack for our memories together',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273ba5db46f4b838ef6027e6f96',
                'tracks_total' => 25,
                'public' => false,
                'collaborative' => false,
                'external_url' => 'https://open.spotify.com/playlist/mock_playlist_001',
                'owner' => 'You',
            ],
            [
                'id' => 'mock_playlist_002',
                'name' => 'Memories Soundtrack',
                'description' => 'Songs that remind us of special moments',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273d5ac8cdb4f7c5f8c5f7b8c1e',
                'tracks_total' => 18,
                'public' => false,
                'collaborative' => true,
                'external_url' => 'https://open.spotify.com/playlist/mock_playlist_002',
                'owner' => 'Both of us',
            ]
        ];
    }

    /**
     * Mock current playback for development
     */
    protected function getMockCurrentPlayback()
    {
        return [
            'is_playing' => true,
            'progress_ms' => 135000, // 2:15
            'device' => 'Khairun Web Player',
            'shuffle_state' => false,
            'repeat_state' => 'off',
            'track' => [
                'id' => 'mock_001',
                'name' => 'Perfect',
                'artist' => 'Ed Sheeran',
                'album' => '÷ (Divide)',
                'image' => 'https://i.scdn.co/image/ab67616d0000b273ba5db46f4b838ef6027e6f96',
                'duration_ms' => 263400,
                'external_url' => 'https://open.spotify.com/track/0tgVpDi06FyKpA1z0VMD4v',
            ]
        ];
    }

    /**
     * Get user's access token with automatic refresh
     */
    protected function getUserAccessToken($user)
    {
        if (!$user->hasSpotifyConnection()) {
            throw new \Exception('User does not have valid Spotify connection');
        }

        // Check if token needs refresh
        if ($user->needsSpotifyTokenRefresh()) {
            $this->refreshUserToken($user);
        }

        return $user->spotify_access_token;
    }

    /**
     * Refresh user's access token
     */
    protected function refreshUserToken($user)
    {
        if (!$user->spotify_refresh_token) {
            throw new \Exception('No refresh token available for user');
        }

        $response = Http::asForm()->post($this->accountsUrl . '/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh user token: ' . $response->body());
        }

        $tokenData = $response->json();

        $user->update([
            'spotify_access_token' => $tokenData['access_token'],
            'spotify_token_expires_at' => now()->addSeconds($tokenData['expires_in'] - 60),
        ]);

        // Update refresh token if provided
        if (isset($tokenData['refresh_token'])) {
            $user->update(['spotify_refresh_token' => $tokenData['refresh_token']]);
        }

        return $tokenData['access_token'];
    }

    // Sisa method tetap sama...
    protected function getAccessToken()
    {
        return Cache::remember('spotify_access_token', 3500, function () {
            $response = Http::asForm()->post($this->accountsUrl . '/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            Log::error('Spotify token request failed', ['response' => $response->body()]);
            return null;
        });
    }

    protected function makeRequest($method, $endpoint, $data = [])
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            throw new \Exception('Could not retrieve Spotify access token');
        }

        return Http::withToken($accessToken)->$method($this->baseUrl . $endpoint, $data);
    }
}