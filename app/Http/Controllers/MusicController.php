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

        return view('music.index', compact('recommendedTracks', 'memoryTracks', 'eventTracks', 'isMockMode'));
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
}