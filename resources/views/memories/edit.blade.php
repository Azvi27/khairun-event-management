@extends('layouts.khairun')

@section('title', 'Edit Memory - Our Memories')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/memories.css') }}">
<style>
    .edit-memory-container {
        max-width: 800px;
        margin: 3vh auto;
        padding: 0 2vw;
    }
    
    .edit-form-section {
        background: #343646;
        border-radius: 2.3vh;
        padding: 3vh 4vw;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .current-image {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 1vh;
        margin-bottom: 1vh;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .spotify-track-display {
        background: rgba(140, 224, 255, 0.1);
        border: 1px solid #8CE0FF;
        border-radius: 1vh;
        padding: 1.5vh 2vw;
        display: flex;
        align-items: center;
        gap: 1vw;
        margin-top: 1vh;
    }
    
    .spotify-track-image {
        width: 60px;
        height: 60px;
        border-radius: 0.5vh;
    }
    
    .spotify-track-info {
        flex: 1;
    }
    
    .spotify-track-name {
        font-weight: 600;
        color: #D3D3D9;
        margin-bottom: 0.3vh;
    }
    
    .spotify-track-artist {
        color: #8F8C8C;
        font-size: 0.9em;
    }
    
    .remove-track-btn {
        background: #FF6B6B;
        color: white;
        border: none;
        padding: 0.5vh 1vw;
        border-radius: 0.5vh;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .remove-track-btn:hover {
        background: #FF5252;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="edit-memory-container">
    <h1 class="page-title">Edit Memory</h1>
    <p class="page-subtitle">Update your precious moment</p>
    
    <div class="edit-form-section">
        <form action="{{ route('memories.update', $memory->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
            <div class="form-grid">
                <!-- Date Field -->
                <div class="form-group">
                    <label class="form-label">📅 Memory Date</label>
                            <input 
                                type="date" 
                                name="memory_date" 
                                value="{{ old('memory_date', $memory->memory_date->format('Y-m-d')) }}"
                        class="form-input"
                                required
                            >
                            @error('memory_date')
                        <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                <!-- Spotify Track -->
                <div class="form-group">
                    <label class="form-label">🎵 Background Music (Optional)</label>
                    <div x-data="spotifySearch()" x-init="
                            @if($memory->spotify_track_id)
                            selectedTrackId = '{{ $memory->spotify_track_id }}';
                                // Load existing track data
                                fetch('/api/spotify/track/{{ $memory->spotify_track_id }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            selectedTrack = data.track;
                                        }
                                    });
                            @endif
                        ">
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    @input.debounce.500ms="search"
                            placeholder="Search for a song..."
                            class="form-input"
                                    :disabled="loading"
                                >
                        
                        <!-- Search Results Dropdown -->
                        <div x-show="showResults && tracks.length > 0" 
                             class="spotify-search-results">
                                <template x-for="track in tracks" :key="track.id">
                                <div @click="selectTrack(track)" class="spotify-result-item">
                                    <img :src="track.image" :alt="track.album" class="result-image">
                                    <div class="result-info">
                                        <div class="result-name" x-text="track.name"></div>
                                        <div class="result-artist" x-text="track.artist"></div>
                                    </div>
                                    </div>
                                </template>
                            </div>
                            
                        <!-- Selected Track Display -->
                        <div x-show="selectedTrack" class="spotify-track-display">
                            <img :src="selectedTrack.image" class="spotify-track-image">
                            <div class="spotify-track-info">
                                <div class="spotify-track-name" x-text="selectedTrack.name"></div>
                                <div class="spotify-track-artist" x-text="selectedTrack.artist"></div>
                            </div>
                            <button type="button" @click="removeTrack" class="remove-track-btn">
                                Remove
                            </button>
                        </div>

                        <input type="hidden" name="spotify_track_id" x-model="selectedTrackId">
                    </div>
                </div>
                
                <!-- Description -->
                <div class="form-group full-width">
                    <label class="form-label">📝 Tell Your Story</label>
                    <textarea 
                        name="description" 
                        class="form-textarea"
                        placeholder="What made this moment special?"
                        required
                    >{{ old('description', $memory->description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Image Upload -->
                <div class="form-group full-width">
                    <label class="form-label">📸 Update Photo (Optional)</label>
                            
                            @if($memory->image_path)
                        <div style="margin-bottom: 1.5vh;">
                            <p style="color: #8F8C8C; margin-bottom: 1vh;">Current photo:</p>
                            <img src="{{ Storage::url($memory->image_path) }}" alt="Current memory" class="current-image">
                                </div>
                            @endif
                            
                    <div class="form-upload">
                        <input type="file" name="image" accept="image/*" id="imageUpload">
                        <label for="imageUpload">
                            <span>📷 Choose new photo</span>
                            <small>Leave empty to keep current photo</small>
                        </label>
                    </div>
                            @error('image')
                        <span class="error-message">{{ $message }}</span>
                            @enderror
                </div>
                        </div>

            <!-- Action Buttons -->
            <div class="form-actions" style="margin-top: 3vh; display: flex; gap: 1vw; justify-content: flex-end;">
                <a href="{{ route('memories.index') }}" class="cancel-btn">
                    Cancel
                </a>
                <button type="submit" class="add-memory-btn">
                    ✨ Update Memory
                            </button>
                        </div>
                    </form>
                </div>
            </div>

<!-- Spotify Search Script -->
<script>
function spotifySearch() {
    return {
        searchQuery: '',
        tracks: [],
        selectedTrack: null,
        selectedTrackId: '',
        loading: false,
        showResults: false,
        
        async search() {
            if (this.searchQuery.length < 2) {
                this.tracks = [];
                this.showResults = false;
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch(`/api/spotify/search?q=${encodeURIComponent(this.searchQuery)}&limit=5`);
                const data = await response.json();
                
                if (data.success) {
                    this.tracks = data.tracks;
                    this.showResults = true;
                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        },
        
        selectTrack(track) {
            this.selectedTrack = track;
            this.selectedTrackId = track.id;
            this.showResults = false;
            this.searchQuery = '';
        },
        
        removeTrack() {
            this.selectedTrack = null;
            this.selectedTrackId = '';
        }
    }
}
</script>
@endsection