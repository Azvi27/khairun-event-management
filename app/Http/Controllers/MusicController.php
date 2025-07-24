<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpotifyService;
use App\Models\Memory;
use App\Models\Event;

class MusicController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * Halaman musik utama
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's Spotify playlists if connected
        $userPlaylists = [];
        $currentPlayback = null;
        $hasSpotifyConnection = $user->hasSpotifyConnection();
        $spotifyAccessToken = null;
        
        if ($hasSpotifyConnection) {
            $userPlaylists = $this->spotifyService->getUserPlaylists($user, 10);
            $currentPlayback = $this->spotifyService->getCurrentPlayback($user);
            $spotifyAccessToken = $user->spotify_access_token;
        }
        
        // Get recommended tracks
        $recommendedTracks = $this->spotifyService->getRecommendedTracks(12);
        
        // Get tracks dari memories
        $memoryTracks = Memory::where('user_id', auth()->id())
                              ->whereNotNull('spotify_track_id')
                              ->latest()
                              ->take(6)
                              ->get()
                              ->map(function($memory) {
                                  $track = $this->spotifyService->getTrack($memory->spotify_track_id);
                                  return $track ? array_merge($track, ['memory' => $memory]) : null;
                              })
                              ->filter();

        // Get tracks dari events
        $eventTracks = Event::whereHas('users', function($query) {
                                $query->where('user_id', auth()->id());
                              })
                              ->whereNotNull('spotify_track_id')
                              ->latest()
                              ->take(6)
                              ->get()
                              ->map(function($event) {
                                  $track = $this->spotifyService->getTrack($event->spotify_track_id);
                                  return $track ? array_merge($track, ['event' => $event]) : null;
                              })
                              ->filter();

        $isMockMode = $this->spotifyService->isMockMode();

        return view('music.index', compact(
            'recommendedTracks', 
            'memoryTracks', 
            'eventTracks', 
            'isMockMode',
            'userPlaylists',
            'currentPlayback',
            'hasSpotifyConnection',
            'spotifyAccessToken'
        ));
    }

    /**
     * Search musik
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json(['tracks' => []]);
        }

        $tracks = $this->spotifyService->searchTracks($query, $limit);

        return response()->json(['tracks' => $tracks]);
    }

    /**
     * Get track detail
     */
    public function track($trackId)
    {
        $track = $this->spotifyService->getTrack($trackId);

        if (!$track) {
            return response()->json(['error' => 'Track not found'], 404);
        }

        return response()->json(['track' => $track]);
    }

    /**
     * Get user's playlists
     */
    public function playlists()
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $playlists = $this->spotifyService->getUserPlaylists($user);

        return response()->json(['playlists' => $playlists]);
    }

    /**
     * Get playlist tracks
     */
    public function playlistTracks($playlistId)
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $tracks = $this->spotifyService->getPlaylistTracks($user, $playlistId);

        return response()->json(['tracks' => $tracks]);
    }

    /**
     * Start/Resume playback
     */
    public function play(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json([
                'success' => false,
                'error' => 'Spotify not connected',
                'error_code' => 'NO_CONNECTION'
            ], 401);
        }

        $options = [];
        
        // Play specific track
        if ($request->has('track_uri')) {
            $options['uris'] = [$request->input('track_uri')];
        }
        
        // Play from playlist
        if ($request->has('context_uri')) {
            $options['context_uri'] = $request->input('context_uri');
            
            if ($request->has('offset')) {
                $options['offset'] = ['position' => (int)$request->input('offset')];
            }
        }
        
        // Add device ID if provided
        $deviceId = $request->input('device_id');

        $result = $this->spotifyService->startPlayback($user, $options, $deviceId);
        
        // Enhanced error handling
        if (!$result['success']) {
            $errorMessage = $result['error'] ?? 'Unknown error';
            $errorCode = 'PLAYBACK_FAILED';
            
            // Parse specific error types
            if (strpos($errorMessage, 'No active device') !== false) {
                $errorCode = 'NO_ACTIVE_DEVICE';
                $errorMessage = 'No active Spotify device found. Please open Spotify on any device first.';
            } elseif (strpos($errorMessage, 'Premium required') !== false) {
                $errorCode = 'PREMIUM_REQUIRED';
                $errorMessage = 'Spotify Premium is required for playback control.';
            } elseif (strpos($errorMessage, 'Device not found') !== false) {
                $errorCode = 'DEVICE_NOT_FOUND';
                $errorMessage = 'Spotify device not found. Please ensure Spotify is running.';
            }
            
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'status_code' => $result['status_code'] ?? null
            ]);
        }

        return response()->json($result);
    }

    /**
     * Pause playback
     */
    public function pause()
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $result = $this->spotifyService->pausePlayback($user);

        return response()->json($result);
    }

    /**
     * Skip to next track
     */
    public function next()
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $result = $this->spotifyService->nextTrack($user);

        return response()->json($result);
    }

    /**
     * Skip to previous track
     */
    public function previous()
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $result = $this->spotifyService->previousTrack($user);

        return response()->json($result);
    }

    /**
     * Get current playback state
     */
    public function currentPlayback()
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }

        $playback = $this->spotifyService->getCurrentPlayback($user);

        return response()->json(['playback' => $playback]);
    }
    
    /**
     * Transfer playback to specific device
     */
    public function transferPlayback(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json([
                'success' => false,
                'error' => 'Spotify not connected'
            ], 401);
        }
        
        $deviceId = $request->input('device_id');
        
        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'error' => 'Device ID is required'
            ], 400);
        }
        
        $result = $this->spotifyService->transferPlayback($user, $deviceId);
        
        return response()->json($result);
    }
}