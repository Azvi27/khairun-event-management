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