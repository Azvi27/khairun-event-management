<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BirthdaySurpriseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    $memories = \App\Models\Memory::where('user_id', auth()->id())
                                  ->orderBy('memory_date', 'desc')
                                  ->take(6) // Hanya ambil 6 memory terbaru untuk dashboard
                                  ->get();
    
    $nextSurprise = \App\Models\BirthdaySurprise::where('receiver_user_id', $user->id)
                                  ->where('reveal_at', '>', now())
                                  ->where('is_revealed', false)
                                  ->orderBy('reveal_at', 'asc')
                                  ->first();
    
    return view('dashboard', compact('memories', 'nextSurprise'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Tambah ini (Memory CRUD Routes):
Route::middleware('auth')->group(function () {
    Route::resource('memories', MemoryController::class);
});

// Birthday Surprise CRUD Routes - DENGAN THROTTLE TERINTEGRASI
Route::middleware('auth')->group(function () {
    // Resource routes dengan throttle khusus untuk store
    Route::get('/birthday-surprises', [BirthdaySurpriseController::class, 'index'])->name('birthday-surprises.index');
    Route::get('/birthday-surprises/create', [BirthdaySurpriseController::class, 'create'])->name('birthday-surprises.create');
    Route::post('/birthday-surprises', [BirthdaySurpriseController::class, 'store'])
         ->middleware('throttle:10,1')
         ->name('birthday-surprises.store');
    Route::get('/birthday-surprises/{birthdaySurprise}', [BirthdaySurpriseController::class, 'show'])->name('birthday-surprises.show');
    Route::get('/birthday-surprises/{birthdaySurprise}/edit', [BirthdaySurpriseController::class, 'edit'])->name('birthday-surprises.edit');
    Route::put('/birthday-surprises/{birthdaySurprise}', [BirthdaySurpriseController::class, 'update'])->name('birthday-surprises.update');
    Route::delete('/birthday-surprises/{birthdaySurprise}', [BirthdaySurpriseController::class, 'destroy'])->name('birthday-surprises.destroy');
    
    // Additional route
    Route::get('/birthday-surprises/{birthdaySurprise}/check-status', [BirthdaySurpriseController::class, 'checkRevealStatus'])
         ->name('birthday-surprises.check-status');
});

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Profile partials routes
    Route::prefix('profile/settings')->group(function () {
        Route::get('/information', function() {
            return view('profile.partials.update-profile-information-form');
        })->name('profile.partials.information');
        
        Route::get('/password', function() {
            return view('profile.partials.update-password-form');
        })->name('profile.partials.password');
        
        Route::get('/delete', function() {
            return view('profile.partials.delete-user-form');
        })->name('profile.partials.delete');
    });
});

// Event CRUD Routes  
Route::middleware('auth')->group(function () {
    Route::get('calendar', [EventController::class, 'calendar'])->name('calendar');

    Route::get('/events', function() {
        return redirect()->route('calendar');
    })->name('events.index');
    
    Route::get('/events/create', function() {
        return redirect()->route('calendar');
    })->name('events.create');

    Route::resource('events', EventController::class)->except(['index', 'create']);
});

// Music Routes
Route::middleware('auth')->group(function () {
    Route::get('/music', [App\Http\Controllers\MusicController::class, 'index'])->name('music.index');
    Route::get('/music/search', [App\Http\Controllers\MusicController::class, 'search'])->name('music.search');
    Route::get('/music/track/{trackId}', [App\Http\Controllers\MusicController::class, 'track'])->name('music.track');
    
    // Music Playlist & Playback Routes
    Route::prefix('music')->name('music.')->group(function () {
        Route::get('/playlists', [App\Http\Controllers\MusicController::class, 'playlists'])->name('playlists');
        Route::get('/playlist/{playlistId}/tracks', [App\Http\Controllers\MusicController::class, 'playlistTracks'])->name('playlist.tracks');
        Route::post('/play', [App\Http\Controllers\MusicController::class, 'play'])->name('play');
        Route::post('/pause', [App\Http\Controllers\MusicController::class, 'pause'])->name('pause');
        Route::post('/next', [App\Http\Controllers\MusicController::class, 'next'])->name('next');
        Route::post('/previous', [App\Http\Controllers\MusicController::class, 'previous'])->name('previous');
        Route::get('/current-playback', [App\Http\Controllers\MusicController::class, 'currentPlayback'])->name('current-playback');
        Route::post('/transfer-playback', [App\Http\Controllers\MusicController::class, 'transferPlayback'])->name('transfer-playback');
    });
    
    // Spotify Authentication Routes (kecuali callback)
    Route::prefix('spotify')->name('spotify.')->group(function () {
        Route::get('/connect', [App\Http\Controllers\SpotifyAuthController::class, 'redirectToSpotify'])->name('connect');
        Route::post('/disconnect', [App\Http\Controllers\SpotifyAuthController::class, 'disconnect'])->name('disconnect');
        Route::get('/status', [App\Http\Controllers\SpotifyAuthController::class, 'status'])->name('status');
        Route::post('/refresh-token', [App\Http\Controllers\SpotifyAuthController::class, 'refreshToken'])->name('refresh-token');
    });
});

// Spotify callback route (di luar middleware auth)
Route::get('/spotify/callback', [App\Http\Controllers\SpotifyAuthController::class, 'handleCallback'])->name('spotify.callback');
require __DIR__.'/auth.php';

