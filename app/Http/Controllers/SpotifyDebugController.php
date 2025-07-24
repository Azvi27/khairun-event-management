<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpotifyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SpotifyDebugController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * Debug Spotify connection and playback issues
     */
    public function debug(Request $request)
    {
        $user = auth()->user();
        $debugInfo = [];
        
        // 1. Check user Spotify connection
        $debugInfo['user_spotify_connection'] = [
            'has_connection' => $user->hasSpotifyConnection(),
            'access_token_exists' => !empty($user->spotify_access_token),
            'refresh_token_exists' => !empty($user->spotify_refresh_token),
            'token_expires_at' => $user->spotify_token_expires_at,
            'needs_refresh' => $user->needsSpotifyTokenRefresh(),
        ];
        
        // 2. Check Spotify API credentials
        $debugInfo['spotify_config'] = [
            'client_id_exists' => !empty(config('services.spotify.client_id')),
            'client_secret_exists' => !empty(config('services.spotify.client_secret')),
            'redirect_uri' => config('services.spotify.redirect'),
        ];
        
        // 3. Test token validity
        if ($user->hasSpotifyConnection()) {
            try {
                $accessToken = $this->getUserAccessToken($user);
                
                // Test API call to get user profile
                $response = Http::withToken($accessToken)
                              ->get('https://api.spotify.com/v1/me');
                              
                $debugInfo['token_test'] = [
                    'status_code' => $response->status(),
                    'success' => $response->successful(),
                    'error' => $response->successful() ? null : $response->body(),
                ];
                
                // Test getting available devices
                $devicesResponse = Http::withToken($accessToken)
                                     ->get('https://api.spotify.com/v1/me/player/devices');
                                     
                $debugInfo['devices_test'] = [
                    'status_code' => $devicesResponse->status(),
                    'success' => $devicesResponse->successful(),
                    'devices' => $devicesResponse->successful() ? $devicesResponse->json() : null,
                    'error' => $devicesResponse->successful() ? null : $devicesResponse->body(),
                ];
                
                // Test current playback state
                $playbackResponse = Http::withToken($accessToken)
                                      ->get('https://api.spotify.com/v1/me/player');
                                      
                $debugInfo['playback_test'] = [
                    'status_code' => $playbackResponse->status(),
                    'success' => $playbackResponse->successful(),
                    'has_active_device' => $playbackResponse->successful() && !empty($playbackResponse->body()),
                    'error' => $playbackResponse->successful() ? null : $playbackResponse->body(),
                ];
                
            } catch (\Exception $e) {
                $debugInfo['token_test'] = [
                    'error' => $e->getMessage(),
                    'success' => false,
                ];
            }
        }
        
        // 4. Environment checks
        $debugInfo['environment'] = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'https_enabled' => $request->secure(),
            'user_agent' => $request->userAgent(),
        ];
        
        // 5. Recent logs check
        $debugInfo['recent_errors'] = $this->getRecentSpotifyErrors();
        
        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Test specific playback functionality
     */
    public function testPlayback(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        
        $testResults = [];
        
        try {
            $accessToken = $this->getUserAccessToken($user);
            
            // Test 1: Get available devices
            $devicesResponse = Http::withToken($accessToken)
                                 ->get('https://api.spotify.com/v1/me/player/devices');
                                 
            $testResults['devices'] = [
                'status_code' => $devicesResponse->status(),
                'success' => $devicesResponse->successful(),
                'data' => $devicesResponse->successful() ? $devicesResponse->json() : null,
                'error' => $devicesResponse->successful() ? null : $devicesResponse->body(),
            ];
            
            // Test 2: Try to start playback with a simple track
            $testTrackUri = 'spotify:track:4iV5W9uYEdYUVa79Axb7Rh'; // Never Gonna Give You Up
            
            $playResponse = Http::withToken($accessToken)
                              ->put('https://api.spotify.com/v1/me/player/play', [
                                  'uris' => [$testTrackUri]
                              ]);
                              
            $testResults['playback_test'] = [
                'status_code' => $playResponse->status(),
                'success' => $playResponse->successful(),
                'error' => $playResponse->successful() ? null : $playResponse->body(),
                'test_track' => $testTrackUri,
            ];
            
            // Test 3: Check if playback started
            sleep(2); // Wait a moment
            $currentResponse = Http::withToken($accessToken)
                                 ->get('https://api.spotify.com/v1/me/player');
                                 
            $testResults['current_playback'] = [
                'status_code' => $currentResponse->status(),
                'success' => $currentResponse->successful(),
                'data' => $currentResponse->successful() ? $currentResponse->json() : null,
                'error' => $currentResponse->successful() ? null : $currentResponse->body(),
            ];
            
        } catch (\Exception $e) {
            $testResults['exception'] = [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }
        
        return response()->json($testResults, 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Get user access token (same as SpotifyService)
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
     * Refresh user token (same as SpotifyService)
     */
    protected function refreshUserToken($user)
    {
        if (!$user->spotify_refresh_token) {
            throw new \Exception('No refresh token available for user');
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
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
    
    /**
     * Get recent Spotify-related errors from logs
     */
    protected function getRecentSpotifyErrors()
    {
        // This is a simplified version - in production you might want to read actual log files
        return [
            'note' => 'Check Laravel logs for detailed error messages',
            'common_issues' => [
                'No active device found',
                'Premium required',
                'Token expired',
                'Invalid client credentials',
                'User not found',
                'Forbidden - insufficient scope'
            ]
        ];
    }
}