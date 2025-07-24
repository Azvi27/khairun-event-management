<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpotifyService;
use App\Models\Memory;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class MusicController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function index()
    {
        $user = Auth::user();
        $userPlaylists = [];
        $currentPlayback = null;
        $hasSpotifyConnection = $user->hasSpotifyConnection();
        
        // =================================================================
        // PERBAIKAN KRUSIAL: Ambil Access Token yang valid untuk dikirim ke View
        // =================================================================
        $spotifyToken = null;
        if ($hasSpotifyConnection) {
            try {
                // Method ini akan otomatis me-refresh token jika akan expired
                $spotifyToken = $this->spotifyService->getUserAccessToken($user);
                $userPlaylists = $this->spotifyService->getUserPlaylists($user, 10);
                $currentPlayback = $this->spotifyService->getCurrentPlayback($user);
            } catch (\Exception $e) {
                // Jika gagal (misal. refresh token gagal), anggap tidak terkoneksi
                $hasSpotifyConnection = false;
            }
        }
        
        $recommendedTracks = $this->spotifyService->getRecommendedTracks(12);
        
        $memoryTracks = Memory::where('user_id', $user->id)
                              ->whereNotNull('spotify_track_id')
                              ->latest()
                              ->take(6)
                              ->get()
                              ->map(fn($memory) => $this->spotifyService->getTrack($memory->spotify_track_id))
                              ->filter();

        $eventTracks = Event::whereHas('users', fn($q) => $q->where('user_id', $user->id))
                              ->whereNotNull('spotify_track_id')
                              ->latest()
                              ->take(6)
                              ->get()
                              ->map(fn($event) => $this->spotifyService->getTrack($event->spotify_track_id))
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
            'spotifyToken' // <-- KIRIM TOKEN YANG BENAR KE VIEW
        ));
    }

    public function play(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['success' => false, 'error' => 'Spotify not connected'], 401);
        }

        $options = [];
        // Spotify API mengharapkan 'uris' sebagai array, bukan 'track_uri'
        if ($request->has('track_uri')) {
            $options['uris'] = [$request->input('track_uri')];
        }
        if ($request->has('context_uri')) {
            $options['context_uri'] = $request->input('context_uri');
        }
        
        $deviceId = $request->input('device_id');

        $result = $this->spotifyService->startPlayback($user, $options, $deviceId);
        
        if (!$result['success']) {
            $errorData = is_array($result['error']) ? $result['error'] : json_decode($result['error'], true);
            $errorMessage = $errorData['error']['message'] ?? 'Unknown playback error';
            $errorCode = $errorData['error']['reason'] ?? 'PLAYBACK_FAILED';
            
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'error_code' => $errorCode,
            ], $result['status_code'] ?? 500);
        }

        return response()->json($result);
    }

    // ... Sisa fungsi (search, pause, next, dll) tidak perlu diubah ...
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

    public function track($trackId)
    {
        $track = $this->spotifyService->getTrack($trackId);
        if (!$track) {
            return response()->json(['error' => 'Track not found'], 404);
        }
        return response()->json(['track' => $track]);
    }

    public function playlists()
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $playlists = $this->spotifyService->getUserPlaylists($user);
        return response()->json(['playlists' => $playlists]);
    }

    public function playlistTracks($playlistId)
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $tracks = $this->spotifyService->getPlaylistTracks($user, $playlistId);
        return response()->json(['tracks' => $tracks]);
    }

    public function pause()
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $result = $this->spotifyService->pausePlayback($user);
        return response()->json($result);
    }

    public function next()
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $result = $this->spotifyService->nextTrack($user);
        return response()->json($result);
    }

    public function previous()
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $result = $this->spotifyService->previousTrack($user);
        return response()->json($result);
    }

    public function currentPlayback()
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['error' => 'Spotify not connected'], 401);
        }
        $playback = $this->spotifyService->getCurrentPlayback($user);
        return response()->json(['playback' => $playback]);
    }
    
    public function transferPlayback(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasSpotifyConnection()) {
            return response()->json(['success' => false, 'error' => 'Spotify not connected'], 401);
        }
        $deviceId = $request->input('device_id');
        if (!$deviceId) {
            return response()->json(['success' => false, 'error' => 'Device ID is required'], 400);
        }
        $result = $this->spotifyService->transferPlayback($user, $deviceId);
        return response()->json($result);
    }
}