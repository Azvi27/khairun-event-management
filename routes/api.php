<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\SpotifyService;
use App\Http\Controllers\Api\MemoryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\BirthdaySurpriseController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
});

// Protected routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    
    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
        Route::get('/statistics', [UserController::class, 'statistics']);
    });
    
    // Memory routes
    Route::apiResource('memories', MemoryController::class);
    Route::prefix('memories')->group(function () {
        Route::get('/calendar', [MemoryController::class, 'calendar']);
        Route::get('/search', [MemoryController::class, 'search']);
        Route::get('/statistics', [MemoryController::class, 'statistics']);
    });
    
    // Event routes
    Route::apiResource('events', EventController::class);
    Route::prefix('events')->group(function () {
        Route::get('/calendar', [EventController::class, 'calendar']);
        Route::get('/upcoming', [EventController::class, 'upcoming']);
        Route::post('/{event}/join', [EventController::class, 'join']);
        Route::delete('/{event}/leave', [EventController::class, 'leave']);
    });
    
    // Birthday Surprise routes
    Route::prefix('birthday-surprises')->group(function () {
        Route::get('/', [BirthdaySurpriseController::class, 'index']);
        Route::post('/', [BirthdaySurpriseController::class, 'store']);
        Route::get('/{surprise}', [BirthdaySurpriseController::class, 'show']);
        Route::put('/{surprise}', [BirthdaySurpriseController::class, 'update']);
        Route::delete('/{surprise}', [BirthdaySurpriseController::class, 'destroy']);
        Route::post('/{surprise}/trigger', [BirthdaySurpriseController::class, 'trigger']);
        Route::post('/test-trigger', [BirthdaySurpriseController::class, 'testTrigger']);
    });
    
    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
    });
    
    // Spotify API Routes
    Route::prefix('spotify')->group(function () {
        // Search tracks
        Route::get('/search', function (Request $request) {
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
        Route::get('/track/{trackId}', function ($trackId) {
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
        
        // Get user's playlists (if authenticated with Spotify)
        Route::get('/playlists', function (Request $request) {
            $spotifyService = new SpotifyService();
            $playlists = $spotifyService->getUserPlaylists($request->user());
            
            return response()->json([
                'success' => true,
                'playlists' => $playlists
            ]);
        })->name('api.spotify.playlists');
    });
    
    // File upload routes
    Route::prefix('upload')->group(function () {
        Route::post('/image', function (Request $request) {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            ]);
            
            $path = $request->file('image')->store('temp', 'public');
            
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        })->name('api.upload.image');
    });
    
    // Theme routes
    Route::prefix('theme')->group(function () {
        Route::get('/current', function (Request $request) {
            $themeService = app(\App\Services\ThemeService::class);
            return response()->json([
                'current_theme' => $themeService->getCurrentTheme(),
                'available_themes' => $themeService->getAvailableThemes()
            ]);
        });
        
        Route::post('/set', function (Request $request) {
            $request->validate([
                'theme' => 'required|string|in:light,dark'
            ]);
            
            $themeService = app(\App\Services\ThemeService::class);
            $themeService->setCurrentTheme($request->theme);
            
            return response()->json([
                'success' => true,
                'theme' => $request->theme
            ]);
        });
    });
});

// Rate limited public routes
Route::middleware('throttle:public')->group(function () {
    // Public memory viewing (for shared memories)
    Route::get('/public/memories/{uuid}', function ($uuid) {
        // Implementation for public memory sharing
        return response()->json(['message' => 'Public memory sharing coming soon']);
    });
});