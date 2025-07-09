<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\BirthdaySurprise;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get recent memories for gallery
        $memories = Memory::where('user_id', auth()->id())
                         ->orderBy('memory_date', 'desc')
                         ->take(6)
                         ->get();
        
        // Get active birthday surprise for countdown
        $nextSurprise = BirthdaySurprise::where('user_id', auth()->id())
                                          ->where('is_revealed', false)
                                          ->where('reveal_date', '>', now())
                                          ->first();
        
        return view('dashboard', compact('memories', 'nextSurprise'));
    }
} 