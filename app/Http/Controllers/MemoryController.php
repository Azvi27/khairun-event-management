<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;  // ← TAMBAH BARIS INI
use App\Services\SpotifyService;
use App\Services\StorageService;

class MemoryController extends Controller
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua memories milik user yang sedang login
        $memories = Memory::where('user_id', auth()->id())
                          ->orderBy('memory_date', 'asc')
                          ->get();
        
        // Kirim data memories ke view dashboard
        return view('memories.index', compact('memories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('memories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'memory_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'spotify_track_id' => 'nullable|string|max:255'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // ✅ GANTI: Gunakan StorageService
            $imagePath = $this->storageService->uploadImage(
                $request->file('image'),
                'memories',
                'memory_' . time() . '_' . uniqid()
            );

            if (!$imagePath) {
                return back()->withErrors(['image' => 'Failed to upload image.']);
            }
        }

        Memory::create([
            'user_id' => auth()->id(),
            'memory_date' => $validated['memory_date'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'spotify_track_id' => $validated['spotify_track_id'],
        ]);

        return redirect()->route('memories.index')
                        ->with('success', 'Memory created successfully! 💝');
    }

    public function update(Request $request, Memory $memory)
    {
        // Pastikan user bisa edit memory ini
        if ($memory->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'memory_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'spotify_track_id' => 'nullable|string|max:255'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($memory->image_path) {
                $this->storageService->deleteFile($memory->image_path); // ✅ GANTI
            }

            // Upload new image
            $imagePath = $this->storageService->uploadImage( // ✅ GANTI
                $request->file('image'),
                'memories',
                'memory_' . time() . '_' . uniqid()
            );

            if (!$imagePath) {
                return back()->withErrors(['image' => 'Failed to upload image.']);
            }

            $validated['image_path'] = $imagePath;
        }

        $memory->update([
            'memory_date' => $validated['memory_date'],
            'description' => $validated['description'],
            'image_path' => $validated['image_path'] ?? $memory->image_path,
            'spotify_track_id' => $validated['spotify_track_id'],
        ]);

        return redirect()->route('memories.show', $memory)
                        ->with('success', 'Memory updated successfully! ✨');
    }

    public function destroy(Memory $memory)
    {
        // Pastikan user bisa delete memory ini
        if ($memory->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete image file
        if ($memory->image_path) {
            $this->storageService->deleteFile($memory->image_path); // ✅ GANTI
        }

        $memory->delete();

        return redirect()->route('memories.index')
                        ->with('success', 'Memory deleted successfully.');
    }

    public function show($id)
    {
        $memory = \App\Models\Memory::findOrFail($id);
        return view('memories.show', compact('memory'));
    }

    public function edit($id)
    {
        $memory = \App\Models\Memory::findOrFail($id);
        return view('memories.edit', compact('memory'));
    }
}