// OTP Demo Route (untuk testing enhanced OTP page)
Route::get('/otp-demo', function () {
    return view('auth.verify-otp-enhanced');
})->name('otp.demo');

// Hanya untuk development
if (config('app.env') === 'local') {
    
    // 📧 TEST EMAIL NOTIFICATIONS
    Route::get('/test-email-notification', function () {
        // Find a sample surprise to test with
        $surprise = \App\Models\BirthdaySurprise::with(['sender', 'receiver'])->first();
        
        if (!$surprise) {
            return response()->json([
                'error' => 'No birthday surprises found. Create one first!',
                'suggestion' => 'Create a surprise at /birthday-surprises/create'
            ]);
        }
        
        try {
            // Test dispatch email job
            \App\Jobs\SendSurpriseNotification::dispatch($surprise);
            
            // Also test the mailable directly
            $mailable = new \App\Mail\SurpriseRevealedMail($surprise);
            \Illuminate\Support\Facades\Mail::send($mailable);
            
            return response()->json([
                'success' => 'Email notification test completed!',
                'surprise_id' => $surprise->id,
                'receiver' => $surprise->receiver->name,
                'sender' => $surprise->sender->name,
                'mail_driver' => config('mail.default'),
                'note' => 'Check storage/logs/laravel.log for email content (log driver)'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Email test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->middleware('auth')->name('test.email');

    Route::get('/test-spotify', function () {
        // 1. BUAT INSTANCE SpotifyService
        $spotifyService = new \App\Services\SpotifyService();
        
        try {
            // 2. TEST SEARCH LAGU
            // Ini akan cari lagu "Perfect" dari Ed Sheeran, maksimal 5 hasil
            $tracks = $spotifyService->searchTracks('Perfect Ed Sheeran', 5);
            
            // 3. KEMBALIKAN HASIL DALAM FORMAT JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Spotify API berhasil bekerja! 🎉',
                'total_tracks_found' => count($tracks),
                'search_query' => 'Perfect Ed Sheeran',
                'tracks' => $tracks
            ]);
            
        } catch (\Exception $e) {
            // 4. JIKA ADA ERROR, TAMPILKAN PESAN ERROR
                return response()->json([
                'status' => 'error',
                'message' => 'Spotify API gagal: ' . $e->getMessage(),
                'possible_causes' => [
                    'Credentials salah di .env',
                    'Koneksi internet bermasalah',
                    'Spotify API sedang down'
                ]
            ]);
        }
    });

    // routes/web.php - Tambahkan route test
    Route::get('/test-storage', function() {
        $storageService = app('App\Services\StorageService');
        return response()->json($storageService->getDiskInfo());
    });
    
    // Spotify Debug Routes
    Route::prefix('debug/spotify')->name('debug.spotify.')->group(function () {
        Route::get('/', [App\Http\Controllers\SpotifyDebugController::class, 'debug'])->name('info');
        Route::get('/test-playback', [App\Http\Controllers\SpotifyDebugController::class, 'testPlayback'])->name('test-playback');
    });
}

// TAMBAHKAN INI DI ROUTES/WEB.PHP
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// ========================================
// 🏥 PRODUCTION HEALTH CHECK ENDPOINTS
// ========================================

// Health check endpoint untuk production monitoring
Route::get('/health', function () {
    if (!config('production.monitoring.health_check_enabled', true)) {
        abort(404);
    }
    
    $result = [];
    $healthy = true;
    
    // Database check
    try {
        \DB::connection()->getPdo();
        \App\Models\User::count(); // Test actual query
        $result['database'] = 'healthy';
    } catch (\Exception $e) {
        $result['database'] = 'failed';
        $healthy = false;
    }
    
    // Storage check  
    try {
        \Storage::disk()->exists('test') || \Storage::disk()->put('health-check.txt', 'test');
        \Storage::disk()->delete('health-check.txt');
        $result['storage'] = 'healthy';
    } catch (\Exception $e) {
        $result['storage'] = 'failed';
        $healthy = false;
    }
    
    // Cache check
    try {
        \Cache::put('health-check', 'test', 60);
        $value = \Cache::get('health-check');
        \Cache::forget('health-check');
        $result['cache'] = $value === 'test' ? 'healthy' : 'failed';
    } catch (\Exception $e) {
        $result['cache'] = 'failed';
        $healthy = false;
    }
    
    // Application stats
    $result['stats'] = [
        'users_count' => \App\Models\User::count(),
        'memories_count' => \App\Models\Memory::count(),
        'uptime' => 'active',
    ];
    
    $result['status'] = $healthy ? 'healthy' : 'failed';
    $result['timestamp'] = now()->toISOString();
    $result['version'] = config('app.version', '1.0.0');
    $result['environment'] = app()->environment();
    
    return response()->json($result, $healthy ? 200 : 503);
})->name('health-check');

// Status endpoint (simplified)
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'environment' => app()->environment(),
        'maintenance' => app()->isDownForMaintenance(),
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
    ]);
})->name('status');

// API status untuk monitoring tools
Route::get('/api/health', function () {
    return response()->json([
        'ok' => true,
        'service' => 'khairun',
        'timestamp' => now()->timestamp
    ]);
})->name('api.health');