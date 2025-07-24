<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SpotifyService;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:health-check {--user-id= : Check specific user ID} {--fix : Attempt to fix common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Spotify integration health and diagnose common issues';

    protected $spotifyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SpotifyService $spotifyService)
    {
        parent::__construct();
        $this->spotifyService = $spotifyService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🎵 Spotify Health Check Started');
        $this->newLine();
        
        // 1. Check Spotify Configuration
        $this->checkSpotifyConfig();
        
        // 2. Check Users with Spotify Connection
        $this->checkSpotifyUsers();
        
        // 3. Test Spotify API Connection
        $this->testSpotifyAPI();
        
        // 4. Check specific user if provided
        if ($userId = $this->option('user-id')) {
            $this->checkSpecificUser($userId);
        }
        
        $this->newLine();
        $this->info('✅ Spotify Health Check Completed');
        
        return 0;
    }
    
    protected function checkSpotifyConfig()
    {
        $this->info('📋 Checking Spotify Configuration...');
        
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $redirectUri = config('services.spotify.redirect');
        
        if (empty($clientId)) {
            $this->error('❌ SPOTIFY_CLIENT_ID is not set');
        } else {
            $this->info('✅ SPOTIFY_CLIENT_ID is configured');
        }
        
        if (empty($clientSecret)) {
            $this->error('❌ SPOTIFY_CLIENT_SECRET is not set');
        } else {
            $this->info('✅ SPOTIFY_CLIENT_SECRET is configured');
        }
        
        if (empty($redirectUri)) {
            $this->error('❌ SPOTIFY_REDIRECT_URI is not set');
        } else {
            $this->info("✅ SPOTIFY_REDIRECT_URI: {$redirectUri}");
        }
        
        $this->newLine();
    }
    
    protected function checkSpotifyUsers()
    {
        $this->info('👥 Checking Users with Spotify Connection...');
        
        $usersWithSpotify = User::whereNotNull('spotify_access_token')->get();
        $totalUsers = User::count();
        $connectedUsers = $usersWithSpotify->count();
        
        $this->info("📊 Total Users: {$totalUsers}");
        $this->info("🔗 Connected to Spotify: {$connectedUsers}");
        
        if ($connectedUsers === 0) {
            $this->warn('⚠️  No users connected to Spotify');
            $this->newLine();
            return;
        }
        
        $expiredTokens = 0;
        $validTokens = 0;
        
        foreach ($usersWithSpotify as $user) {
            if ($user->needsSpotifyTokenRefresh()) {
                $expiredTokens++;
                
                if ($this->option('fix')) {
                    $this->info("🔄 Attempting to refresh token for user {$user->id}...");
                    try {
                        $this->refreshUserToken($user);
                        $this->info("✅ Token refreshed for user {$user->id}");
                        $validTokens++;
                    } catch (\Exception $e) {
                        $this->error("❌ Failed to refresh token for user {$user->id}: {$e->getMessage()}");
                    }
                } else {
                    $this->warn("⚠️  User {$user->id} ({$user->name}) has expired token");
                }
            } else {
                $validTokens++;
            }
        }
        
        $this->info("✅ Valid tokens: {$validTokens}");
        if ($expiredTokens > 0) {
            $this->warn("⚠️  Expired tokens: {$expiredTokens}");
            if (!$this->option('fix')) {
                $this->info('💡 Run with --fix to attempt token refresh');
            }
        }
        
        $this->newLine();
    }
    
    protected function testSpotifyAPI()
    {
        $this->info('🌐 Testing Spotify API Connection...');
        
        try {
            // Test client credentials flow
            $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.spotify.client_id'),
                'client_secret' => config('services.spotify.client_secret'),
            ]);
            
            if ($response->successful()) {
                $this->info('✅ Spotify API connection successful');
                
                $token = $response->json()['access_token'];
                
                // Test search API
                $searchResponse = Http::withToken($token)
                                    ->get('https://api.spotify.com/v1/search', [
                                        'q' => 'test',
                                        'type' => 'track',
                                        'limit' => 1
                                    ]);
                                    
                if ($searchResponse->successful()) {
                    $this->info('✅ Spotify Search API working');
                } else {
                    $this->error('❌ Spotify Search API failed: ' . $searchResponse->body());
                }
                
            } else {
                $this->error('❌ Spotify API connection failed: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Spotify API test failed: ' . $e->getMessage());
        }
        
        $this->newLine();
    }
    
    protected function checkSpecificUser($userId)
    {
        $this->info("🔍 Checking User ID: {$userId}");
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ User {$userId} not found");
            return;
        }
        
        $this->info("👤 User: {$user->name} ({$user->email})");
        
        if (!$user->hasSpotifyConnection()) {
            $this->warn('⚠️  User not connected to Spotify');
            return;
        }
        
        $this->info('✅ User has Spotify connection');
        
        // Check token validity
        if ($user->needsSpotifyTokenRefresh()) {
            $this->warn('⚠️  Token needs refresh');
            
            if ($this->option('fix')) {
                try {
                    $this->refreshUserToken($user);
                    $this->info('✅ Token refreshed successfully');
                } catch (\Exception $e) {
                    $this->error('❌ Token refresh failed: ' . $e->getMessage());
                    return;
                }
            }
        } else {
            $this->info('✅ Token is valid');
        }
        
        // Test user's Spotify API access
        try {
            $accessToken = $user->spotify_access_token;
            
            // Test user profile
            $profileResponse = Http::withToken($accessToken)
                                 ->get('https://api.spotify.com/v1/me');
                                 
            if ($profileResponse->successful()) {
                $profile = $profileResponse->json();
                $this->info("✅ User profile: {$profile['display_name']} (Premium: " . 
                           ($profile['product'] === 'premium' ? 'Yes' : 'No') . ")");
                           
                if ($profile['product'] !== 'premium') {
                    $this->warn('⚠️  User does not have Spotify Premium (required for playback)');
                }
            } else {
                $this->error('❌ Failed to get user profile: ' . $profileResponse->body());
            }
            
            // Test devices
            $devicesResponse = Http::withToken($accessToken)
                                 ->get('https://api.spotify.com/v1/me/player/devices');
                                 
            if ($devicesResponse->successful()) {
                $devices = $devicesResponse->json()['devices'];
                $this->info("📱 Available devices: " . count($devices));
                
                foreach ($devices as $device) {
                    $status = $device['is_active'] ? '🟢 Active' : '⚪ Inactive';
                    $this->info("   - {$device['name']} ({$device['type']}) {$status}");
                }
                
                if (empty($devices)) {
                    $this->warn('⚠️  No Spotify devices found (user needs to open Spotify app)');
                }
            } else {
                $this->error('❌ Failed to get devices: ' . $devicesResponse->body());
            }
            
            // Test current playback
            $playbackResponse = Http::withToken($accessToken)
                                  ->get('https://api.spotify.com/v1/me/player');
                                  
            if ($playbackResponse->successful() && !empty($playbackResponse->body())) {
                $playback = $playbackResponse->json();
                $track = $playback['item'];
                $this->info("🎵 Currently playing: {$track['name']} by {$track['artists'][0]['name']}");
            } else {
                $this->info('⏸️  No active playback');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ User API test failed: ' . $e->getMessage());
        }
        
        $this->newLine();
    }
    
    protected function refreshUserToken($user)
    {
        if (!$user->spotify_refresh_token) {
            throw new \Exception('No refresh token available');
        }

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh token: ' . $response->body());
        }

        $tokenData = $response->json();

        $user->update([
            'spotify_access_token' => $tokenData['access_token'],
            'spotify_token_expires_at' => now()->addSeconds($tokenData['expires_in'] - 60),
        ]);

        if (isset($tokenData['refresh_token'])) {
            $user->update(['spotify_refresh_token' => $tokenData['refresh_token']]);
        }
    }
}