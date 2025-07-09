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
});

require __DIR__.'/auth.php';

// Hanya untuk development
if (config('app.env') === 'local') {
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
}

// TAMBAHKAN INI DI ROUTES/WEB.PHP
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});