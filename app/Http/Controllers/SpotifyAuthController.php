<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class SpotifyAuthController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $authUrl = 'https://accounts.spotify.com/authorize';
    protected $tokenUrl = 'https://accounts.spotify.com/api/token';

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        $this->redirectUri = env('SPOTIFY_REDIRECT_URI', route('spotify.callback'));
    }

    /**
     * Redirect user to Spotify OAuth
     */
    public function redirectToSpotify()
    {
        if (empty($this->clientId)) {
            return redirect()->route('music.index')
                           ->with('error', 'Spotify authentication is not configured');
        }

        $state = Str::random(16);
        session([
            'spotify_state' => $state,
            'spotify_user_id' => Auth::id() // Simpan user ID
        ]);

        $scopes = [
            'streaming',           // Web Playback SDK
            'user-read-email',     // User email
            'user-read-private',   // User profile
            'user-read-playback-state',     // Current playback
            'user-modify-playback-state',   // Control playback
            'user-read-currently-playing',  // Currently playing
            'playlist-read-private',        // Private playlists
            'playlist-read-collaborative',  // Collaborative playlists
        ];

        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $scopes),
            'state' => $state,
            'show_dialog' => 'true', // Force approval dialog
        ];

        $authUrl = $this->authUrl . '?' . http_build_query($params);

        return redirect($authUrl);
    }

    /**
     * Handle Spotify OAuth callback
     */
    public function handleCallback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        $error = $request->get('error');

        // Check for errors
        if ($error) {
            return redirect()->route('music.index')
                           ->with('error', 'Spotify authentication failed: ' . $error);
        }

        // Verify state parameter
        if (!$state || $state !== session('spotify_state')) {
            return redirect()->route('music.index')
                           ->with('error', 'Invalid state parameter. Please try again.');
        }

        if (!$code) {
            return redirect()->route('music.index')
                           ->with('error', 'Authorization code not received from Spotify');
        }

        try {
            // Exchange code for token
            $tokenResponse = Http::asForm()->post($this->tokenUrl, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Failed to exchange code for token: ' . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();

            // Get user info from Spotify
            $userResponse = Http::withToken($tokenData['access_token'])
                              ->get('https://api.spotify.com/v1/me');

            if (!$userResponse->successful()) {
                throw new \Exception('Failed to get user info from Spotify');
            }

            $spotifyUser = $userResponse->json();

            // Get user ID from session instead of Auth::user()
            $userId = session('spotify_user_id');
            if (!$userId) {
                return redirect()->route('login')
                               ->with('error', 'Session expired. Please login and try again.');
            }

            // Update user with Spotify data using user ID from session
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('login')
                               ->with('error', 'User not found. Please login again.');
            }

            $user->update([
                'spotify_id' => $spotifyUser['id'],
                'spotify_access_token' => $tokenData['access_token'],
                'spotify_refresh_token' => $tokenData['refresh_token'],
                'spotify_token_expires_at' => now()->addSeconds($tokenData['expires_in'] - 60),
                'spotify_user_data' => $spotifyUser,
            ]);

            // Clear session data
            session()->forget(['spotify_state', 'spotify_user_id']);

            // Login the user and redirect
            Auth::login($user);

            return redirect()->route('music.index')
                           ->with('success', 'Successfully connected to Spotify! You can now play music directly.');

        } catch (\Exception $e) {
            \Log::error('Spotify OAuth error: ' . $e->getMessage());
            
            return redirect()->route('music.index')
                           ->with('error', 'Failed to connect to Spotify. Please try again.');
        }
    }

    /**
     * Disconnect Spotify account
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'spotify_id' => null,
            'spotify_access_token' => null,
            'spotify_refresh_token' => null,
            'spotify_token_expires_at' => null,
            'spotify_user_data' => null,
        ]);

        return redirect()->route('music.index')
                       ->with('success', 'Spotify account disconnected successfully.');
    }

    /**
     * Get current Spotify connection status
     */
    public function status()
    {
        $user = Auth::user();
        
        $isConnected = !empty($user->spotify_access_token) && 
                      (!$user->spotify_token_expires_at || $user->spotify_token_expires_at > now());

        $spotifyData = null;
        if ($isConnected && $user->spotify_user_data) {
            $spotifyData = [
                'id' => $user->spotify_user_data['id'] ?? null,
                'name' => $user->spotify_user_data['display_name'] ?? 'Unknown',
                'email' => $user->spotify_user_data['email'] ?? null,
                'image' => $user->spotify_user_data['images'][0]['url'] ?? null,
                'followers' => $user->spotify_user_data['followers']['total'] ?? 0,
                'country' => $user->spotify_user_data['country'] ?? null,
            ];
        }

        return response()->json([
            'connected' => $isConnected,
            'user' => $spotifyData,
            'token_expires_at' => $user->spotify_token_expires_at,
        ]);
    }

    /**
     * Refresh Spotify token
     */
    public function refreshToken()
    {
        $user = Auth::user();

        if (!$user->spotify_refresh_token) {
            return response()->json(['error' => 'No refresh token available'], 400);
        }

        try {
            $response = Http::asForm()->post($this->tokenUrl, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $user->spotify_refresh_token,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to refresh token: ' . $response->body());
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

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Spotify token refresh error: ' . $e->getMessage());
            
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }
    }
}
