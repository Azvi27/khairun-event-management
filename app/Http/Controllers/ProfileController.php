<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\StorageService;

class ProfileController extends Controller
{
    protected $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $memoriesCount = \App\Models\Memory::where('user_id', auth()->id())->count();
        $eventsCount = \App\Models\Event::whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        })->count();
        
        return view('profile.edit', [
            'user' => $request->user(),
            'memoriesCount' => $memoriesCount,
            'eventsCount' => $eventsCount
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                $this->storageService->deleteFile($user->profile_photo_path);
            }
            
            // Upload new photo
            $photoPath = $this->storageService->uploadImage(
                $request->file('profile_photo'),
                'profiles',
                'profile_' . $user->id . '_' . time()
            );

            if ($photoPath) {
                $user->profile_photo_path = $photoPath;
            }
        }
        
        // Update other fields
        $user->fill($request->safe()->except(['profile_photo']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile photo if exists
        if ($user->profile_photo_path) {
            $this->storageService->deleteFile($user->profile_photo_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function index(Request $request)
    {
        $memoriesCount = \App\Models\Memory::where('user_id', auth()->id())->count();
        $eventsCount = \App\Models\Event::whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        })->count();
        
        return view('profile.index', [
            'user' => $request->user(),
            'memoriesCount' => $memoriesCount,
            'eventsCount' => $eventsCount
        ]);
    }
}
