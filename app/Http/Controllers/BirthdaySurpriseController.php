<?php

namespace App\Http\Controllers;

use App\Models\BirthdaySurprise;
use Illuminate\Http\Request;
use App\Models\User;  // ← TAMBAH BARIS INI
use Illuminate\Support\Facades\Log;

class BirthdaySurpriseController extends Controller
{
    /**
     * Tampilkan daftar surprises untuk user yang login
     */
    public function index()
    {
        $user = auth()->user(); // Ambil user yang sedang login
        
        // Surprises yang DITERIMA user (incoming)
        $receivedSurprises = BirthdaySurprise::where('receiver_user_id', $user->id)
                                           ->with(['sender', 'receiver'])
                                           ->orderBy('reveal_at', 'asc')
                                           ->get();
        
        // Surprises yang DIKIRIM user (outgoing)  
        $sentSurprises = BirthdaySurprise::where('sender_user_id', $user->id)
                                       ->with(['sender', 'receiver'])
                                       ->orderBy('reveal_at', 'asc')
                                       ->get();
        
        // Kirim data ke view
        return view('birthday-surprises.index', compact('receivedSurprises', 'sentSurprises'));
    }

    /**
     * Tampilkan form untuk buat surprise baru
     */
    public function create()
    {
        // Ambil user yang sedang login
        $currentUser = auth()->user();
        
        // Cari user lain (karena app ini private untuk 2 orang)
        $otherUser = User::where('id', '!=', $currentUser->id)->first();
        
        // Kirim data ke view form
        return view('birthday-surprises.create', compact('otherUser'));
    }

     /**
     * Tampilkan form edit surprise (hanya untuk surprise yang belum terbuka)
     */
    public function edit(BirthdaySurprise $birthdaySurprise)
    {
        // 1. SECURITY CHECK - Hanya pengirim yang boleh edit
        if ($birthdaySurprise->sender_user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. TIMING CHECK - Tidak bisa edit jika sudah terbuka
        if ($birthdaySurprise->isRevealed()) {
            return redirect()->route('birthday-surprises.index')
                           ->with('error', 'Surprise yang sudah terbuka tidak bisa diedit! 🚫');
        }
        
        // 3. PREPARE DATA - Siapkan data untuk form
        $currentUser = auth()->user();
        $otherUser = User::where('id', '!=', $currentUser->id)->first();
        
        // 4. SHOW EDIT FORM - Kirim data ke view edit
        return view('birthday-surprises.edit', compact('birthdaySurprise', 'otherUser'));
    }

    /**
     * Simpan surprise baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_user_id' => 'required|exists:users,id|different:' . auth()->id(),
            'content_type' => 'required|in:message,image,video_link',
            'content_payload' => 'required|string|max:2000|min:1',
            'reveal_at' => 'required|date|after:now|before:' . now()->addYears(2),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:max_width=2000,max_height=2000',
        ]);

        // 2. Handle upload gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('birthday_surprises', 'public');
            } catch (\Exception $e) {
                Log::error('File upload failed', ['error' => $e->getMessage()]);
                return back()->with('error', 'Gagal mengupload gambar. Coba lagi.');
            }
        }

        // 3. Simpan ke database
        BirthdaySurprise::create([
            'sender_user_id' => auth()->id(),
            'receiver_user_id' => $validated['receiver_user_id'],
            'content_type' => $validated['content_type'],
            'content_payload' => $validated['content_payload'],
            'reveal_at' => $validated['reveal_at'],
            'content' => $imagePath,
        ]);

        return redirect()->route('birthday-surprises.index')
                        ->with('success', 'Birthday surprise berhasil dijadwalkan! 🎁✨');
    }
    
        /**
     * Update surprise yang sudah ada
     */
    public function update(Request $request, BirthdaySurprise $birthdaySurprise)
    {
        // 1. SECURITY CHECK - Hanya pengirim yang boleh update
        if ($birthdaySurprise->sender_user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. TIMING CHECK - Tidak bisa update jika sudah terbuka
        if ($birthdaySurprise->isRevealed()) {
            return redirect()->route('birthday-surprises.index')
                           ->with('error', 'Surprise yang sudah terbuka tidak bisa diedit! 🚫');
        }

        // 3. VALIDATION - Cek input dari form edit
        $validated = $request->validate([
            'content_type' => 'required|in:message,image,video_link',
            'content_payload' => 'required|string|max:2000',
            'reveal_at' => 'required|date|after:now'
        ]);

        // 4. UPDATE DATABASE - Update data surprise
        $birthdaySurprise->update([
            'content_type' => $validated['content_type'],
            'content_payload' => $validated['content_payload'],
            'reveal_at' => $validated['reveal_at']
        ]);

        // 5. SUCCESS RESPONSE - Redirect dengan pesan sukses
        return redirect()->route('birthday-surprises.index')
                        ->with('success', 'Birthday surprise berhasil diperbarui! ✨');
    }

    /**
     * Tampilkan detail surprise (dengan aturan keamanan)
     */
    public function show(BirthdaySurprise $birthdaySurprise)
    {
        $currentUserId = auth()->id();

        // ✅ AUTHORIZATION: Izinkan baik sender maupun receiver untuk melihat
        if ($birthdaySurprise->sender_user_id !== $currentUserId && 
            $birthdaySurprise->receiver_user_id !== $currentUserId) {
            abort(403, 'Unauthorized action.');
        }

        // ✅ SMART TIMING: Cek apakah user adalah receiver
        if ($birthdaySurprise->receiver_user_id === $currentUserId) {
            // Lebih aman - hanya izinkan bypass di local
            $isDevelopment = config('app.env') === 'local';
            
            // Jika bukan development, cek timing normal
            if (!$isDevelopment) {
                if (!$birthdaySurprise->canBeRevealed()) {
                    return redirect()->route('dashboard')
                                ->with('error', 'Tunggu sampai hari spesialmu tiba! 🎂');
                }
            }
            
            // Update status revealed (untuk receiver yang membuka surprise)
            if (!$birthdaySurprise->is_revealed) {
                $birthdaySurprise->update(['is_revealed' => true]);
            }
        }

        // ✅ SHOW SURPRISE: Tampilkan surprise
        return view('birthday-surprises.show', compact('birthdaySurprise'));
    }

    public function checkRevealStatus(BirthdaySurprise $birthdaySurprise)
    {
        return response()->json([
            'can_reveal' => $birthdaySurprise->canBeRevealed(),
            'days_remaining' => now()->diffInDays($birthdaySurprise->reveal_at, false)
        ]);
    }

    /**
     * Hapus surprise (hanya pengirim yang boleh)
     */
    public function destroy(BirthdaySurprise $birthdaySurprise)
    {
        // 1. SECURITY CHECK - Hanya pengirim yang boleh hapus
        if ($birthdaySurprise->sender_user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. OPTIONAL: Cek apakah sudah terlanjur terbuka
        if ($birthdaySurprise->isRevealed()) {
            return redirect()->route('birthday-surprises.index')
                           ->with('error', 'Surprise yang sudah terbuka tidak bisa dihapus! 🚫');
        }
        
        // 3. DELETE SURPRISE
        $birthdaySurprise->delete();
        
        // 4. SUCCESS RESPONSE
        return redirect()->route('birthday-surprises.index')
                        ->with('success', 'Birthday surprise berhasil dihapus! 🗑️');
    }
}