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
        
        if ($hasSpotifyConnection) {
            $userPlaylists = $this->spotifyService->getUserPlaylists($user, 10);
            $currentPlayback = $this->spotifyService->getCurrentPlayback($user);
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
            'hasSpotifyConnection'
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
            return response()->json(['error' => 'Spotify not connected'], 401);
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

        $result = $this->spotifyService->startPlayback($user, $options);

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
}