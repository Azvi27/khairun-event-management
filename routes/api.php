<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\SpotifyService;

// Spotify API Routes
Route::middleware('auth')->group(function () {
    // Search tracks
    Route::get('/spotify/search', function (Request $request) {
        $query = $request->get('q');
        $limit = $request->get('limit', 10);
        
        if (!$query) {
            return response()->json(['error' => 'Query parameter required'], 400);
        }
        
        $spotifyService = new SpotifyService();
        $tracks = $spotifyService->searchTracks($query, $limit);
        
        return response()->json([
            'success' => true,
            'tracks' => $tracks
        ]);
    })->name('api.spotify.search');
    
    // Get track details
    Route::get('/spotify/track/{trackId}', function ($trackId) {
        $spotifyService = new SpotifyService();
        $track = $spotifyService->getTrack($trackId);
        
        if (!$track) {
            return response()->json(['error' => 'Track not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'track' => $track
        ]);
    })->name('api.spotify.track');
});